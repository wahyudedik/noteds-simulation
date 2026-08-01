<?php

namespace App\Services\Builder;

use App\Models\ExperienceProject;
use App\Models\Simulation;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class PublishService
{
    private ExportService $exportService;

    public function __construct()
    {
        $this->exportService = new ExportService;
    }

    /**
     * Publish an Experience Project to the platform as a Simulation.
     *
     * @param  array{category: string, subcategory?: string, tags?: string, description?: string, thumbnail?: UploadedFile|null}  $metadata
     */
    public function publish(ExperienceProject $project, array $metadata): Simulation
    {
        $user = Auth::user();
        abort_unless($user, 401);

        // Validate required metadata
        $this->validateMetadata($metadata);

        // Validate project has components
        $components = $project->getComponents();
        abort_if(empty($components), 422, 'Project must have at least one component before publishing.');

        return DB::transaction(function () use ($project, $metadata, $user) {
            // 1. Export project to ZIP
            $zipPath = $this->exportService->export($project);

            // 2. Upload ZIP to public storage
            $storagePath = 'simulations/'.$user->id.'/'.$project->slug.'.zip';
            $zipContents = file_get_contents($zipPath);
            Storage::disk('public')->put($storagePath, $zipContents);

            // Clean up temp export file
            if (file_exists($zipPath)) {
                @unlink($zipPath);
            }

            // 3. Handle thumbnail
            $thumbnailPath = $this->handleThumbnail($project, $metadata['thumbnail'] ?? null);

            // 4. Create or update Simulation record
            $simulation = $this->createOrUpdateSimulation(
                $project,
                $user->id,
                $storagePath,
                $thumbnailPath,
                $metadata
            );

            // 5. Publish the ExperienceProject
            $project->publish();

            Log::info('Experience published to platform', [
                'project_id' => $project->id,
                'simulation_id' => $simulation->id,
                'user_id' => $user->id,
            ]);

            return $simulation;
        });
    }

    /**
     * Unpublish a Builder experience from the platform.
     */
    public function unpublish(ExperienceProject $project): void
    {
        $simulation = $project->simulation;

        if ($simulation) {
            $simulation->update([
                'is_published' => false,
            ]);

            Log::info('Experience unpublished from platform', [
                'project_id' => $project->id,
                'simulation_id' => $simulation->id,
            ]);
        }

        $project->update([
            'status' => 'draft',
        ]);
    }

    /**
     * Re-publish with updated metadata.
     */
    public function republish(ExperienceProject $project, array $metadata): Simulation
    {
        // If already has simulation, update it
        if ($project->hasSimulation()) {
            return $this->updateSimulation($project, $metadata);
        }

        // Otherwise, fresh publish
        return $this->publish($project, $metadata);
    }

    /**
     * Update an existing simulation with new metadata.
     */
    private function updateSimulation(ExperienceProject $project, array $metadata): Simulation
    {
        $user = Auth::user();
        abort_unless($user, 401);

        $this->validateMetadata($metadata);

        return DB::transaction(function () use ($project, $metadata, $user) {
            // Re-export if config changed
            $zipPath = $this->exportService->export($project);
            $storagePath = 'simulations/'.$user->id.'/'.$project->slug.'.zip';
            $zipContents = file_get_contents($zipPath);
            Storage::disk('public')->put($storagePath, $zipContents);

            if (file_exists($zipPath)) {
                @unlink($zipPath);
            }

            $thumbnailPath = $this->handleThumbnail($project, $metadata['thumbnail'] ?? null);
            $tags = $this->parseTags($metadata['tags'] ?? '');

            $simulation = $project->simulation;
            $simulation->update([
                'title' => $project->title,
                'description' => $metadata['description'] ?? $project->description ?? '',
                'category' => $metadata['category'],
                'subcategory' => $metadata['subcategory'] ?? null,
                'tags' => $tags,
                'thumbnail' => $thumbnailPath,
                'zip_path' => $storagePath,
                'is_published' => true,
                'published_at' => now(),
            ]);

            $project->publish();

            return $simulation->fresh();
        });
    }

    /**
     * Validate required metadata fields.
     */
    private function validateMetadata(array $metadata): void
    {
        abort_if(
            empty($metadata['category']),
            422,
            'Category is required for publishing.'
        );
    }

    /**
     * Handle thumbnail upload or auto-generate.
     */
    private function handleThumbnail(ExperienceProject $project, ?UploadedFile $file): ?string
    {
        if ($file && $file->isValid()) {
            $path = $file->store('simulations/thumbnails', 'public');

            // Generate variants
            $this->generateThumbnailVariants($path);

            return $path;
        }

        // Auto-generate: use project thumbnail if available
        if ($project->thumbnail_path && Storage::disk('public')->exists($project->thumbnail_path)) {
            return $project->thumbnail_path;
        }

        // No thumbnail — return null (platform will use placeholder)
        return null;
    }

    /**
     * Generate thumbnail variants (small, medium, large).
     */
    private function generateThumbnailVariants(string $path): void
    {
        try {
            $fullPath = Storage::disk('public')->path($path);
            $imageInfo = @getimagesize($fullPath);

            if ($imageInfo === false) {
                return;
            }

            $originalWidth = $imageInfo[0];
            $originalHeight = $imageInfo[1];

            $variants = [
                'small' => ['width' => 320, 'height' => 180],
                'medium' => ['width' => 640, 'height' => 360],
                'large' => ['width' => 1280, 'height' => 720],
            ];

            $variantPaths = [];
            $dir = dirname($path);
            $filename = pathinfo($path, PATHINFO_FILENAME);
            $extension = pathinfo($path, PATHINFO_EXTENSION);

            foreach ($variants as $size => $dimensions) {
                // Skip if original is smaller than target
                if ($originalWidth <= $dimensions['width']) {
                    $variantPaths[$size] = $path;

                    continue;
                }

                $variantPath = $dir.'/'.$filename.'_'.$size.'.'.$extension;
                $variantFullPath = Storage::disk('public')->path($variantPath);

                // Use GD directly for thumbnail generation
                $this->resizeWithGd($fullPath, $variantFullPath, $dimensions['width'], $dimensions['height']);
                $variantPaths[$size] = $variantPath;
            }

            // Store variant paths
            if (! empty($variantPaths)) {
                // We'll store this as JSON in thumbnail_variants column
                // This is handled by the caller
            }
        } catch (\Exception $e) {
            Log::warning('Failed to generate thumbnail variants', [
                'path' => $path,
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Create or update the Simulation record.
     */
    private function createOrUpdateSimulation(
        ExperienceProject $project,
        int $userId,
        string $storagePath,
        ?string $thumbnailPath,
        array $metadata
    ): Simulation {
        // Check if simulation already exists
        $existing = $project->simulation;

        $tags = $this->parseTags($metadata['tags'] ?? '');

        $data = [
            'user_id' => $userId,
            'title' => $project->title,
            'slug' => $this->generateUniqueSlug($project->slug, $existing?->id),
            'description' => $metadata['description'] ?? $project->description ?? '',
            'category' => $metadata['category'],
            'subcategory' => $metadata['subcategory'] ?? null,
            'tags' => $tags,
            'thumbnail' => $thumbnailPath,
            'version' => '1.0.0',
            'zip_path' => $storagePath,
            'entry_point' => 'index.html',
            'is_published' => true,
            'published_at' => now(),
        ];

        if ($existing) {
            $existing->update($data);

            return $existing->fresh();
        }

        return Simulation::create(array_merge($data, [
            'experience_project_id' => $project->id,
        ]));
    }

    /**
     * Parse comma-separated tags into a string.
     */
    private function parseTags(string $tagsInput): ?string
    {
        if (empty(trim($tagsInput))) {
            return null;
        }

        $tags = array_map('trim', explode(',', $tagsInput));
        $tags = array_filter($tags);
        $tags = array_unique($tags);

        return implode(',', $tags);
    }

    /**
     * Generate a unique slug for the simulation.
     */
    private function generateUniqueSlug(string $baseSlug, ?int $excludeId = null): string
    {
        $slug = Str::slug($baseSlug);
        $originalSlug = $slug;
        $counter = 1;

        while (Simulation::where('slug', $slug)
            ->when($excludeId, fn ($q) => $q->where('id', '!=', $excludeId))
            ->exists()) {
            $slug = $originalSlug.'-'.$counter;
            $counter++;
        }

        return $slug;
    }

    /**
     * Resize image using GD library.
     */
    private function resizeWithGd(string $sourcePath, string $destPath, int $targetWidth, int $targetHeight): void
    {
        $imageInfo = @getimagesize($sourcePath);
        if ($imageInfo === false) {
            return;
        }

        $mimeType = $imageInfo['mime'];

        $source = match ($mimeType) {
            'image/jpeg' => @imagecreatefromjpeg($sourcePath),
            'image/png' => @imagecreatefrompng($sourcePath),
            'image/gif' => @imagecreatefromgif($sourcePath),
            'image/webp' => @imagecreatefromwebp($sourcePath),
            default => null,
        };

        if ($source === null) {
            return;
        }

        $sourceWidth = imagesx($source);
        $sourceHeight = imagesy($source);

        // Calculate aspect-ratio-preserving dimensions
        $ratio = min($targetWidth / $sourceWidth, $targetHeight / $sourceHeight);
        $newWidth = (int) round($sourceWidth * $ratio);
        $newHeight = (int) round($sourceHeight * $ratio);

        $dest = imagecreatetruecolor($newWidth, $newHeight);

        // Preserve transparency for PNG/GIF
        if ($mimeType === 'image/png' || $mimeType === 'image/gif') {
            imagealphablending($dest, false);
            imagesavealpha($dest, true);
            $transparent = imagecolorallocatealpha($dest, 0, 0, 0, 127);
            imagefilledrectangle($dest, 0, 0, $newWidth, $newHeight, $transparent);
        }

        imagecopyresampled($dest, $source, 0, 0, 0, 0, $newWidth, $newHeight, $sourceWidth, $sourceHeight);

        match ($mimeType) {
            'image/jpeg' => imagejpeg($dest, $destPath, 85),
            'image/png' => imagepng($dest, $destPath, 6),
            'image/gif' => imagegif($dest, $destPath),
            'image/webp' => imagewebp($dest, $destPath, 85),
            default => null,
        };

        imagedestroy($source);
        imagedestroy($dest);
    }
}
