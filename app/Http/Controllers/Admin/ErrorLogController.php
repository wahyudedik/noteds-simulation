<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class ErrorLogController extends Controller
{
    /**
     * Display the error logs index page.
     */
    public function index(Request $request): View
    {
        $entries = $this->parseLogEntries($request);
        $stats = $this->getLogStats();

        return view('admin.logs.index', [
            'entries' => $entries,
            'stats' => $stats,
            'currentLevel' => $request->get('level', ''),
            'search' => $request->get('search', ''),
        ]);
    }

    /**
     * Display a single log entry detail.
     */
    public function show(Request $request, int $id): View
    {
        $entries = $this->parseLogEntries($request);
        $entry = $entries[$id] ?? null;

        if (! $entry) {
            abort(404, 'Log entry tidak ditemukan.');
        }

        $rawFormatted = $this->formatEntryForAI($entry);

        return view('admin.logs.show', [
            'entry' => $entry,
            'entryId' => $id,
            'rawFormatted' => $rawFormatted,
        ]);
    }

    /**
     * Clear the log file (superadmin only).
     */
    public function clear(): RedirectResponse
    {
        if (Auth::user()->role !== 'superadmin') {
            abort(403, 'Hanya superadmin yang dapat menghapus log.');
        }

        $logPath = storage_path('logs/laravel.log');

        if (file_exists($logPath)) {
            file_put_contents($logPath, '');
        }

        return back()->with('status', 'Log berhasil dibersihkan.');
    }

    /**
     * Download the log file.
     */
    public function download()
    {
        $logPath = storage_path('logs/laravel.log');

        if (! file_exists($logPath) || filesize($logPath) === 0) {
            return back()->withErrors(['download' => 'File log kosong atau tidak ditemukan.']);
        }

        return response()->download($logPath, 'laravel-log-'.Carbon::now()->format('Y-m-d-His').'.log');
    }

    /**
     * Parse log entries from the Laravel log file.
     *
     * @return array<int, array{id: int, timestamp: string, level: string, channel: string, message: string, context: string, stackTrace: string, url: string|null, ip: string|null, user: string|null}>
     */
    private function parseLogEntries(Request $request): array
    {
        $logPath = storage_path('logs/laravel.log');

        if (! file_exists($logPath)) {
            return [];
        }

        $content = file_get_contents($logPath);

        if ($content === false || $content === '') {
            return [];
        }

        // Split log by the Laravel log entry pattern
        // Format: [2024-01-15 10:30:45] production.ERROR: message
        $pattern = '/^\[(\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2})\]\s+(\w+)\.(\w+)\:\s(.+)$/m';

        preg_match_all($pattern, $content, $matches, PREG_SET_ORDER | PREG_OFFSET_CAPTURE);

        $entries = [];
        $id = 0;

        foreach ($matches as $index => $match) {
            $timestamp = $match[1][0];
            $channel = $match[2][0];
            $level = $match[3][0];
            $messageStart = $match[4][0];
            $offset = $match[0][1];
            $matchLength = strlen($match[0][0]);

            // Extract stack trace and context from between this entry and the next
            $nextOffset = isset($matches[$index + 1]) ? $matches[$index + 1][0][1] : strlen($content);
            $remainder = substr($content, $offset + $matchLength, $nextOffset - $offset - $matchLength);

            $parsed = $this->parseEntryBody($messageStart, $remainder);

            $entry = [
                'id' => $id,
                'timestamp' => $timestamp,
                'level' => strtolower($level),
                'channel' => $channel,
                'message' => $parsed['message'],
                'context' => $parsed['context'],
                'stackTrace' => $parsed['stackTrace'],
            ];

            $id++;

            // Apply level filter
            $levelFilter = $request->get('level');
            if ($levelFilter && strtolower($level) !== strtolower($levelFilter)) {
                continue;
            }

            // Apply search filter
            $search = $request->get('search');
            if ($search) {
                $searchLower = strtolower($search);
                if (
                    str_contains(strtolower($entry['message']), $searchLower) === false
                    && str_contains(strtolower($entry['context']), $searchLower) === false
                    && str_contains(strtolower($entry['stackTrace']), $searchLower) === false
                ) {
                    continue;
                }
            }

            $entries[] = $entry;
        }

        // Sort by timestamp descending (newest first)
        usort($entries, function ($a, $b) {
            return strcmp($b['timestamp'], $a['timestamp']);
        });

        // Limit to 500 entries for performance
        $entries = array_slice($entries, 0, 500);

        return $entries;
    }

    /**
     * Parse the body of a log entry (message, context, stack trace).
     *
     * @return array{message: string, context: string, stackTrace: string}
     */
    private function parseEntryBody(string $messageStart, string $remainder): array
    {
        $message = $messageStart;
        $context = '';
        $stackTrace = '';

        // Try to extract JSON context from the message
        $jsonStart = strrpos($messageStart, ' {');
        if ($jsonStart !== false) {
            $potentialJson = substr($messageStart, $jsonStart + 1);
            $decoded = json_decode(trim($potentialJson), true);
            if (json_last_error() === JSON_ERROR_NONE) {
                $message = trim(substr($messageStart, 0, $jsonStart));
                $context = $this->prettyPrintJson(trim($potentialJson));
            }
        }

        // Parse remainder for stack trace and additional context
        $remainder = ltrim($remainder);

        if ($remainder !== '') {
            // Check if remainder contains a stack trace
            if (preg_match('/^Stack trace:\n(.*)/s', $remainder, $traceMatch)) {
                $stackTrace = trim($traceMatch[1]);
                // Check for additional context after stack trace
                $afterTrace = trim(substr($remainder, strlen($traceMatch[0])));
                if ($afterTrace !== '') {
                    $context .= ($context ? "\n\n" : '').$afterTrace;
                }
            } elseif (str_starts_with($remainder, '#0 ') || str_starts_with($remainder, '#1 ')) {
                // Stack trace without "Stack trace:" prefix
                $lines = explode("\n", $remainder);
                $traceLines = [];
                $contextLines = [];
                $inTrace = true;

                foreach ($lines as $line) {
                    if ($inTrace && preg_match('/^#\d+\s/', $line)) {
                        $traceLines[] = $line;
                    } else {
                        $inTrace = false;
                        $contextLines[] = $line;
                    }
                }

                $stackTrace = trim(implode("\n", $traceLines));
                $contextRemainder = trim(implode("\n", $contextLines));

                if ($contextRemainder !== '') {
                    $context .= ($context ? "\n\n" : '').$contextRemainder;
                }
            } else {
                $context .= ($context ? "\n\n" : '').$remainder;
            }
        }

        return [
            'message' => $message,
            'context' => trim($context),
            'stackTrace' => trim($stackTrace),
        ];
    }

    /**
     * Pretty print JSON string.
     */
    private function prettyPrintJson(string $json): string
    {
        $decoded = json_decode($json, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            return $json;
        }

        $pretty = json_encode($decoded, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

        return $pretty !== false ? $pretty : $json;
    }

    /**
     * Get log statistics.
     *
     * @return array{total: int, error: int, critical: int, warning: int, info: int, debug: int, notice: int, alert: int, emergency: int}
     */
    private function getLogStats(): array
    {
        $logPath = storage_path('logs/laravel.log');

        $stats = [
            'total' => 0,
            'error' => 0,
            'critical' => 0,
            'warning' => 0,
            'info' => 0,
            'debug' => 0,
            'notice' => 0,
            'alert' => 0,
            'emergency' => 0,
        ];

        if (! file_exists($logPath)) {
            return $stats;
        }

        // Count entries by level using regex
        $content = file_get_contents($logPath);

        if ($content === false) {
            return $stats;
        }

        preg_match_all('/^\[\d{4}-\d{2}-\d{2}\s\d{2}:\d{2}:\d{2}\]\s+\w+\.(\w+)\:/m', $content, $levelMatches);

        foreach ($levelMatches[1] as $level) {
            $levelLower = strtolower($level);
            $stats['total']++;

            if (isset($stats[$levelLower])) {
                $stats[$levelLower]++;
            }
        }

        return $stats;
    }

    /**
     * Format a log entry as AI-friendly text.
     */
    private function formatEntryForAI(array $entry): string
    {
        $text = "=== APPLICATION ERROR LOG ===\n";
        $text .= "Timestamp: {$entry['timestamp']}\n";
        $text .= 'Level: '.strtoupper($entry['level'])."\n";
        $text .= "Channel: {$entry['channel']}\n";
        $text .= "\n--- Error Message ---\n";
        $text .= "{$entry['message']}\n";

        if ($entry['context']) {
            $text .= "\n--- Context ---\n";
            $text .= "{$entry['context']}\n";
        }

        if ($entry['stackTrace']) {
            $text .= "\n--- Stack Trace ---\n";
            $text .= "{$entry['stackTrace']}\n";
        }

        $text .= "\n===============================\n";

        return $text;
    }
}
