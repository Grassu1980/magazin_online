@extends('layouts.app')

@section('title', 'Magazin Online - Cumpără produse de calitate')

@section('content')
<div class="min-h-screen flex flex-col">
    <!-- Homepage Sections (Dynamic) -->
    @if($homepageSections)
    @foreach($homepageSections as $section)
    @switch($section['type'])
    @case('slider')
    @if(!empty($section['banners']))
    <section class="relative overflow-hidden">
        <div class="slider-container">
            @foreach($section['banners'] as $banner)
            <div class="slider-item">
                @if($banner->link_url)
                <a href="{{ $banner->link_url }}">
                @endif
                @switch($banner->size)
                @case('small')
                @switch($banner->height)
                @case('small')
                <img src="{{ asset($banner->image_path) }}" alt="{{ $banner->title }}" class="w-1/4 h-48 object-cover mx-auto">
                @break
                @case('medium')
                <img src="{{ asset($banner->image_path) }}" alt="{{ $banner->title }}" class="w-1/4 h-64 object-cover mx-auto">
                @break
                @case('large')
                <img src="{{ asset($banner->image_path) }}" alt="{{ $banner->title }}" class="w-1/4 h-96 object-cover mx-auto">
                @break
                @case('xlarge')
                <img src="{{ asset($banner->image_path) }}" alt="{{ $banner->title }}" class="w-1/4 h-[500px] object-cover mx-auto">
                @break
                @case('full')
                <img src="{{ asset($banner->image_path) }}" alt="{{ $banner->title }}" class="w-1/4 h-screen object-cover mx-auto">
                @break
                @default
                <img src="{{ asset($banner->image_path) }}" alt="{{ $banner->title }}" class="w-1/4 h-64 object-cover mx-auto">
                @endswitch
                @break
                @case('medium')
                @switch($banner->height)
                @case('small')
                <img src="{{ asset($banner->image_path) }}" alt="{{ $banner->title }}" class="w-1/2 h-48 object-cover mx-auto">
                @break
                @case('medium')
                <img src="{{ asset($banner->image_path) }}" alt="{{ $banner->title }}" class="w-1/2 h-64 object-cover mx-auto">
                @break
                @case('large')
                <img src="{{ asset($banner->image_path) }}" alt="{{ $banner->title }}" class="w-1/2 h-96 object-cover mx-auto">
                @break
                @case('xlarge')
                <img src="{{ asset($banner->image_path) }}" alt="{{ $banner->title }}" class="w-1/2 h-[500px] object-cover mx-auto">
                @break
                @case('full')
                <img src="{{ asset($banner->image_path) }}" alt="{{ $banner->title }}" class="w-1/2 h-screen object-cover mx-auto">
                @break
                @default
                <img src="{{ asset($banner->image_path) }}" alt="{{ $banner->title }}" class="w-1/2 h-64 object-cover mx-auto">
                @endswitch
                @break
                @case('large')
                @switch($banner->height)
                @case('small')
                <img src="{{ asset($banner->image_path) }}" alt="{{ $banner->title }}" class="w-3/4 h-48 object-cover mx-auto">
                @break
                @case('medium')
                <img src="{{ asset($banner->image_path) }}" alt="{{ $banner->title }}" class="w-3/4 h-64 object-cover mx-auto">
                @break
                @case('large')
                <img src="{{ asset($banner->image_path) }}" alt="{{ $banner->title }}" class="w-3/4 h-96 object-cover mx-auto">
                @break
                @case('xlarge')
                <img src="{{ asset($banner->image_path) }}" alt="{{ $banner->title }}" class="w-3/4 h-[500px] object-cover mx-auto">
                @break
                @case('full')
                <img src="{{ asset($banner->image_path) }}" alt="{{ $banner->title }}" class="w-3/4 h-screen object-cover mx-auto">
                @break
                @default
                <img src="{{ asset($banner->image_path) }}" alt="{{ $banner->title }}" class="w-3/4 h-64 object-cover mx-auto">
                @endswitch
                @break
                @case('full')
                @switch($banner->height)
                @case('small')
                <img src="{{ asset($banner->image_path) }}" alt="{{ $banner->title }}" class="w-full h-48 object-cover">
                @break
                @case('medium')
                <img src="{{ asset($banner->image_path) }}" alt="{{ $banner->title }}" class="w-full h-64 object-cover">
                @break
                @case('large')
                <img src="{{ asset($banner->image_path) }}" alt="{{ $banner->title }}" class="w-full h-96 object-cover">
                @break
                @case('xlarge')
                <img src="{{ asset($banner->image_path) }}" alt="{{ $banner->title }}" class="w-full h-[500px] object-cover">
                @break
                @case('full')
                <img src="{{ asset($banner->image_path) }}" alt="{{ $banner->title }}" class="w-full h-screen object-cover">
                @break
                @default
                <img src="{{ asset($banner->image_path) }}" alt="{{ $banner->title }}" class="w-full h-64 object-cover">
                @endswitch
                @break
                @default
                <img src="{{ asset($banner->image_path) }}" alt="{{ $banner->title }}" class="w-full h-64 object-cover">
                @endswitch
                @if($banner->link_url)
                </a>
                @endif
            </div>
            @endforeach
        </div>
    </section>
    @endif
    @break

    @case('banners_row')
    @if(!empty($section['banners']))
    <section class="py-8">
        <div class="container mx-auto px-4">
            @if($section['title'])
            <h2 class="text-2xl font-bold mb-6">{{ $section['title'] }}</h2>
            @endif
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                @foreach($section['banners'] as $banner)
                <div class="banner-item">
                    @if($banner->link_url)
                    <a href="{{ $banner->link_url }}">
                    @endif
                    @switch($banner->size)
                    @case('small')
                    @switch($banner->height)
                    @case('small')
                    <img src="{{ asset($banner->image_path) }}" alt="{{ $banner->title }}" class="w-1/4 h-48 object-cover rounded-lg hover:opacity-90 transition-opacity mx-auto">
                    @break
                    @case('medium')
                    <img src="{{ asset($banner->image_path) }}" alt="{{ $banner->title }}" class="w-1/4 h-64 object-cover rounded-lg hover:opacity-90 transition-opacity mx-auto">
                    @break
                    @case('large')
                    <img src="{{ asset($banner->image_path) }}" alt="{{ $banner->title }}" class="w-1/4 h-96 object-cover rounded-lg hover:opacity-90 transition-opacity mx-auto">
                    @break
                    @case('xlarge')
                    <img src="{{ asset($banner->image_path) }}" alt="{{ $banner->title }}" class="w-1/4 h-[500px] object-cover rounded-lg hover:opacity-90 transition-opacity mx-auto">
                    @break
                    @case('full')
                    <img src="{{ asset($banner->image_path) }}" alt="{{ $banner->title }}" class="w-1/4 h-screen object-cover rounded-lg hover:opacity-90 transition-opacity mx-auto">
                    @break
                    @default
                    <img src="{{ asset($banner->image_path) }}" alt="{{ $banner->title }}" class="w-1/4 h-48 object-cover rounded-lg hover:opacity-90 transition-opacity mx-auto">
                    @endswitch
                    @break
                    @case('medium')
                    @switch($banner->height)
                    @case('small')
                    <img src="{{ asset($banner->image_path) }}" alt="{{ $banner->title }}" class="w-1/2 h-48 object-cover rounded-lg hover:opacity-90 transition-opacity mx-auto">
                    @break
                    @case('medium')
                    <img src="{{ asset($banner->image_path) }}" alt="{{ $banner->title }}" class="w-1/2 h-64 object-cover rounded-lg hover:opacity-90 transition-opacity mx-auto">
                    @break
                    @case('large')
                    <img src="{{ asset($banner->image_path) }}" alt="{{ $banner->title }}" class="w-1/2 h-96 object-cover rounded-lg hover:opacity-90 transition-opacity mx-auto">
                    @break
                    @case('xlarge')
                    <img src="{{ asset($banner->image_path) }}" alt="{{ $banner->title }}" class="w-1/2 h-[500px] object-cover rounded-lg hover:opacity-90 transition-opacity mx-auto">
                    @break
                    @case('full')
                    <img src="{{ asset($banner->image_path) }}" alt="{{ $banner->title }}" class="w-1/2 h-screen object-cover rounded-lg hover:opacity-90 transition-opacity mx-auto">
                    @break
                    @default
                    <img src="{{ asset($banner->image_path) }}" alt="{{ $banner->title }}" class="w-1/2 h-48 object-cover rounded-lg hover:opacity-90 transition-opacity mx-auto">
                    @endswitch
                    @break
                    @case('large')
                    @switch($banner->height)
                    @case('small')
                    <img src="{{ asset($banner->image_path) }}" alt="{{ $banner->title }}" class="w-3/4 h-48 object-cover rounded-lg hover:opacity-90 transition-opacity mx-auto">
                    @break
                    @case('medium')
                    <img src="{{ asset($banner->image_path) }}" alt="{{ $banner->title }}" class="w-3/4 h-64 object-cover rounded-lg hover:opacity-90 transition-opacity mx-auto">
                    @break
                    @case('large')
                    <img src="{{ asset($banner->image_path) }}" alt="{{ $banner->title }}" class="w-3/4 h-96 object-cover rounded-lg hover:opacity-90 transition-opacity mx-auto">
                    @break
                    @case('xlarge')
                    <img src="{{ asset($banner->image_path) }}" alt="{{ $banner->title }}" class="w-3/4 h-[500px] object-cover rounded-lg hover:opacity-90 transition-opacity mx-auto">
                    @break
                    @case('full')
                    <img src="{{ asset($banner->image_path) }}" alt="{{ $banner->title }}" class="w-3/4 h-screen object-cover rounded-lg hover:opacity-90 transition-opacity mx-auto">
                    @break
                    @default
                    <img src="{{ asset($banner->image_path) }}" alt="{{ $banner->title }}" class="w-3/4 h-48 object-cover rounded-lg hover:opacity-90 transition-opacity mx-auto">
                    @endswitch
                    @break
                    @case('full')
                    @switch($banner->height)
                    @case('small')
                    <img src="{{ asset($banner->image_path) }}" alt="{{ $banner->title }}" class="w-full h-48 object-cover rounded-lg hover:opacity-90 transition-opacity">
                    @break
                    @case('medium')
                    <img src="{{ asset($banner->image_path) }}" alt="{{ $banner->title }}" class="w-full h-64 object-cover rounded-lg hover:opacity-90 transition-opacity">
                    @break
                    @case('large')
                    <img src="{{ asset($banner->image_path) }}" alt="{{ $banner->title }}" class="w-full h-96 object-cover rounded-lg hover:opacity-90 transition-opacity">
                    @break
                    @case('xlarge')
                    <img src="{{ asset($banner->image_path) }}" alt="{{ $banner->title }}" class="w-full h-[500px] object-cover rounded-lg hover:opacity-90 transition-opacity">
                    @break
                    @case('full')
                    <img src="{{ asset($banner->image_path) }}" alt="{{ $banner->title }}" class="w-full h-screen object-cover rounded-lg hover:opacity-90 transition-opacity">
                    @break
                    @default
                    <img src="{{ asset($banner->image_path) }}" alt="{{ $banner->title }}" class="w-full h-48 object-cover rounded-lg hover:opacity-90 transition-opacity">
                    @endswitch
                    @break
                    @default
                    <img src="{{ asset($banner->image_path) }}" alt="{{ $banner->title }}" class="w-full h-48 object-cover rounded-lg hover:opacity-90 transition-opacity">
                    @endswitch
                    @if($banner->link_url)
                    </a>
                    @endif
                </div>
                @endforeach
            </div>
        </div>
    </section>
    @endif
    @break

    @case('products_grid')
    @if(!empty($section['products']) && $section['products']->count() > 0)
    <section class="py-12 @if(isset($section['config']['background_color'])) bg-[{{ $section['config']['background_color'] }}] @else bg-gray-100 @endif">
        <div class="container mx-auto px-4">
            <div class="flex justify-between items-center mb-8">
                @if($section['title'])
                <h2 class="text-2xl font-bold">{{ $section['title'] }}</h2>
                @else
                <h2 class="text-2xl font-bold">Produse</h2>
                @endif
                <a href="{{ route('products.index') }}" class="text-blue-600 hover:text-blue-700">
                    Vezi toate <i class="fas fa-arrow-right ml-1"></i>
                </a>
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                @foreach($section['products'] as $product)
                @include('components.product-card', ['product' => $product])
                @endforeach
            </div>
        </div>
    </section>
    @endif
    @break

    @case('categories_grid')
    @if(!empty($section['categories']) && $section['categories']->count() > 0)
    <section class="py-12">
        <div class="container mx-auto px-4">
            @if($section['title'])
            <h2 class="text-2xl font-bold mb-8 text-center">{{ $section['title'] }}</h2>
            @endif
            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-4">
                @foreach($section['categories'] as $category)
                <a href="{{ route('products.index', ['category' => $category->id]) }}"
                   class="bg-white rounded-lg shadow-md p-4 text-center hover:shadow-xl transition-shadow group">
                    @if($category->image)
                    <img src="{{ asset($category->image) }}" alt="{{ $category->name }}" class="w-full h-24 object-cover rounded mb-3 group-hover:scale-105 transition-transform">
                    @else
                    <div class="w-full h-24 bg-gray-200 rounded mb-3 flex items-center justify-center">
                        <i class="fas fa-tag text-3xl text-gray-400"></i>
                    </div>
                    @endif
                    <h3 class="font-semibold text-gray-800 group-hover:text-blue-600">{{ $category->name }}</h3>
                    <p class="text-xs text-gray-500">{{ $category->products()->count() }} produse</p>
                </a>
                @endforeach
            </div>
        </div>
    </section>
    @endif
    @break

    @case('custom_html')
    @if(!empty($section['html_content']))
    <section class="py-8">
        <div class="container mx-auto px-4">
            @if($section['title'])
            <h2 class="text-2xl font-bold mb-6">{{ $section['title'] }}</h2>
            @endif
            <div class="custom-html-content">
                {!! $section['html_content'] !!}
            </div>
        </div>
    </section>
    @endif
    @break

    @case('video')
    @if(!empty($section['video_url']))
    <section class="py-12 bg-gray-900 text-white">
        <div class="container mx-auto px-4">
            <div class="max-w-4xl mx-auto text-center">
                @if($section['video_title'])
                <h2 class="text-2xl font-bold mb-4">{{ $section['video_title'] }}</h2>
                @endif
                @if($section['video_description'])
                <p class="text-gray-300 mb-8">{{ $section['video_description'] }}</p>
                @endif
                <div class="video-container aspect-video">
                    <iframe src="{{ $section['video_url'] }}" class="w-full h-full" frameborder="0" allowfullscreen></iframe>
                </div>
            </div>
        </div>
    </section>
    @endif
    @break

    @case('text_block')
    @if(!empty($section['text_content']))
    <section class="py-12" style="background-color: {{ $section['background_color'] ?? '#ffffff' }}; color: {{ $section['text_color'] ?? '#000000' }};">
        <div class="container mx-auto px-4">
            @if($section['title'])
            <h2 class="text-2xl font-bold mb-6 text-center">{{ $section['title'] }}</h2>
            @endif
            <div class="max-w-4xl mx-auto text-center">
                {!! $section['text_content'] !!}
            </div>
        </div>
    </section>
    @endif
    @break

    @endswitch
    @endforeach
    @endif

    <!-- Hero Section (Fallback if no sections configured) -->
    @if(!$homepageSections || count($homepageSections) == 0)
    <section class="flex-1 bg-gradient-to-r from-primary to-secondary text-white flex items-center justify-center py-0.5">
        <div class="container mx-auto px-4">
            <div class="flex flex-col items-center justify-center text-center">
                @if(setting('logo'))
                <img src="{{ Storage::url(setting('logo')) }}" alt="{{ setting('site_name', 'MagazinOnline') }}" class="{{ setting('logo_size', 'h-24') }} mb-0.5">
                @endif
                <h1 class="text-sm font-bold mb-0.5">{{ setting('welcome_title', 'Bine ai venit la ' . setting('site_name', 'MagazinOnline')) }}</h1>
                <p class="text-xs mb-0.5 max-w-2xl">{{ setting('welcome_description', 'Descoperă cele mai bune produse la prețuri incredibile. Livrare rapidă și sigură.') }}</p>
                <a href="{{ route('products.index') }}" class="inline-block bg-white text-primary px-2 py-0.5 rounded-lg font-semibold hover:bg-gray-100 transition-colors text-xs">
                    Vezi Produse <i class="fas fa-arrow-right ml-1"></i>
                </a>
            </div>
        </div>
    </section>
    @endif

    <!-- Categories Section -->
    <section class="py-12">
        <div class="container mx-auto px-4">
            <h2 class="text-2xl font-bold mb-8 text-center">Categorii Populare</h2>

            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-4">
                @foreach($categories as $category)
                <a href="{{ route('products.index', ['category' => $category->id]) }}"
                   class="bg-white rounded-lg shadow-md p-4 text-center hover:shadow-xl transition-shadow group">
                    @if($category->image)
                    <img src="{{ asset($category->image) }}" alt="{{ $category->name }}" class="w-full h-24 object-cover rounded mb-3 group-hover:scale-105 transition-transform">
                    @else
                    <div class="w-full h-24 bg-gray-200 rounded mb-3 flex items-center justify-center">
                        <i class="fas fa-tag text-3xl text-gray-400"></i>
                    </div>
                    @endif
                    <h3 class="font-semibold text-gray-800 group-hover:text-blue-600">{{ $category->name }}</h3>
                    <p class="text-xs text-gray-500">{{ $category->products()->count() }} produse</p>
                </a>
                @endforeach
            </div>
        </div>
    </section>

    <!-- Featured Products -->
    @if($featuredProducts->count() > 0)
    <section class="py-12 bg-gray-100">
        <div class="container mx-auto px-4">
            <div class="flex justify-between items-center mb-8">
                <h2 class="text-2xl font-bold">Produse Recomandate</h2>
                <a href="{{ route('products.index', ['sort' => 'featured']) }}" class="text-blue-600 hover:text-blue-700">
                    Vezi toate <i class="fas fa-arrow-right ml-1"></i>
                </a>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                @foreach($featuredProducts as $product)
                @include('components.product-card', ['product' => $product])
                @endforeach
            </div>
        </div>
    </section>
    @endif

    <!-- New Products -->
    @if($newProducts->count() > 0)
    <section class="py-12">
        <div class="container mx-auto px-4">
            <div class="flex justify-between items-center mb-8">
                <h2 class="text-2xl font-bold">Produse Noi</h2>
                <a href="{{ route('products.index') }}" class="text-blue-600 hover:text-blue-700">
                    Vezi toate <i class="fas fa-arrow-right ml-1"></i>
                </a>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                @foreach($newProducts as $product)
                @include('components.product-card', ['product' => $product])
                @endforeach
            </div>
        </div>
    </section>
    @endif

    <!-- Features Section -->
    <section class="py-12 bg-gray-800 text-white">
        <div class="container mx-auto px-4">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
                <div class="text-center">
                    <i class="fas fa-shipping-fast text-4xl text-blue-400 mb-4"></i>
                    <h3 class="font-semibold mb-2">Livrare Rapidă</h3>
                    <p class="text-gray-400 text-sm">Livrare în 24-48 ore</p>
                </div>
                <div class="text-center">
                    <i class="fas fa-shield-alt text-4xl text-blue-400 mb-4"></i>
                    <h3 class="font-semibold mb-2">Produse Originale</h3>
                    <p class="text-gray-400 text-sm">100% garantate</p>
                </div>
                <div class="text-center">
                    <i class="fas fa-undo text-4xl text-blue-400 mb-4"></i>
                    <h3 class="font-semibold mb-2">Retur Gratuit</h3>
                    <p class="text-gray-400 text-sm">În 14 zile</p>
                </div>
                <div class="text-center">
                    <i class="fas fa-headset text-4xl text-blue-400 mb-4"></i>
                    <h3 class="font-semibold mb-2">Suport 24/7</h3>
                    <p class="text-gray-400 text-sm">La dispoziția ta</p>
                </div>
            </div>
        </div>
    </section>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Slider functionality
    const sliderContainers = document.querySelectorAll('.slider-container');
    sliderContainers.forEach(container => {
        const items = container.querySelectorAll('.slider-item');
        if (items.length > 1) {
            let currentIndex = 0;
            
            // Show first item
            items.forEach((item, index) => {
                item.style.display = index === 0 ? 'block' : 'none';
            });
            
            // Auto-advance slider
            setInterval(() => {
                items[currentIndex].style.display = 'none';
                currentIndex = (currentIndex + 1) % items.length;
                items[currentIndex].style.display = 'block';
            }, 5000);
        }
    });
});
</script>
@endpush