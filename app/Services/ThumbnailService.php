<?php

namespace App\Services;

use App\Models\Simulation;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Log;

class ThumbnailService
{
    /**
     * Thumbnail variant definitions: name => [width, height].
     *
     * @var array<string, array{0: int, 1: int}>
     */
    private const VARIANTS = [
        'thumb' => [300, 200],
        'medium' => [600, 400],
        'large' => [1200, 800],
    ];

    /**
     * Generate all thumbnail variants from an original image path.
     *
     * @return array<string, string> Map of variant name => storage path
     */
    public function generateVariants(string $originalPath): array
    {
        $fullPath = storage_path('app/public/'.$originalPath);

        if (! file_exists($fullPath)) {
            Log::warning('ThumbnailService: original not found', ['path' => $fullPath]);

            return [];
        }

        $variants = [];
        $originalDir = pathinfo($originalPath, PATHINFO_DIRNAME);
        $originalName = pathinfo($originalPath, PATHINFO_FILENAME);
        $variantDir = ($originalDir === '.' || $originalDir === '') ? 'thumbnails' : $originalDir.'/thumbnails';

        foreach (self::VARIANTS as $name => [$targetWidth, $targetHeight]) {
            $variantFilename = $name.'_'.$originalName.'.webp';
            $variantRelativePath = $variantDir.'/'.$variantFilename;
            $variantFullPath = storage_path('app/public/'.$variantRelativePath);

            // Ensure directory exists
            if (! is_dir(dirname($variantFullPath))) {
                mkdir(dirname($variantFullPath), 0775, true);
            }

            try {
                $result = $this->resizeImage($fullPath, $variantFullPath, $targetWidth, $targetHeight);
                if ($result) {
                    $variants[$name] = $variantRelativePath;
                }
            } catch (\Throwable $e) {
                Log::error('ThumbnailService: failed to generate variant', [
                    'variant' => $name,
                    'original' => $originalPath,
                    'error' => $e->getMessage(),
                ]);
            }
        }

        return $variants;
    }

    /**
     * Resize an image and save as WebP using native GD.
     */
    private function resizeImage(string $sourcePath, string $destPath, int $targetWidth, int $targetHeight): bool
    {
        $imageInfo = @getimagesize($sourcePath);
        if ($imageInfo === false) {
            return false;
        }

        [$originalWidth, $originalHeight, $mimeType] = $imageInfo;

        // Create source image resource based on type
        $source = match ($mimeType) {
            IMAGETYPE_JPEG => @imagecreatefromjpeg($sourcePath),
            IMAGETYPE_PNG => @imagecreatefrompng($sourcePath),
            IMAGETYPE_GIF => @imagecreatefromgif($sourcePath),
            IMAGETYPE_WEBP => @imagecreatefromwebp($sourcePath),
            default => @imagecreatefromjpeg($sourcePath),
        };

        if ($source === false) {
            return false;
        }

        // Calculate crop dimensions (center crop to target aspect ratio)
        $targetRatio = $targetWidth / $targetHeight;
        $originalRatio = $originalWidth / $originalHeight;

        if ($originalRatio > $targetRatio) {
            // Image is wider than target — crop sides
            $cropHeight = $originalHeight;
            $cropWidth = (int) ($originalHeight * $targetRatio);
            $cropX = (int) (($originalWidth - $cropWidth) / 2);
            $cropY = 0;
        } else {
            // Image is taller than target — crop top/bottom
            $cropWidth = $originalWidth;
            $cropHeight = (int) ($originalWidth / $targetRatio);
            $cropX = 0;
            $cropY = (int) (($originalHeight - $cropHeight) / 2);
        }

        // Create canvas with target dimensions
        $canvas = imagecreatetruecolor($targetWidth, $targetHeight);
        if ($canvas === false) {
            imagedestroy($source);

            return false;
        }

        // Preserve transparency for PNG/WebP
        if ($mimeType === IMAGETYPE_PNG || $mimeType === IMAGETYPE_WEBP) {
            imagealphablending($canvas, false);
            imagesavealpha($canvas, true);
            $transparent = imagecolorallocatealpha($canvas, 0, 0, 0, 127);
            imagefilledrectangle($canvas, 0, 0, $targetWidth, $targetHeight, $transparent);
        }

        // Resize and crop
        $result = imagecopyresampled(
            $canvas, $source,
            0, 0,
            $cropX, $cropY,
            $targetWidth, $targetHeight,
            $cropWidth, $cropHeight
        );

        if (! $result) {
            imagedestroy($source);
            imagedestroy($canvas);

            return false;
        }

        // Save as WebP
        $saved = imagewebp($canvas, $destPath, 80);

        imagedestroy($source);
        imagedestroy($canvas);

        return $saved;
    }

    /**
     * Generate variants from an UploadedFile.
     *
     * @return array<string, string>
     */
    public function generateVariantsFromUpload(UploadedFile $file, string $subdirectory = 'thumbnails'): array
    {
        $storedPath = $file->store($subdirectory, 'public');

        return $this->generateVariants($storedPath);
    }

    /**
     * Get the best available thumbnail URL for a simulation.
     * Falls back through variants: medium → thumb → original.
     */
    public function getBestThumbnailUrl(Simulation $simulation): ?string
    {
        $variants = $simulation->thumbnail_variants;

        if (is_array($variants) && ! empty($variants['medium'])) {
            return asset('storage/'.$variants['medium']);
        }

        if (is_array($variants) && ! empty($variants['thumb'])) {
            return asset('storage/'.$variants['thumb']);
        }

        if ($simulation->thumbnail) {
            return asset('storage/'.$simulation->thumbnail);
        }

        return null;
    }

    /**
     * Get a specific variant URL for a simulation.
     */
    public function getVariantUrl(Simulation $simulation, string $variant = 'thumb'): ?string
    {
        $variants = $simulation->thumbnail_variants;

        if (is_array($variants) && ! empty($variants[$variant])) {
            return asset('storage/'.$variants[$variant]);
        }

        if ($simulation->thumbnail) {
            return asset('storage/'.$simulation->thumbnail);
        }

        return null;
    }

    /**
     * Get all variant URLs for srcset.
     *
     * @return array<string, string> Map of "variant_width" => URL
     */
    public function getSrcSetUrls(Simulation $simulation): array
    {
        $urls = [];
        $variants = $simulation->thumbnail_variants;

        if (is_array($variants)) {
            foreach (self::VARIANTS as $name => [$width]) {
                if (! empty($variants[$name])) {
                    $urls[$name.'_'.$width.'w'] = asset('storage/'.$variants[$name]);
                }
            }
        }

        // Always include original as largest
        if ($simulation->thumbnail) {
            $urls['original'] = asset('storage/'.$simulation->thumbnail);
        }

        return $urls;
    }
}
