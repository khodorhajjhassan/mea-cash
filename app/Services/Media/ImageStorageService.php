<?php

namespace App\Services\Media;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use RuntimeException;

class ImageStorageService
{
    public function storeAsWebp(UploadedFile $file, string $directory, ?string $oldPath = null): string
    {
        if (! function_exists('imagewebp') || ! function_exists('imagecreatefromstring')) {
            throw new RuntimeException('GD with WebP support is required to optimize images.');
        }

        $sourceBinary = file_get_contents($file->getRealPath());

        if ($sourceBinary === false) {
            throw new RuntimeException('Unable to read uploaded image.');
        }

        $sourceImage = @imagecreatefromstring($sourceBinary);

        if ($sourceImage === false) {
            throw new RuntimeException('Unsupported or invalid image content.');
        }

        $sourceWidth = imagesx($sourceImage);
        $sourceHeight = imagesy($sourceImage);

        [$targetWidth, $targetHeight] = $this->targetDimensions($sourceWidth, $sourceHeight);

        $canvas = imagecreatetruecolor($targetWidth, $targetHeight);
        imagealphablending($canvas, false);
        imagesavealpha($canvas, true);

        $transparent = imagecolorallocatealpha($canvas, 0, 0, 0, 127);
        imagefilledrectangle($canvas, 0, 0, $targetWidth, $targetHeight, $transparent);

        imagecopyresampled(
            $canvas,
            $sourceImage,
            0,
            0,
            0,
            0,
            $targetWidth,
            $targetHeight,
            $sourceWidth,
            $sourceHeight
        );

        ob_start();
        $quality = max(1, min(100, (int) config('media.image_webp_quality', 82)));
        imagewebp($canvas, null, $quality);
        $webpBinary = ob_get_clean();

        imagedestroy($sourceImage);
        imagedestroy($canvas);

        if (! is_string($webpBinary) || $webpBinary === '') {
            throw new RuntimeException('Failed to encode image as WebP.');
        }

        $disk = config('media.disk', config('filesystems.default'));
        $normalizedDir = trim($directory, '/');
        $path = $normalizedDir.'/'.Str::uuid()->toString().'.webp';

        Storage::disk($disk)->put($path, $webpBinary, [
            'visibility' => 'public',
            'ContentType' => 'image/webp',
            'CacheControl' => 'public, max-age=31536000, immutable',
        ]);

        if ($oldPath !== null) {
            Storage::disk($disk)->delete($oldPath);
        }

        return $path;
    }

    public function delete(?string $path): void
    {
        if ($path === null || $path === '') {
            return;
        }

        $disk = config('media.disk', config('filesystems.default'));
        Storage::disk($disk)->delete($path);
    }

    public function url(?string $path): ?string
    {
        if ($path === null || $path === '') {
            return null;
        }

        $disk = config('media.disk', config('filesystems.default'));

        return Storage::disk($disk)->url($path);
    }

    /**
     * @return array{int, int}
     */
    private function targetDimensions(int $width, int $height): array
    {
        if (! (bool) config('media.image_optimization_enabled', true)) {
            return [$width, $height];
        }

        $maxWidth = max(1, (int) config('media.image_max_width', 2000));

        if ($width <= $maxWidth) {
            return [$width, $height];
        }

        $ratio = $height / $width;

        return [$maxWidth, (int) round($maxWidth * $ratio)];
    }
}
