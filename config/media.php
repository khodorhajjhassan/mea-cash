<?php

return [
    'disk' => env('STORAGE_DRIVER', env('FILESYSTEM_DISK', 'local')),

    'image_optimization_enabled' => env('IMAGE_OPTIMIZATION_ENABLED', true),

    'image_webp_quality' => (int) env('IMAGE_WEBP_QUALITY', 82),

    'image_max_width' => (int) env('IMAGE_MAX_WIDTH', 2000),
];
