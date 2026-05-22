@extends('layouts.admin')

@section('title', $product->name . ' - Admin Panel')

@section('content')
<div class="p-6">
    <div class="mb-6 flex justify-between items-center">
        <a href="{{ route('admin.products.index') }}" class="text-blue-600 hover:text-blue-700">
            <i class="fas fa-arrow-left mr-1"></i>Înapoi la produse
        </a>
        <div class="flex gap-2">
            <a href="{{ route('admin.products.edit', $product->id) }}" class="px-4 py-2 bg-yellow-600 text-white rounded-lg hover:bg-yellow-700">
                <i class="fas fa-edit mr-2"></i>Editează
            </a>
            <form action="{{ route('admin.products.destroy', $product->id) }}" method="POST" class="inline">
                @csrf
                @method('DELETE')
                <button type="submit" class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700" onclick="return confirm('Sigur dorești să ștergi acest produs?')">
                    <i class="fas fa-trash mr-2"></i>Șterge
                </button>
            </form>
        </div>
    </div>
    
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Product Images -->
        <div class="lg:col-span-1">
            <div class="bg-white rounded-lg shadow p-6">
                @php
                    $galleryImages = collect();

                    if (!empty($product->image)) {
                        $galleryImages->push($product->image);
                    }

                    $galleryImages = $galleryImages
                        ->merge($product->images->pluck('image_path')->filter())
                        ->unique()
                        ->values();
                @endphp

                <h2 class="font-semibold mb-4">Galerie Imagini</h2>
                @if($galleryImages->count() > 0)
                <div class="relative">
                    <img id="product-carousel-image"
                         src="{{ asset($galleryImages->first()) }}"
                         alt="{{ $product->name }}"
                         class="w-full h-80 object-cover rounded-lg">

                    @if($galleryImages->count() > 1)
                    <button type="button"
                            onclick="changeCarouselImage(-1)"
                            class="absolute left-2 top-1/2 -translate-y-1/2 bg-black/50 text-white w-9 h-9 rounded-full hover:bg-black/70">
                        <i class="fas fa-chevron-left"></i>
                    </button>
                    <button type="button"
                            onclick="changeCarouselImage(1)"
                            class="absolute right-2 top-1/2 -translate-y-1/2 bg-black/50 text-white w-9 h-9 rounded-full hover:bg-black/70">
                        <i class="fas fa-chevron-right"></i>
                    </button>
                    @endif
                </div>

                @if($galleryImages->count() > 1)
                <div class="grid grid-cols-4 gap-2 mt-3">
                    @foreach($galleryImages as $image)
                    <button type="button"
                            class="carousel-thumb border-2 border-transparent rounded overflow-hidden"
                            onclick="setCarouselImage({{ $loop->index }})">
                        <img src="{{ asset($image) }}" alt="{{ $product->name }}" class="w-full h-16 object-cover">
                    </button>
                    @endforeach
                </div>
                @endif
                @else
                <div class="w-full h-64 bg-gray-200 rounded-lg flex items-center justify-center">
                    <i class="fas fa-image text-4xl text-gray-400"></i>
                </div>
                @endif
            </div>
        </div>
        
        <!-- Product Details -->
        <div class="lg:col-span-2 space-y-6">
            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex justify-between items-start mb-4">
                    <div>
                        <h1 class="text-2xl font-bold">{{ $product->name }}</h1>
                        <p class="text-gray-500">SKU: {{ $product->sku }}</p>
                    </div>
                    <div class="flex gap-2">
                        <span class="px-3 py-1 rounded-full text-sm font-medium {{ $product->is_active ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-700' }}">
                            {{ $product->is_active ? 'Activ' : 'Inactiv' }}
                        </span>
                        @if($product->is_featured)
                        <span class="px-3 py-1 rounded-full text-sm font-medium bg-yellow-100 text-yellow-700">
                            Recomandat
                        </span>
                        @endif
                        @if($product->is_on_sale)
                        <span class="px-3 py-1 rounded-full text-sm font-medium bg-red-100 text-red-700">
                            Promoție
                        </span>
                        @endif
                    </div>
                </div>
                
                <div class="grid grid-cols-2 gap-4 mb-6">
                    <div>
                        <p class="text-sm text-gray-500">Categorie</p>
                        <p class="font-medium">{{ $product->category->name ?? '-' }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">Cota TVA</p>
                        <p class="font-medium">{{ $product->vat_rate ?? 21 }}%</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">Preț fără TVA</p>
                        <p class="font-medium">{{ number_format($product->price_without_vat ?? ($product->price / (1 + ($product->vat_rate ?? 21) / 100)), 2) }} lei</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">Preț Final (cu TVA)</p>
                        <p class="font-medium">{{ number_format($product->price, 2) }} lei</p>
                    </div>
                    @if($product->is_on_sale && $product->special_price)
                    <div>
                        <p class="text-sm text-gray-500">Preț Redus</p>
                        <p class="font-medium text-red-600">{{ number_format($product->special_price, 2) }} lei</p>
                    </div>
                    @endif
                </div>
                
                
                @if($product->description)
                <div>
                    <p class="text-sm text-gray-500">Descriere</p>
                    <p>{{ $product->description }}</p>
                </div>
                @endif
            </div>
            
            <!-- Inventory -->
            <div class="bg-white rounded-lg shadow p-6">
                <h2 class="font-semibold mb-4">Stoc</h2>
                <div class="grid grid-cols-3 gap-4">
                    <div>
                        <p class="text-sm text-gray-500">Cantitate</p>
                        <p class="text-xl font-bold {{ $product->stock_quantity > 10 ? 'text-green-600' : ($product->stock_quantity > 0 ? 'text-yellow-600' : 'text-red-600') }}">
                            {{ $product->stock_quantity }} buc
                        </p>
                    </div>
                    @if($product->stockRecord)
                    <div>
                        <p class="text-sm text-gray-500">Preț Cost</p>
                        <p class="font-medium">{{ $product->stockRecord->cost_price ? number_format($product->stockRecord->cost_price, 2) . ' lei' : '-' }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">Preț Vânzare</p>
                        <p class="font-medium">{{ $product->stockRecord->selling_price ? number_format($product->stockRecord->selling_price, 2) . ' lei' : '-' }}</p>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Stock History -->
            <div class="bg-white rounded-lg shadow p-6">
                <h2 class="font-semibold mb-4">Istoric Stoc</h2>
                @if($product->stockRecord && $product->stockRecord->histories->count() > 0)
                <div class="overflow-x-auto">
                    <table class="min-w-full">
                        <thead>
                            <tr class="border-b">
                                <th class="text-left py-2 px-3">Data</th>
                                <th class="text-left py-2 px-3">Tip</th>
                                <th class="text-left py-2 px-3">Cantitate</th>
                                <th class="text-left py-2 px-3">Înainte</th>
                                <th class="text-left py-2 px-3">După</th>
                                <th class="text-left py-2 px-3">Referință</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($product->stockRecord->histories->take(10) as $history)
                            <tr class="border-b">
                                <td class="py-2 px-3">{{ $history->created_at->format('d.m.Y H:i') }}</td>
                                <td class="py-2 px-3">
                                    <span class="px-2 py-1 rounded text-xs {{ $history->type === 'in' ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                                        {{ $history->type === 'in' ? 'Intrare' : 'Ieșire' }}
                                    </span>
                                </td>
                                <td class="py-2 px-3">{{ $history->quantity }}</td>
                                <td class="py-2 px-3">{{ $history->old_quantity }}</td>
                                <td class="py-2 px-3">{{ $history->new_quantity }}</td>
                                <td class="py-2 px-3">{{ $history->reference ?? '-' }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @else
                <p class="text-gray-500">Nu există istoric de stoc.</p>
                @endif
            </div>
            
            <!-- Stats -->
            <div class="bg-white rounded-lg shadow p-6">
                <h2 class="font-semibold mb-4">Statistici</h2>
                <div class="grid grid-cols-3 gap-4">
                    <div>
                        <p class="text-sm text-gray-500">Vizualizări</p>
                        <p class="text-xl font-bold">{{ $product->views }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">Vânzări</p>
                        <p class="text-xl font-bold">{{ $product->sold_count ?? 0 }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">Rating</p>
                        <p class="text-xl font-bold">
                            @php
                                $averageRating = method_exists($product, 'reviews')
                                    ? optional($product->reviews)->avg('rating')
                                    : null;
                            @endphp
                            @if($averageRating)
                            {{ number_format($averageRating, 1) }} <i class="fas fa-star text-yellow-500 text-sm"></i>
                            @else
                            -
                            @endif
                        </p>
                    </div>
                </div>
            </div>
            
            <!-- SEO -->
            <div class="bg-white rounded-lg shadow p-6">
                <h2 class="font-semibold mb-4">SEO</h2>
                <div class="space-y-3">
                    <div>
                        <p class="text-sm text-gray-500">URL Slug</p>
                        <p class="font-medium">/products/{{ $product->slug }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">Meta Title</p>
                        <p class="font-medium">{{ $product->meta_title ?? $product->name }}</p>
                    </div>
                    @if($product->meta_description)
                    <div>
                        <p class="text-sm text-gray-500">Meta Description</p>
                        <p class="font-medium">{{ $product->meta_description }}</p>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

@if($galleryImages->count() > 1)
<script>
const carouselImages = @json($galleryImages->map(fn($img) => asset($img))->values());
let currentCarouselIndex = 0;

function renderCarouselImage() {
    const imageElement = document.getElementById('product-carousel-image');
    const thumbs = document.querySelectorAll('.carousel-thumb');

    imageElement.src = carouselImages[currentCarouselIndex];
    thumbs.forEach((thumb, index) => {
        thumb.classList.toggle('border-blue-500', index === currentCarouselIndex);
    });
}

function changeCarouselImage(direction) {
    currentCarouselIndex = (currentCarouselIndex + direction + carouselImages.length) % carouselImages.length;
    renderCarouselImage();
}

function setCarouselImage(index) {
    currentCarouselIndex = index;
    renderCarouselImage();
}

renderCarouselImage();
</script>
@endif
@endsection