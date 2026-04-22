<?php

use App\Models\Banner;
use App\Services\Media\ImageStorageService;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('media:generate-banner-variants', function (ImageStorageService $imageStorage) {
    $processed = 0;
    $generated = 0;

    Banner::query()->orderBy('id')->chunkById(100, function ($banners) use ($imageStorage, &$processed, &$generated): void {
        foreach ($banners as $banner) {
            $processed++;

            if ($imageStorage->ensureBannerMobileVariant($banner->image_path)) {
                $generated++;
            }
        }
    });

    $this->info("Processed {$processed} banner(s); mobile variants ready for {$generated}.");
})->purpose('Generate missing mobile WebP variants for storefront banners');
