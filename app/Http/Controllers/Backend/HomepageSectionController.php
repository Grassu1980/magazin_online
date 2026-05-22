<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\HomepageSection;
use App\Models\Product;
use App\Models\Category;
use Illuminate\Http\Request;

class HomepageSectionController extends Controller
{
    public function __construct()
    {
        $this->middleware('admin');
    }

    public function index()
    {
        $sections = HomepageSection::ordered()->get();
        return view('backend.homepage_sections.index', compact('sections'));
    }

    public function create()
    {
        $products = Product::where('is_active', true)->get();
        $categories = Category::where('is_active', true)->get();
        return view('backend.homepage_sections.create', compact('products', 'categories'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'type' => 'required|in:slider,banners_row,products_grid,categories_grid,custom_html,video,text_block',
            'title' => 'nullable|string|max:255',
            'config' => 'nullable|array',
            'sort_order' => 'nullable|integer|min:0',
            'is_active' => 'boolean',
        ]);

        $data = $request->only(['type', 'title', 'config', 'sort_order', 'is_active']);
        $data['is_active'] = $request->boolean('is_active');
        $data['sort_order'] = $data['sort_order'] ?? 0;
        $data['config'] = $data['config'] ?? [];

        HomepageSection::create($data);

        return redirect()->route('admin.homepage-sections.index')->with('success', 'Secțiune creată cu succes!');
    }

    public function show(HomepageSection $section)
    {
        return view('backend.homepage_sections.show', compact('section'));
    }

    public function edit(HomepageSection $section)
    {
        $products = Product::where('is_active', true)->get();
        $categories = Category::where('is_active', true)->get();
        return view('backend.homepage_sections.edit', compact('section', 'products', 'categories'));
    }

    public function update(Request $request, HomepageSection $section)
    {
        $request->validate([
            'type' => 'required|in:slider,banners_row,products_grid,categories_grid,custom_html,video,text_block',
            'title' => 'nullable|string|max:255',
            'config' => 'nullable|array',
            'sort_order' => 'nullable|integer|min:0',
            'is_active' => 'boolean',
        ]);

        $data = $request->only(['type', 'title', 'config', 'sort_order', 'is_active']);
        $data['is_active'] = $request->boolean('is_active');
        $data['sort_order'] = $data['sort_order'] ?? 0;
        $data['config'] = $data['config'] ?? [];

        $section->update($data);

        return redirect()->route('admin.homepage-sections.index')->with('success', 'Secțiune actualizată cu succes!');
    }

    public function destroy(HomepageSection $section)
    {
        $section->delete();

        return redirect()->route('admin.homepage-sections.index')->with('success', 'Secțiune ștearsă cu succes!');
    }

    public function toggleStatus(HomepageSection $section)
    {
        $section->update(['is_active' => !$section->is_active]);

        return response()->json(['success' => true, 'is_active' => $section->is_active]);
    }

    public function updateOrder(Request $request)
    {
        foreach ($request->order as $item) {
            HomepageSection::where('id', $item['id'])->update(['sort_order' => $item['order']]);
        }

        return response()->json(['success' => true]);
    }
}
