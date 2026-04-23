<div class="grid gap-6 md:grid-cols-2">
    <div class="field">
        <label for="image">Banner Image (English)</label>
        <input type="file" name="image" id="image" accept="image/*" @if(!isset($banner)) required @endif>
        @if(isset($banner) && $banner->image_path)
            <div class="mt-2">
                <x-admin.image :path="$banner->image_path" alt="Current image EN" class="h-24 w-auto rounded border" />
            </div>
        @endif
    </div>

    <div class="field">
        <label for="image_ar">Banner Image (Arabic - Optional)</label>
        <input type="file" name="image_ar" id="image_ar" accept="image/*">
        @if(isset($banner) && $banner->image_path_ar)
            <div class="mt-2">
                <x-admin.image :path="$banner->image_path_ar" alt="Current image AR" class="h-24 w-auto rounded border" />
            </div>
        @endif
    </div>

    <div class="field md:col-span-2">
        <p class="mt-1 text-xs text-slate-500">Images are automatically converted to WebP and optimized.</p>
        <p class="mt-1 text-xs text-slate-500">
            <strong>Middle (Carousel):</strong> 1440 x 720 (Landscape 2:1 ratio).<br>
            <strong>Side (Left/Right):</strong> 400 x 500 (Portrait 4:5 ratio). side banners are portrait and will be cropped if you use landscape images.
        </p>
    </div>
 
    <div class="field">
        <label for="title_en">Title (English)</label>
        <input type="text" name="title_en" id="title_en" value="{{ old('title_en', $banner->title_en ?? '') }}" placeholder="e.g. MEGA SALE: 50% OFF">
    </div>
 
    <div class="field">
        <label for="title_ar">Title (Arabic)</label>
        <input type="text" name="title_ar" id="title_ar" value="{{ old('title_ar', $banner->title_ar ?? '') }}" placeholder="مثال: خصومات كبرى: ٥٠٪">
    </div>
 
    <div class="field">
        <label for="description_en">Description (English)</label>
        <textarea name="description_en" id="description_en" rows="3">{{ old('description_en', $banner->description_en ?? '') }}</textarea>
    </div>
 
    <div class="field">
        <label for="description_ar">Description (Arabic)</label>
        <textarea name="description_ar" id="description_ar" rows="3" dir="rtl">{{ old('description_ar', $banner->description_ar ?? '') }}</textarea>
    </div>
 
    <div class="field">
        <label for="button_text_en">Button Text (English)</label>
        <input type="text" name="button_text_en" id="button_text_en" value="{{ old('button_text_en', $banner->button_text_en ?? '') }}" placeholder="e.g. Shop Now">
    </div>
 
    <div class="field">
        <label for="button_text_ar">Button Text (Arabic)</label>
        <input type="text" name="button_text_ar" id="button_text_ar" value="{{ old('button_text_ar', $banner->button_text_ar ?? '') }}" placeholder="مثال: تسوق الآن">
    </div>
 
    <div class="field">
        <label for="link">Link (URL)</label>
        <input type="text" name="link" id="link" value="{{ old('link', $banner->link ?? '') }}" placeholder="/category/digital-cards">
    </div>
 
    <div class="field">
        <label for="sort_order">Sort Order</label>
        <input type="number" name="sort_order" id="sort_order" value="{{ old('sort_order', $banner->sort_order ?? 0) }}" min="0">
    </div>

    <div class="field">
        <label for="position">Position</label>
        <select name="position" id="position">
            <option value="middle" @selected(old('position', $banner->position ?? 'middle') == 'middle')>Middle (Carousel)</option>
            <option value="left" @selected(old('position', $banner->position ?? 'middle') == 'left')>Left Side</option>
            <option value="right" @selected(old('position', $banner->position ?? 'middle') == 'right')>Right Side</option>
        </select>
    </div>
 
    <div class="col-span-2">
        <label class="inline-flex items-center cursor-pointer">
            <input type="checkbox" name="is_active" value="1" class="sr-only peer" @checked(old('is_active', $banner->is_active ?? true))>
            <div class="w-11 h-6 bg-slate-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
            <span class="ms-3 text-sm font-medium text-slate-900">Active</span>
        </label>
    </div>
</div>
