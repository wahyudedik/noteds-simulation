<?php

namespace App\Services;

use App\Models\CodeScanLog;
use App\Models\Simulation;
use Illuminate\Support\Facades\File;

class SecurityService
{
    /**
     * Dangerous patterns to detect in simulation code (Layer 1: Auto-Scan).
     */
    private const DANGEROUS_PATTERNS = [
        'eval\s*\(' => ['severity' => 'critical', 'description' => 'eval() ditemukan — potensi eksekusi kode berbahaya'],
        'exec\s*\(' => ['severity' => 'critical', 'description' => 'exec() ditemukan — eksekusi sistem operasi'],
        'shell_exec\s*\(' => ['severity' => 'critical', 'description' => 'shell_exec() ditemukan — eksekusi shell'],
        'system\s*\(' => ['severity' => 'critical', 'description' => 'system() ditemukan — eksekusi sistem'],
        'passthru\s*\(' => ['severity' => 'high', 'description' => 'passthru() ditemukan — output sistem'],
        'proc_open\s*\(' => ['severity' => 'critical', 'description' => 'proc_open() ditemukan — akses proses'],
        'popen\s*\(' => ['severity' => 'critical', 'description' => 'popen() ditemukan — akses proses'],
        'file_get_contents\s*\(\s*["\']https?://' => ['severity' => 'medium', 'description' => 'HTTP request dari kode — potensi data exfiltration'],
        'file_put_contents' => ['severity' => 'medium', 'description' => 'file_put_contents() — potensi tulis file'],
        'XMLHttpRequest.*\.open.*POST' => ['severity' => 'medium', 'description' => 'AJAX POST request — potensi data exfiltration'],
        'fetch\s*\(.*POST' => ['severity' => 'medium', 'description' => 'Fetch POST request — potensi data exfiltration'],
        'document\.cookie' => ['severity' => 'high', 'description' => 'Akses cookie — potensi pencurian sesi'],
        'localStorage\.(set|get)Item' => ['severity' => 'low', 'description' => 'Akses localStorage — perlu verifikasi'],
        'navigator\.clipboard' => ['severity' => 'low', 'description' => 'Akses clipboard — perlu verifikasi'],
        '<script[^>]*src=["\']https?://[^"\']*["\']' => ['severity' => 'high', 'description' => 'External script — potensi XSS/kode berbahaya'],
        'innerHTML\s*=' => ['severity' => 'low', 'description' => 'innerHTML assignment — potensi XSS'],
        'document\.write\s*\(' => ['severity' => 'medium', 'description' => 'document.write() — potensi XSS'],
        'iframe.*src=["\']https?://' => ['severity' => 'medium', 'description' => 'External iframe — potensi embed berbahaya'],
        'crypto.*mining|coinhive|cryptonight' => ['severity' => 'critical', 'description' => 'Crypto mining — dilarang'],
        'adsbygoogle|googletag|adSense' => ['severity' => 'flag', 'description' => 'Iklan tanpa izin — perlu review'],
    ];

    /**
     * Run auto-scan (Layer 1) on simulation ZIP file.
     * Scans for dangerous patterns in all text files.
     */
    public function autoScan(Simulation $simulation, string $zipPath): CodeScanLog
    {
        $startTime = microtime(true);
        $findings = [];
        $result = 'pass';

        if (! file_exists($zipPath)) {
            return $this->createScanLog($simulation, 'auto_scan', 'reject', [
                ['severity' => 'critical', 'description' => 'File ZIP tidak ditemukan', 'file' => basename($zipPath)],
            ], $startTime);
        }

        $zip = new \ZipArchive;
        if ($zip->open($zipPath) !== true) {
            return $this->createScanLog($simulation, 'auto_scan', 'reject', [
                ['severity' => 'critical', 'description' => 'File ZIP corrupt atau tidak dapat dibuka', 'file' => basename($zipPath)],
            ], $startTime);
        }

        $textExtensions = ['html', 'htm', 'js', 'css', 'json', 'txt', 'php', 'py', 'svg', 'xml', 'php5'];

        for ($i = 0; $i < $zip->numFiles; $i++) {
            $filename = $zip->getNameIndex($i);
            $extension = strtolower(pathinfo($filename, PATHINFO_EXTENSION));

            if (! in_array($extension, $textExtensions)) {
                continue;
            }

            $content = $zip->getFromIndex($i);
            if ($content === false) {
                continue;
            }

            foreach (self::DANGEROUS_PATTERNS as $pattern => $info) {
                if (preg_match('~'.$pattern.'~i', $content)) {
                    $findings[] = [
                        'severity' => $info['severity'],
                        'description' => $info['description'],
                        'file' => $filename,
                        'pattern' => $pattern,
                    ];

                    if ($info['severity'] === 'critical') {
                        $result = 'reject';
                    } elseif ($info['severity'] === 'high' && $result !== 'reject') {
                        $result = 'flag';
                    } elseif ($info['severity'] === 'flag' && $result === 'pass') {
                        $result = 'flag';
                    }
                }
            }
        }

        $zip->close();

        return $this->createScanLog($simulation, 'auto_scan', $result, $findings, $startTime);
    }

    /**
     * Run sandbox test (Layer 2) — validates simulation loads in sandboxed iframe.
     * This is a structural check; actual dynamic testing happens in browser.
     */
    public function sandboxTest(Simulation $simulation, string $extractPath): CodeScanLog
    {
        $startTime = microtime(true);
        $findings = [];
        $result = 'pass';

        if (! is_dir($extractPath)) {
            return $this->createScanLog($simulation, 'sandbox_test', 'reject', [
                ['severity' => 'critical', 'description' => 'Direktori simulasi tidak ditemukan'],
            ], $startTime);
        }

        // Check for index.html
        if (! file_exists($extractPath.'/index.html') && ! file_exists($extractPath.'/index.htm')) {
            $findings[] = [
                'severity' => 'medium',
                'description' => 'File index.html tidak ditemukan — simulasi mungkin tidak berjalan',
            ];
            $result = 'flag';
        }

        // Check total size (prevent oversized uploads)
        $totalSize = 0;
        $iterator = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($extractPath));
        foreach ($iterator as $file) {
            if ($file->isFile()) {
                $totalSize += $file->getSize();
            }
        }

        if ($totalSize > 50 * 1024 * 1024) { // 50MB limit
            $findings[] = [
                'severity' => 'high',
                'description' => 'Ukuran simulasi melebihi 50MB ('.round($totalSize / 1024 / 1024, 2).'MB)',
            ];
            $result = 'flag';
        }

        // Check for excessive file count
        $fileCount = count(glob($extractPath.'/**/*', GLOB_NOSORT));
        if ($fileCount > 500) {
            $findings[] = [
                'severity' => 'medium',
                'description' => 'Jumlah file terlalu banyak ('.$fileCount.' files) — mungkin mengandung konten berlebih',
            ];
            $result = 'flag';
        }

        return $this->createScanLog($simulation, 'sandbox_test', $result, $findings, $startTime);
    }

    /**
     * Create a scan log entry.
     */
    private function createScanLog(
        Simulation $simulation,
        string $scanType,
        string $result,
        array $findings,
        float $startTime,
    ): CodeScanLog {
        $duration = (int) ((microtime(true) - $startTime) * 1000);

        return CodeScanLog::create([
            'simulation_id' => $simulation->id,
            'version' => $simulation->current_version,
            'scan_type' => $scanType,
            'result' => $result,
            'findings' => $findings ?: null,
            'scan_duration_ms' => $duration,
        ]);
    }

    /**
     * Run manual review (Layer 3) by admin.
     */
    public function manualReview(
        Simulation $simulation,
        int $adminId,
        string $result,
        ?string $notes = null,
    ): CodeScanLog {
        $findings = [];
        if ($notes) {
            $findings[] = [
                'severity' => 'info',
                'description' => $notes,
            ];
        }

        return CodeScanLog::create([
            'simulation_id' => $simulation->id,
            'version' => $simulation->current_version,
            'scan_type' => 'manual_review',
            'result' => $result,
            'findings' => $findings ?: null,
            'scanned_by' => $adminId,
            'scan_duration_ms' => 0,
        ]);
    }
}
