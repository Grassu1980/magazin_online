@extends('layouts.app')

@section('title', 'Produse - Magazin Online')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="flex flex-col lg:flex-row gap-8">
        <!-- Sidebar Filters -->
        <aside class="lg:w-1/4">
            <div class="bg-white rounded-lg shadow-md p-6">
                <h3 class="text-lg font-semibold mb-4">Filtre</h3>
                
                <!-- Search -->
                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Căutare</label>
                    <form action="{{ route('products.index') }}" method="GET">
                        <input type="text" name="search" value="{{ request('search') }}" placeholder="Caută..." 
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                </div>
                
                <!-- Categories -->
                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Categorie</label>
                    <div class="space-y-2">
                        <label class="flex items-center">
                            <input type="radio" name="category" value="" {{ !request('category') ? 'checked' : '' }} 
                                   onchange="this.form.submit()" class="mr-2">
                            Toate categoriile
                        </label>
                        @foreach($categories as $category)
                        <label class="flex items-center">
                            <input type="radio" name="category" value="{{ $category->id }}" 
                                   {{ request('category') == $category->id ? 'checked' : '' }}
                                   onchange="this.form.submit()" class="mr-2">
                            {{ $category->name }}
                        </label>
                        @endforeach
                    </div>
                </div>
                
                <!-- Price Range -->
                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Preț</label>
                    <div class="flex gap-2">
                        <input type="number" name="min_price" value="{{ request('min_price') }}" placeholder="Min" 
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg">
                        <input type="number" name="max_price" value="{{ request('max_price') }}" placeholder="Max" 
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg">
                    </div>
                    <button type="submit" class="w-full mt-2 bg-blue-600 text-white py-2 rounded-lg hover:bg-blue-700">
                        Aplică
                    </button>
                    </form>
                </div>
                
                <!-- Clear Filters -->
                @if(request()->anyFilled(['search', 'category', 'min_price', 'max_price']))
                <a href="{{ route('products.index') }}" class="block text-center text-red-600 hover:text-red-700">
                    <i class="fas fa-times mr-1"></i>Șterge filtrele
                </a>
                @endif
            </div>
        </aside>
        
        <!-- Products Grid -->
        <div class="lg:w-3/4">
            <!-- Sort & Results Info -->
            <div class="flex flex-col sm:flex-row justify-between items-center mb-6 gap-4">
                <p class="text-gray-600">S-au găsit {{ $products->total() }} produse</p>
                
                <form action="{{ route('products.index') }}" method="GET" class="flex items-center gap-2">
                    @foreach(request()->except('sort') as $key => $value)
                    <input type="hidden" name="{{ $key }}" value="{{ $value }}">
                    @endforeach
                    <label class="text-sm text-gray-600">Sortare:</label>
                    <select name="sort" onchange="this.form.submit()" class="border border-gray-300 rounded-lg px-3 py-2">
                        <option value="latest" {{ request('sort') == 'latest' ? 'selected' : '' }}>Cele mai noi</option>
                        <option value="price_asc" {{ request('sort') == 'price_asc' ? 'selected' : '' }}>Preț crescător</option>
                        <option value="price_desc" {{ request('sort') == 'price_desc' ? 'selected' : '' }}>Preț descrescător</option>
                        <option value="popular" {{ request('sort') == 'popular' ? 'selected' : '' }}>Cele mai vândute</option>
                        <option value="name" {{ request('sort') == 'name' ? 'selected' : '' }}>Nume A-Z</option>
                    </select>
                </form>
            </div>
            
            <!-- Products -->
            @if($products->count() > 0)
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($products as $product)
                @include('components.product-card', ['product' => $product])
                @endforeach
            </div>
            
            <!-- Pagination -->
            <div class="mt-8">
                {{ $products->links() }}
            </div>
            @else
            <div class="text-center py-12">
                <i class="fas fa-search text-6xl text-gray-300 mb-4"></i>
                <h3 class="text-xl font-semibold text-gray-600">Nu s-au găsit produse</h3>
                <p class="text-gray-500">Încearcă să modifici filtrele de căutare</p>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection