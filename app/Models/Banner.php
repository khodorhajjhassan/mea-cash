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
        return $this->storageUrl($this->image_path);
    }

    public function mobileImageUrl(): string
    {
        if ($this->image_path === null || $this->image_path === '' || Str::startsWith($this->image_path, ['http://', 'https://'])) {
            return $this->imageUrl();
        }

        $disk = config('media.disk', config('filesystems.default'));
        $variantPath = preg_replace('/(\.[^.]+)$/', '.mobile$1', $this->image_path) ?? $this->image_path;
        $exists = Cache::remember(
            'banner-mobile-variant:'.$disk.':'.$variantPath,
            now()->addMinutes(30),
            fn (): bool => Storage::disk($disk)->exists($variantPath)
        );

        return $exists ? $this->storageUrl($variantPath) : $this->imageUrl();
    }

    private function storageUrl(string $path): string
    {
        if (Str::startsWith($path, ['http://', 'https://'])) {
            return $path;
        }

        $disk = config('media.disk', config('filesystems.default'));

        return Storage::disk($disk)->url($path);
    }
}
