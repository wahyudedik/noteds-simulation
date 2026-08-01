<?php

namespace App\Services\Builder;

use App\Models\ExperienceProject;
use ZipArchive;

class ExportService
{
    private ComponentRegistry $registry;

    public function __construct()
    {
        $this->registry = new ComponentRegistry;
    }

    /**
     * Export a project to ZIP file containing index.html and manifest.json.
     */
    public function export(ExperienceProject $project): string
    {
        $html = $this->renderHtml($project);
        $manifest = $this->buildManifest($project);

        $zipPath = storage_path('app/exports/'.$project->slug.'.zip');

        // Ensure exports directory exists
        $dir = dirname($zipPath);
        if (! is_dir($dir)) {
            mkdir($dir, 0755, true);
        }

        // Remove any stale file before creating a fresh ZIP
        if (file_exists($zipPath)) {
            unlink($zipPath);
        }

        $zip = new ZipArchive;
        $result = $zip->open($zipPath, ZipArchive::CREATE);

        if ($result !== true) {
            throw new \RuntimeException('Failed to create ZIP archive: error code '.$result);
        }

        if ($zip->addFromString('index.html', $html) === false) {
            $zip->close();
            unlink($zipPath);

            throw new \RuntimeException('Failed to add index.html to ZIP archive.');
        }

        if ($zip->addFromString('manifest.json', json_encode($manifest, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)) === false) {
            $zip->close();
            unlink($zipPath);

            throw new \RuntimeException('Failed to add manifest.json to ZIP archive.');
        }

        if ($zip->close() === false) {
            throw new \RuntimeException('Failed to close ZIP archive.');
        }

        if (! file_exists($zipPath)) {
            throw new \RuntimeException('ZIP archive was not created at: '.$zipPath);
        }

        return $zipPath;
    }

    /**
     * Render the full HTML page from project components.
     */
    public function renderHtml(ExperienceProject $project): string
    {
        $components = $project->getComponents();
        $body = '';

        foreach ($components as $component) {
            $rendered = $this->registry->render($component['type'], $component['properties'] ?? []);
            if ($rendered) {
                $body .= "    <section class=\"experience-component mb-6\">\n{$rendered}\n    </section>\n";
            }
        }

        $title = htmlspecialchars($project->title, ENT_QUOTES, 'UTF-8');
        $description = htmlspecialchars($project->description ?? '', ENT_QUOTES, 'UTF-8');

        return <<<HTML
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{$title}</title>
    <meta name="description" content="{$description}">
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <style>
        [x-cloak] { display: none !important; }
    </style>
</head>
<body class="bg-gray-50 min-h-screen">
    <div class="max-w-4xl mx-auto py-8 px-4">
{$body}    </div>
</body>
</html>
HTML;
    }

    /**
     * Build manifest.json for the project.
     */
    public function buildManifest(ExperienceProject $project): array
    {
        $components = $project->getComponents();
        $componentTypes = array_unique(array_column($components, 'type'));

        return [
            'name' => $project->title,
            'version' => '1.0.0',
            'description' => $project->description ?? '',
            'author' => $project->user?->username ?? 'unknown',
            'category' => $project->template?->category ?? 'general',
            'renderer' => 'html',
            'components' => $componentTypes,
            'min_platform_version' => '1.0',
            'exported_at' => now()->toIso8601String(),
        ];
    }
}
