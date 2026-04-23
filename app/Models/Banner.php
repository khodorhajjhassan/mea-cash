<?php
 
namespace App\Models;
 
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
 
class Banner extends Model
{
    use HasFactory;
 
    protected $fillable = [
        'image_path',
        'title_en',
        'title_ar',
        'description_en',
        'description_ar',
        'link',
        'button_text_en',
        'button_text_ar',
        'sort_order',
        'is_active',
    ];
 
    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'sort_order' => 'integer',
        ];
    }

    public function imageUrl(): string
    {
        return $this->versionedStorageUrl($this->image_path);
    }

    public function mobileImageUrl(): string
    {
        if ($this->image_path === null || $this->image_path === '' || Str::startsWith($this->image_path, ['http://', 'https://'])) {
            return $this->versionedStorageUrl($this->image_path);
        }

        $disk = config('media.disk', config('filesystems.default'));
        $variantPath = preg_replace('/(\.[^.]+)$/', '.mobile$1', $this->image_path) ?? $this->image_path;
        $cacheKey = 'banner-mobile-variant:'.$disk.':'.$variantPath;

        if (Cache::has($cacheKey)) {
            return $this->storageUrl($variantPath);
        }

        if (! Storage::disk($disk)->exists($variantPath)) {
            return $this->imageUrl();
        }

        Cache::put($cacheKey, true, now()->addHours(6));

        return $this->versionedStorageUrl($variantPath);
    }

    private function storageUrl(string $path): string
    {
        if (Str::startsWith($path, ['http://', 'https://'])) {
            return $path;
        }

        $disk = config('media.disk', config('filesystems.default'));

        return Storage::disk($disk)->url($path);
    }

    private function versionedStorageUrl(?string $path): string
    {
        if ($path === null || $path === '') {
            return '';
        }

        $url = $this->storageUrl($path);

        if (Str::startsWith($path, ['http://', 'https://'])) {
            return $url;
        }

        $disk = config('media.disk', config('filesystems.default'));
        $cacheKey = 'banner-url-version:'.$disk.':'.$path;

        try {
            $version = Cache::remember(
                $cacheKey,
                now()->addHours(6),
                fn (): int => Storage::disk($disk)->lastModified($path)
            );
        } catch (\Throwable) {
            return $url;
        }

        return $url.(str_contains($url, '?') ? '&' : '?').'v='.$version;
    }
}
