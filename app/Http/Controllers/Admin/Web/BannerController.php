<?php
 
namespace App\Http\Controllers\Admin\Web;
 
use App\Http\Controllers\Controller;
use App\Models\Banner;
use App\Services\Media\ImageStorageService;
use Exception;
use Illuminate\Http\Request;
 
class BannerController extends Controller
{
    public function __construct(
        private readonly ImageStorageService $imageStorage,
    ) {}
 
    public function index()
    {
        $banners = Banner::orderBy('sort_order')->paginate(15);
        return view('admin.banners.index', compact('banners'));
    }
 
    public function create()
    {
        return view('admin.banners.create');
    }
 
    public function store(Request $request)
    {
        $data = $request->validate([
            'image' => ['required', 'image', 'max:5120'],
            'image_ar' => ['nullable', 'image', 'max:5120'],
            'title_en' => ['nullable', 'string', 'max:255'],
            'title_ar' => ['nullable', 'string', 'max:255'],
            'description_en' => ['nullable', 'string', 'max:500'],
            'description_ar' => ['nullable', 'string', 'max:500'],
            'link' => ['nullable', 'string', 'max:255'],
            'button_text_en' => ['nullable', 'string', 'max:50'],
            'button_text_ar' => ['nullable', 'string', 'max:50'],
            'position' => ['sometimes', 'string', 'in:middle,left,right'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'is_active' => ['sometimes', 'boolean'],
        ]);
 
        try {
            $position = $request->input('position', 'middle');
            if ($request->hasFile('image')) {
                $data['image_path'] = $this->imageStorage->storeBannerAsWebp($request->file('image'), null, $position);
            }
            if ($request->hasFile('image_ar')) {
                $data['image_path_ar'] = $this->imageStorage->storeBannerAsWebp($request->file('image_ar'), null, $position);
            }
 
            $data['is_active'] = $request->boolean('is_active', true);
 
            Banner::create($data);
 
            return redirect()->route('admin.banners.index')->with('success', 'Banner created successfully.');
        } catch (Exception $e) {
            report($e);
            return back()->withInput()->with('error', 'Failed to create banner.');
        }
    }
 
    public function edit(Banner $banner)
    {
        return view('admin.banners.edit', compact('banner'));
    }
 
    public function update(Request $request, Banner $banner)
    {
        $data = $request->validate([
            'image' => ['nullable', 'image', 'max:5120'],
            'image_ar' => ['nullable', 'image', 'max:5120'],
            'title_en' => ['nullable', 'string', 'max:255'],
            'title_ar' => ['nullable', 'string', 'max:255'],
            'description_en' => ['nullable', 'string', 'max:500'],
            'description_ar' => ['nullable', 'string', 'max:500'],
            'link' => ['nullable', 'string', 'max:255'],
            'button_text_en' => ['nullable', 'string', 'max:50'],
            'button_text_ar' => ['nullable', 'string', 'max:50'],
            'position' => ['sometimes', 'string', 'in:middle,left,right'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'is_active' => ['sometimes', 'boolean'],
        ]);
 
        try {
            $position = $request->input('position', $banner->position);
            if ($request->hasFile('image')) {
                $data['image_path'] = $this->imageStorage->storeBannerAsWebp($request->file('image'), $banner->image_path, $position);
            }
            if ($request->hasFile('image_ar')) {
                $data['image_path_ar'] = $this->imageStorage->storeBannerAsWebp($request->file('image_ar'), $banner->image_path_ar, $position);
            }
 
            $data['is_active'] = $request->boolean('is_active', $banner->is_active);
 
            $banner->update($data);
 
            return redirect()->route('admin.banners.index')->with('success', 'Banner updated successfully.');
        } catch (Exception $e) {
            report($e);
            return back()->withInput()->with('error', 'Failed to update banner.');
        }
    }
 
    public function destroy(Banner $banner)
    {
        try {
            $this->imageStorage->deleteBannerImage($banner->image_path);
            if ($banner->image_path_ar) {
                $this->imageStorage->deleteBannerImage($banner->image_path_ar);
            }
            $banner->delete();
            return redirect()->route('admin.banners.index')->with('success', 'Banner deleted successfully.');
        } catch (Exception $e) {
            report($e);
            return back()->with('error', 'Failed to delete banner.');
        }
    }
}
