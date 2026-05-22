<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductImage;
use App\Services\ImageService;
use App\Services\PriceService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ProductController extends Controller
{
    protected $imageService;
    protected $priceService;

    public function __construct(ImageService $imageService, PriceService $priceService)
    {
        $this->imageService = $imageService;
        $this->priceService = $priceService;
        $this->middleware('admin');
    }

    public function index(Request $request)
    {
        $query = Product::with('category');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('sku', 'like', "%{$search}%");
            });
        }

        if ($request->filled('category')) {
            $query->where('category_id', $request->category);
        }

        if ($request->filled('status')) {
            $query->where('is_active', $request->status === 'active');
        }

        if ($request->filled('stock')) {
            if ($request->stock === 'low') {
                $query->where('stock', '<', 10)->where('stock', '>', 0);
            } elseif ($request->stock === 'out') {
                $query->where('stock', 0);
            } elseif ($request->stock === 'in') {
                $query->where('stock', '>=', 10);
            }
        }

        $products = $query->latest()->paginate(20)->appends($request->query());
        $categories = Category::all();

        return view('backend.products.index', compact('products', 'categories'));
    }

    public function create()
    {
        $categories = Category::where('is_active', true)->get();
        $product = null;
        return view('backend.products.create-edit', compact('categories', 'product'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:products,slug',
            'category_id' => 'required|exists:categories,id',
            'price_with_vat' => 'nullable|numeric|min:0',
            'price_without_vat' => 'nullable|numeric|min:0',
            'vat_rate' => 'required|integer|in:0,11,21',
            'purchase_price_without_vat' => 'nullable|numeric|min:0',
            'markup_percentage' => 'nullable|numeric|min:0',
            'promo_price' => 'nullable|numeric|min:0',
            'promo_start' => 'nullable|date',
            'promo_end' => 'nullable|date|after:promo_start',
            'special_price' => 'nullable|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'sku' => 'nullable|string|max:100|unique:products,sku',
            'description' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:' . setting('image_max_file_size', 2048),
            'images.*' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:' . setting('image_max_file_size', 2048),
            'is_active' => 'boolean',
            'is_featured' => 'boolean'
        ]);

        $data = $request->only([
            'name', 'category_id', 'vat_rate', 'purchase_price_without_vat', 
            'markup_percentage', 'promo_price', 'promo_start', 'promo_end',
            'special_price', 'stock', 'sku', 'description', 'is_active', 'is_featured'
        ]);
        
        $data['slug'] = $this->generateUniqueSlug($request->input('slug'), $request->name);
        $data['is_active'] = $request->boolean('is_active');
        $data['is_featured'] = $request->boolean('is_featured');

        // Procesează datele prețurilor folosind PriceService
        $priceData = [
            'vat_rate' => $data['vat_rate'],
            'purchase_price_without_vat' => $data['purchase_price_without_vat'] ?? 0,
        ];
        
        if ($request->filled('price_with_vat')) {
            $priceData['price_with_vat'] = $request->input('price_with_vat');
        }
        
        if ($request->filled('price_without_vat')) {
            $priceData['price_without_vat'] = $request->input('price_without_vat');
        }
        
        if ($request->filled('markup_percentage')) {
            $priceData['markup_percentage'] = $request->input('markup_percentage');
        }
        
        $processedData = $this->priceService->processPriceData($priceData);
        $data['price_without_vat'] = $processedData['price_without_vat'] ?? 0;
        $data['price_with_vat'] = $processedData['price_with_vat'] ?? 0;
        $data['price'] = $data['price_with_vat']; // Keep backward compatibility

        // Upload imagine principală cu procesare automată
        if ($request->hasFile('image')) {
            try {
                $processedImages = $this->imageService->processImage($request->file('image'), 'products', true);
                $data['image'] = $processedImages['main'];
            } catch (\Exception $e) {
                return back()->with('error', 'Eroare la procesarea imaginii: ' . $e->getMessage())->withInput();
            }
        }

        try {
            $product = Product::create($data);
        } catch (\Exception $e) {
            return back()->with('error', 'Nu s-a putut crea produsul.')->withInput();
        }

        // Upload imagini adiționale cu procesare automată
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $index => $image) {
                try {
                    $processedImages = $this->imageService->processImage($image, 'products', true);
                    ProductImage::create([
                        'product_id' => $product->id,
                        'image' => $processedImages['main'],
                        'sort_order' => $index
                    ]);
                } catch (\Exception $e) {
                    // Continuăm cu restul imaginilor chiar dacă una eșuează
                    continue;
                }
            }
        }

        return redirect()->route('admin.products.index')->with('success', 'Produs creat cu succes!');
    }

    public function show(Product $product)
    {
        $product->load(['category', 'images', 'stockRecord.histories']);
        return view('backend.products.show', compact('product'));
    }

    public function edit(Product $product)
    {
        $categories = Category::where('is_active', true)->get();
        $product->load('images');
        return view('backend.products.create-edit', compact('product', 'categories'));
    }

    public function update(Request $request, Product $product)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:products,slug,' . $product->id,
            'category_id' => 'required|exists:categories,id',
            'price_with_vat' => 'nullable|numeric|min:0',
            'price_without_vat' => 'nullable|numeric|min:0',
            'vat_rate' => 'required|integer|in:0,11,21',
            'purchase_price_without_vat' => 'nullable|numeric|min:0',
            'markup_percentage' => 'nullable|numeric|min:0',
            'promo_price' => 'nullable|numeric|min:0',
            'promo_start' => 'nullable|date',
            'promo_end' => 'nullable|date|after:promo_start',
            'special_price' => 'nullable|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'sku' => 'nullable|string|max:100|unique:products,sku,' . $product->id,
            'description' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:' . setting('image_max_file_size', 2048),
            'images.*' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:' . setting('image_max_file_size', 2048),
            'is_active' => 'boolean',
            'is_featured' => 'boolean'
        ]);

        // Salvează datele vechi pentru istoric
        $oldData = [
            'price_without_vat' => $product->price_without_vat,
            'price_with_vat' => $product->price_with_vat,
            'vat_rate' => $product->vat_rate,
        ];

        $data = $request->only([
            'name', 'category_id', 'vat_rate', 'purchase_price_without_vat', 
            'markup_percentage', 'promo_price', 'promo_start', 'promo_end',
            'special_price', 'stock', 'sku', 'description', 'is_active', 'is_featured'
        ]);
        
        $data['slug'] = $this->generateUniqueSlug($request->input('slug'), $request->name, $product->id);
        $data['is_active'] = $request->boolean('is_active');
        $data['is_featured'] = $request->boolean('is_featured');

        // Procesează datele prețurilor folosind PriceService
        $priceData = [
            'vat_rate' => $data['vat_rate'],
            'purchase_price_without_vat' => $data['purchase_price_without_vat'] ?? 0,
        ];
        
        if ($request->filled('price_with_vat')) {
            $priceData['price_with_vat'] = $request->input('price_with_vat');
        }
        
        if ($request->filled('price_without_vat')) {
            $priceData['price_without_vat'] = $request->input('price_without_vat');
        }
        
        if ($request->filled('markup_percentage')) {
            $priceData['markup_percentage'] = $request->input('markup_percentage');
        }
        
        $processedData = $this->priceService->processPriceData($priceData);
        $data['price_without_vat'] = $processedData['price_without_vat'] ?? 0;
        $data['price_with_vat'] = $processedData['price_with_vat'] ?? 0;
        $data['price'] = $data['price_with_vat']; // Keep backward compatibility

        // Upload imagine principală cu procesare automată
        if ($request->has('remove_image') && $request->remove_image) {
            $this->imageService->deleteImage($product->image);
            $data['image'] = null;
        }

        if ($request->hasFile('image')) {
            try {
                // Șterge imaginea vechea
                if ($product->image) {
                    $this->imageService->deleteImage($product->image);
                }
                
                $processedImages = $this->imageService->processImage($request->file('image'), 'products', true);
                $data['image'] = $processedImages['main'];
            } catch (\Exception $e) {
                return back()->with('error', 'Eroare la procesarea imaginii: ' . $e->getMessage())->withInput();
            }
        }

        $product->update($data);

        // Salvează istoricul prețurilor dacă s-a modificat prețul
        $newData = [
            'price_without_vat' => $product->price_without_vat,
            'price_with_vat' => $product->price_with_vat,
            'vat_rate' => $product->vat_rate,
        ];
        $this->priceService->savePriceHistory($product, $oldData, $newData);

        // Upload imagini adiționale cu procesare automată
        if ($request->hasFile('images')) {
            $baseSort = $product->images()->count();
            foreach ($request->file('images') as $index => $image) {
                try {
                    $processedImages = $this->imageService->processImage($image, 'products', true);
                    ProductImage::create([
                        'product_id' => $product->id,
                        'image' => $processedImages['main'],
                        'sort_order' => $index + $baseSort
                    ]);
                } catch (\Exception $e) {
                    // Continuăm cu restul imaginilor chiar dacă una eșuează
                    continue;
                }
            }
        }

        return redirect()->route('admin.products.index')->with('success', 'Produs actualizat cu succes!');
    }

    public function destroy(Product $product)
    {
        // Șterge imaginile folosind ImageService
        if ($product->image) {
            $this->imageService->deleteImage($product->image);
        }
        foreach ($product->images as $image) {
            $this->imageService->deleteImage($image->image);
        }

        $product->delete();
        return redirect()->route('admin.products.index')->with('success', 'Produs șters cu succes!');
    }

    public function deleteImage(ProductImage $image)
    {
        $this->imageService->deleteImage($image->image);
        $image->delete();

        return response()->json(['success' => true]);
    }

    private function generateUniqueSlug(?string $slug, string $name, ?int $ignoreId = null): string
    {
        $baseSlug = Str::slug($slug ?: $name);
        $currentSlug = $baseSlug;
        $counter = 1;

        while (Product::where('slug', $currentSlug)
            ->when($ignoreId, fn($query) => $query->where('id', '!=', $ignoreId))
            ->exists()) {
            $currentSlug = $baseSlug . '-' . $counter++;
        }

        return $currentSlug;
    }
}
