<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Banner;
use App\Services\ImageService;
use Illuminate\Http\Request;

class BannerController extends Controller
{
    protected $imageService;

    public function __construct(ImageService $imageService)
    {
        $this->imageService = $imageService;
        $this->middleware('admin');
    }

    public function index(Request $request)
    {
        $query = Banner::query();

        if ($request->filled('position')) {
            $query->where('position', $request->position);
        }

        if ($request->filled('status')) {
            $query->where('is_active', $request->status === 'active');
        }

        $banners = $query->orderBy('sort_order')->paginate(20)->appends($request->query());

        return view('backend.banners.index', compact('banners'));
    }

    public function create()
    {
        return view('backend.banners.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'link_url' => 'nullable|url|max:500',
            'position' => 'required|in:slider,top,middle,bottom,footer',
            'size' => 'required|in:small,medium,large,full',
            'height' => 'required|in:small,medium,large,xlarge,full',
            'sort_order' => 'nullable|integer|min:0',
            'is_active' => 'boolean',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after:start_date',
        ]);

        $data = $request->only(['title', 'link_url', 'position', 'size', 'height', 'sort_order', 'is_active', 'start_date', 'end_date']);
        $data['is_active'] = $request->boolean('is_active');
        $data['sort_order'] = $data['sort_order'] ?? 0;

        // Upload imagine
        if ($request->hasFile('image')) {
            try {
                $processedImages = $this->imageService->processImage($request->file('image'), 'banners', true);
                $data['image_path'] = $processedImages['main'];
            } catch (\Exception $e) {
                return back()->with('error', 'Eroare la procesarea imaginii: ' . $e->getMessage())->withInput();
            }
        }

        Banner::create($data);

        return redirect()->route('admin.banners.index')->with('success', 'Banner creat cu succes!');
    }

    public function show(Banner $banner)
    {
        return view('backend.banners.show', compact('banner'));
    }

    public function edit(Banner $banner)
    {
        return view('backend.banners.edit', compact('banner'));
    }

    public function update(Request $request, Banner $banner)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'link_url' => 'nullable|url|max:500',
            'position' => 'required|in:slider,top,middle,bottom,footer',
            'size' => 'required|in:small,medium,large,full',
            'height' => 'required|in:small,medium,large,xlarge,full',
            'sort_order' => 'nullable|integer|min:0',
            'is_active' => 'boolean',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after:start_date',
        ]);

        $data = $request->only(['title', 'link_url', 'position', 'size', 'height', 'sort_order', 'is_active', 'start_date', 'end_date']);
        $data['is_active'] = $request->boolean('is_active');
        $data['sort_order'] = $data['sort_order'] ?? 0;

        // Upload imagine nouă
        if ($request->hasFile('image')) {
            try {
                // Șterge imaginea vechea
                if ($banner->image_path) {
                    $this->imageService->deleteImage($banner->image_path);
                }
                
                $processedImages = $this->imageService->processImage($request->file('image'), 'banners', true);
                $data['image_path'] = $processedImages['main'];
            } catch (\Exception $e) {
                return back()->with('error', 'Eroare la procesarea imaginii: ' . $e->getMessage())->withInput();
            }
        }

        $banner->update($data);

        return redirect()->route('admin.banners.index')->with('success', 'Banner actualizat cu succes!');
    }

    public function destroy(Banner $banner)
    {
        // Șterge imaginea
        if ($banner->image_path) {
            $this->imageService->deleteImage($banner->image_path);
        }

        $banner->delete();

        return redirect()->route('admin.banners.index')->with('success', 'Banner șters cu succes!');
    }

    public function toggleStatus(Banner $banner)
    {
        $banner->update(['is_active' => !$banner->is_active]);

        return response()->json(['success' => true, 'is_active' => $banner->is_active]);
    }

    public function updateOrder(Request $request)
    {
        foreach ($request->order as $item) {
            Banner::where('id', $item['id'])->update(['sort_order' => $item['order']]);
        }

        return response()->json(['success' => true]);
    }
}
