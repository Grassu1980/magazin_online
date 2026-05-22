<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;

class CategoryController extends Controller
{
    public function __construct()
    {
        $this->middleware('admin');
    }

    public function index(Request $request)
    {
        $query = Category::query()->withCount('products');

        if ($request->has('search') && $request->search) {
            $query->where('name', 'like', "%{$request->search}%");
        }

        if ($request->has('status') && $request->status) {
            $query->where('is_active', $request->status === 'active');
        }

        $categories = $query->orderBy('sort_order')->paginate(20)->appends($request->query());
        return view('backend.categories.index', compact('categories'));
    }

    public function create()
    {
        $category = null;
        $categories = Category::orderBy('name')->get();

        return view('backend.categories.create-edit', compact('category', 'categories'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:categories,name',
            'slug' => 'nullable|string|max:255|unique:categories,slug',
            'description' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:' . setting('image_max_file_size', 2048),
            'sort_order' => 'nullable|integer|min:0',
            'is_active' => 'boolean'
        ]);

        $data = $request->only(['name', 'slug', 'description', 'sort_order', 'is_active']);
        $data['is_active'] = $request->has('is_active');

        if ($request->hasFile('image')) {
            $uploadPath = public_path('uploads/categories');
            if (!File::exists($uploadPath)) {
                File::makeDirectory($uploadPath, 0755, true);
            }
            $image = $request->file('image');
            $filename = time() . '_' . $image->getClientOriginalName();
            $image->move($uploadPath, $filename);
            $data['image'] = 'uploads/categories/' . $filename;
        }

        try {
            Category::create($data);
            return redirect()->route('admin.categories.index')->with('success', 'Categorie creată cu succes!');
        } catch (\Exception $e) {
            return back()->with('error', 'Eroare la crearea categoriei: ' . $e->getMessage())->withInput();
        }
    }

    public function show(Category $category)
    {
        $category->load('products');
        return view('backend.categories.show', compact('category'));
    }

    public function edit(Category $category)
    {
        $categories = Category::orderBy('name')->get();

        return view('backend.categories.create-edit', compact('category', 'categories'));
    }

    public function update(Request $request, Category $category)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:categories,name,' . $category->id,
            'slug' => 'nullable|string|max:255|unique:categories,slug,' . $category->id,
            'description' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:' . setting('image_max_file_size', 2048),
            'sort_order' => 'nullable|integer|min:0',
            'is_active' => 'boolean'
        ]);

        $data = $request->only(['name', 'slug', 'description', 'sort_order', 'is_active']);
        $data['is_active'] = $request->has('is_active');

        if (!$request->filled('slug')) {
            unset($data['slug']);
        }

        if ($request->boolean('remove_image')) {
            if ($category->image && File::exists(public_path($category->image))) {
                File::delete(public_path($category->image));
            }
            $data['image'] = null;
        }

        if ($request->hasFile('image')) {
            if ($category->image && File::exists(public_path($category->image))) {
                File::delete(public_path($category->image));
            }
            $uploadPath = public_path('uploads/categories');
            if (!File::exists($uploadPath)) {
                File::makeDirectory($uploadPath, 0755, true);
            }
            $image = $request->file('image');
            $filename = time() . '_' . $image->getClientOriginalName();
            $image->move($uploadPath, $filename);
            $data['image'] = 'uploads/categories/' . $filename;
        }

        try {
            $category->update($data);
            return redirect()->route('admin.categories.index')->with('success', 'Categorie actualizată cu succes!');
        } catch (\Exception $e) {
            return back()->with('error', 'Eroare la actualizarea categoriei: ' . $e->getMessage())->withInput();
        }
    }

    public function destroy(Category $category)
    {
        if ($category->products()->count() > 0) {
            return back()->with('error', 'Nu puteți șterge o categorie care are produse!');
        }

        if ($category->image && File::exists(public_path($category->image))) {
            File::delete(public_path($category->image));
        }

        $category->delete();
        return redirect()->route('admin.categories.index')->with('success', 'Categorie ștearsă cu succes!');
    }
}
