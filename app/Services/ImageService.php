<?php

namespace App\Services;

use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\UploadedFile;

class ImageService
{
    protected ImageManager $manager;

    public function __construct()
    {
        $this->manager = new ImageManager(new Driver());
    }

    /**
     * Optimize and convert image to WebP format
     *
     * @param UploadedFile $file
     * @param string $directory
     * @param array $sizes Thumbnail sizes to generate
     * @return array Paths to generated images
     */
    public function optimizeAndStore(UploadedFile $file, string $directory = 'images', array $sizes = []): array
    {
        $filename = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
        $timestamp = time();
        $results = [];

        // Read the image
        $image = $this->manager->read($file->getPathname());

        // Store original (optimized)
        $originalPath = "{$directory}/{$filename}_{$timestamp}.webp";
        $this->storeWebP($image, $originalPath, 85);
        $results['original'] = $originalPath;

        // Generate thumbnails
        foreach ($sizes as $sizeName => $dimensions) {
            $width = $dimensions['width'] ?? null;
            $height = $dimensions['height'] ?? null;

            $thumbnail = clone $image;
            
            if ($width && $height) {
                $thumbnail->cover($width, $height);
            } elseif ($width) {
                $thumbnail->scale(width: $width);
            } elseif ($height) {
                $thumbnail->scale(height: $height);
            }

            $thumbPath = "{$directory}/{$filename}_{$timestamp}_{$sizeName}.webp";
            $this->storeWebP($thumbnail, $thumbPath, 80);
            $results[$sizeName] = $thumbPath;
        }

        return $results;
    }

    /**
     * Convert and store image as WebP
     */
    protected function storeWebP($image, string $path, int $quality = 85): void
    {
        $encoded = $image->toWebp($quality);
        Storage::disk('public')->put($path, (string) $encoded);
    }

    /**
     * Generate responsive srcset for an image
     *
     * @param string $imagePath
     * @param array $widths
     * @return array
     */
    public function generateResponsiveSizes(string $imagePath, array $widths = [320, 640, 768, 1024, 1280, 1920]): array
    {
        if (!Storage::disk('public')->exists($imagePath)) {
            return [];
        }

        $image = $this->manager->read(Storage::disk('public')->path($imagePath));
        $pathInfo = pathinfo($imagePath);
        $directory = $pathInfo['dirname'];
        $filename = $pathInfo['filename'];
        $results = [];

        foreach ($widths as $width) {
            $resized = clone $image;
            $resized->scale(width: $width);
            
            $resizedPath = "{$directory}/{$filename}_{$width}w.webp";
            $this->storeWebP($resized, $resizedPath, 85);
            
            $results[] = [
                'path' => $resizedPath,
                'url' => Storage::disk('public')->url($resizedPath),
                'width' => $width,
            ];
        }

        return $results;
    }

    /**
     * Create thumbnail with specific dimensions
     *
     * @param string $imagePath
     * @param int $width
     * @param int $height
     * @param string $fit Method: 'cover', 'contain', 'fill'
     * @return string|null
     */
    public function createThumbnail(string $imagePath, int $width, int $height, string $fit = 'cover'): ?string
    {
        if (!Storage::disk('public')->exists($imagePath)) {
            return null;
        }

        $image = $this->manager->read(Storage::disk('public')->path($imagePath));
        $pathInfo = pathinfo($imagePath);
        $directory = $pathInfo['dirname'];
        $filename = $pathInfo['filename'];

        switch ($fit) {
            case 'cover':
                $image->cover($width, $height);
                break;
            case 'contain':
                $image->contain($width, $height);
                break;
            case 'fill':
                $image->resize($width, $height);
                break;
        }

        $thumbnailPath = "{$directory}/{$filename}_thumb_{$width}x{$height}.webp";
        $this->storeWebP($image, $thumbnailPath, 80);

        return $thumbnailPath;
    }

    /**
     * Optimize existing image without changing format
     *
     * @param string $imagePath
     * @param int $quality
     * @return bool
     */
    public function optimizeExisting(string $imagePath, int $quality = 85): bool
    {
        if (!Storage::disk('public')->exists($imagePath)) {
            return false;
        }

        $image = $this->manager->read(Storage::disk('public')->path($imagePath));
        $this->storeWebP($image, $imagePath, $quality);

        return true;
    }

    /**
     * Get image dimensions
     *
     * @param string $imagePath
     * @return array|null
     */
    public function getImageDimensions(string $imagePath): ?array
    {
        if (!Storage::disk('public')->exists($imagePath)) {
            return null;
        }

        $image = $this->manager->read(Storage::disk('public')->path($imagePath));

        return [
            'width' => $image->width(),
            'height' => $image->height(),
        ];
    }

    /**
     * Delete image and all its variants
     *
     * @param string $imagePath
     * @return bool
     */
    public function deleteWithVariants(string $imagePath): bool
    {
        $pathInfo = pathinfo($imagePath);
        $directory = $pathInfo['dirname'];
        $filename = $pathInfo['filename'];

        // Delete original
        Storage::disk('public')->delete($imagePath);

        // Delete all variants (thumbnails, responsive sizes)
        $pattern = "{$directory}/{$filename}_*";
        $variants = Storage::disk('public')->files($directory);
        
        foreach ($variants as $variant) {
            if (str_starts_with(basename($variant), $filename . '_')) {
                Storage::disk('public')->delete($variant);
            }
        }

        return true;
    }

    /**
     * Batch optimize images in a directory
     *
     * @param string $directory
     * @param int $quality
     * @return int Number of images optimized
     */
    public function batchOptimize(string $directory, int $quality = 85): int
    {
        $files = Storage::disk('public')->files($directory);
        $count = 0;

        foreach ($files as $file) {
            $extension = strtolower(pathinfo($file, PATHINFO_EXTENSION));
            
            if (in_array($extension, ['jpg', 'jpeg', 'png', 'gif', 'webp'])) {
                $this->optimizeExisting($file, $quality);
                $count++;
            }
        }

        return $count;
    }
}
