<?php

namespace App\Services\Media;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use GdImage;
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
        $webpBinary = $this->encodeResizedWebp($sourceImage, $sourceWidth, $sourceHeight, $targetWidth, $targetHeight);
        imagedestroy($sourceImage);

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

    public function storeBannerAsWebp(UploadedFile $file, ?string $oldPath = null): string
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
        $aspectRatio = $this->bannerAspectRatio();

        [$desktopWidth, $desktopHeight] = $this->bannerDimensionsForWidth(
            $sourceWidth,
            max(1, (int) config('media.banner_max_width', 1600)),
            $aspectRatio
        );
        [$mobileWidth, $mobileHeight] = $this->bannerDimensionsForWidth(
            $sourceWidth,
            max(1, (int) config('media.banner_mobile_max_width', 768)),
            $aspectRatio
        );

        $desktopBinary = $this->encodeResizedWebp($sourceImage, $sourceWidth, $sourceHeight, $desktopWidth, $desktopHeight, $aspectRatio);
        $mobileBinary = $this->encodeResizedWebp($sourceImage, $sourceWidth, $sourceHeight, $mobileWidth, $mobileHeight, $aspectRatio);
        imagedestroy($sourceImage);

        if (! is_string($desktopBinary) || $desktopBinary === '' || ! is_string($mobileBinary) || $mobileBinary === '') {
            throw new RuntimeException('Failed to encode banner image as WebP.');
        }

        $disk = config('media.disk', config('filesystems.default'));
        $basePath = 'banners/'.Str::uuid()->toString().'.webp';

        $this->putWebp($disk, $basePath, $desktopBinary);
        $this->putWebp($disk, $this->variantPath($basePath, 'mobile'), $mobileBinary);

        if ($oldPath !== null) {
            $this->deleteBannerImage($oldPath);
        }

        return $basePath;
    }

    public function ensureBannerMobileVariant(string $path): bool
    {
        if ($path === '' || Str::startsWith($path, ['http://', 'https://'])) {
            return false;
        }

        $disk = config('media.disk', config('filesystems.default'));
        $variantPath = $this->variantPath($path, 'mobile');
        if (! Storage::disk($disk)->exists($path)) {
            return false;
        }

        $sourceBinary = Storage::disk($disk)->get($path);
        $sourceImage = @imagecreatefromstring($sourceBinary);

        if ($sourceImage === false) {
            return false;
        }

        $sourceWidth = imagesx($sourceImage);
        $sourceHeight = imagesy($sourceImage);
        $aspectRatio = $this->bannerAspectRatio();
        [$mobileWidth, $mobileHeight] = $this->bannerDimensionsForWidth(
            $sourceWidth,
            max(1, (int) config('media.banner_mobile_max_width', 768)),
            $aspectRatio
        );

        $mobileBinary = $this->encodeResizedWebp($sourceImage, $sourceWidth, $sourceHeight, $mobileWidth, $mobileHeight, $aspectRatio);
        imagedestroy($sourceImage);

        if (! is_string($mobileBinary) || $mobileBinary === '') {
            return false;
        }

        $this->putWebp($disk, $variantPath, $mobileBinary);
        Cache::forget('banner-url-version:'.$disk.':'.$variantPath);

        return true;
    }

    public function deleteBannerImage(?string $path): void
    {
        $this->delete($path);

        if ($path === null || $path === '' || Str::startsWith($path, ['http://', 'https://'])) {
            return;
        }

        $disk = config('media.disk', config('filesystems.default'));
        Cache::forget('banner-url-version:'.$disk.':'.$path);
        Storage::disk($disk)->delete($this->variantPath($path, 'mobile'));
        Cache::forget('banner-url-version:'.$disk.':'.$this->variantPath($path, 'mobile'));
    }

    public function delete(?string $path): void
    {
        if ($path === null || $path === '') {
            return;
        }

        if (Str::startsWith($path, ['http://', 'https://'])) {
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

        if (Str::startsWith($path, ['http://', 'https://'])) {
            return $path;
        }

        $disk = config('media.disk', config('filesystems.default'));

        return Storage::disk($disk)->url($path);
    }

    public function variantPath(string $path, string $variant): string
    {
        return preg_replace('/(\.[^.]+)$/', sprintf('.%s$1', $variant), $path) ?? $path;
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

    /**
     * @return array{int, int}
     */
    private function dimensionsForWidth(int $width, int $height, int $maxWidth): array
    {
        if ($width <= $maxWidth) {
            return [$width, $height];
        }

        $ratio = $height / $width;

        return [$maxWidth, (int) round($maxWidth * $ratio)];
    }

    /**
     * @return array{int, int}
     */
    private function bannerDimensionsForWidth(int $width, int $maxWidth, float $aspectRatio): array
    {
        $targetWidth = min($width, $maxWidth);
        $targetHeight = (int) round($targetWidth / max($aspectRatio, 0.1));

        return [$targetWidth, max(1, $targetHeight)];
    }

    private function bannerAspectRatio(): float
    {
        $ratio = (float) config('media.banner_aspect_ratio', 2.0);

        return $ratio > 0 ? $ratio : 2.0;
    }

    private function encodeResizedWebp(
        GdImage $sourceImage,
        int $sourceWidth,
        int $sourceHeight,
        int $targetWidth,
        int $targetHeight,
        ?float $targetAspectRatio = null
    ): string
    {
        $canvas = imagecreatetruecolor($targetWidth, $targetHeight);
        imagealphablending($canvas, false);
        imagesavealpha($canvas, true);

        $transparent = imagecolorallocatealpha($canvas, 0, 0, 0, 127);
        imagefilledrectangle($canvas, 0, 0, $targetWidth, $targetHeight, $transparent);

        $sourceX = 0;
        $sourceY = 0;
        $copyWidth = $sourceWidth;
        $copyHeight = $sourceHeight;

        if ($targetAspectRatio !== null && $targetAspectRatio > 0) {
            $sourceAspectRatio = $sourceWidth / $sourceHeight;

            if ($sourceAspectRatio > $targetAspectRatio) {
                $copyWidth = max(1, (int) round($sourceHeight * $targetAspectRatio));
                $sourceX = max(0, (int) floor(($sourceWidth - $copyWidth) / 2));
            } elseif ($sourceAspectRatio < $targetAspectRatio) {
                $copyHeight = max(1, (int) round($sourceWidth / $targetAspectRatio));
                $sourceY = max(0, (int) floor(($sourceHeight - $copyHeight) / 2));
            }
        }

        imagecopyresampled(
            $canvas,
            $sourceImage,
            0,
            0,
            $sourceX,
            $sourceY,
            $targetWidth,
            $targetHeight,
            $copyWidth,
            $copyHeight
        );

        ob_start();
        $quality = max(1, min(100, (int) config('media.image_webp_quality', 82)));
        imagewebp($canvas, null, $quality);
        $webpBinary = ob_get_clean();
        imagedestroy($canvas);

        if (! is_string($webpBinary) || $webpBinary === '') {
            throw new RuntimeException('Failed to encode image as WebP.');
        }

        return $webpBinary;
    }

    private function putWebp(string $disk, string $path, string $binary): void
    {
        Storage::disk($disk)->put($path, $binary, [
            'visibility' => 'public',
            'ContentType' => 'image/webp',
            'CacheControl' => 'public, max-age=31536000, immutable',
        ]);
    }
}
