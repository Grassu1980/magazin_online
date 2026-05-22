@extends('layouts.admin')

@section('title', $category->name . ' - Admin Panel')

@section('content')
@php
    $children = collect($category->children ?? []);
@endphp
<div class="p-6">
    <div class="mb-6 flex justify-between items-center">
        <a href="{{ route('admin.categories.index') }}" class="text-blue-600 hover:text-blue-700">
            <i class="fas fa-arrow-left mr-1"></i>Înapoi la categorii
        </a>
        <div class="flex gap-2">
            <a href="{{ route('admin.categories.edit', $category->id) }}" class="px-4 py-2 bg-yellow-600 text-white rounded-lg hover:bg-yellow-700">
                <i class="fas fa-edit mr-2"></i>Editează
            </a>
            <form action="{{ route('admin.categories.destroy', $category->id) }}" method="POST" class="inline">
                @csrf
                @method('DELETE')
                <button type="submit" class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700" onclick="return confirm('Sigur dorești să ștergi această categorie?')">
                    <i class="fas fa-trash mr-2"></i>Șterge
                </button>
            </form>
        </div>
    </div>
    
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Category Info -->
        <div class="lg:col-span-2 space-y-6">
            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex justify-between items-start mb-4">
                    <div>
                        <h1 class="text-2xl font-bold">{{ $category->name }}</h1>
                        <p class="text-gray-500">Slug: {{ $category->slug }}</p>
                    </div>
                    <div class="flex gap-2">
                        <span class="px-3 py-1 rounded-full text-sm font-medium {{ $category->is_active ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-700' }}">
                            {{ $category->is_active ? 'Activă' : 'Inactivă' }}
                        </span>
                        @if($category->is_featured)
                        <span class="px-3 py-1 rounded-full text-sm font-medium bg-yellow-100 text-yellow-700">
                            În Homepage
                        </span>
                        @endif
                    </div>
                </div>
                
                @if($category->parent)
                <div class="mb-4">
                    <p class="text-sm text-gray-500">Categorie Părinte</p>
                    <a href="{{ route('admin.categories.show', $category->parent->id) }}" class="font-medium text-blue-600 hover:text-blue-700">
                        {{ $category->parent->name }}
                    </a>
                </div>
                @endif
                
                @if($category->description)
                <div>
                    <p class="text-sm text-gray-500">Descriere</p>
                    <p>{{ $category->description }}</p>
                </div>
                @endif
            </div>
            
            <!-- Subcategories -->
            @if($children->count() > 0)
            <div class="bg-white rounded-lg shadow p-6">
                <h2 class="font-semibold mb-4">Subcategorii</h2>
                <div class="space-y-2">
                    @foreach($children as $child)
                    <a href="{{ route('admin.categories.show', $child->id) }}" class="flex items-center justify-between p-3 border rounded-lg hover:bg-gray-50">
                        <div class="flex items-center gap-3">
                            @if($child->image)
                            <img src="{{ asset($child->image) }}" alt="{{ $child->name }}" class="w-8 h-8 object-cover rounded">
                            @else
                            <div class="w-8 h-8 bg-gray-200 rounded flex items-center justify-center">
                                <i class="fas fa-folder text-gray-400 text-xs"></i>
                            </div>
                            @endif
                            <span>{{ $child->name }}</span>
                        </div>
                        <span class="text-sm text-gray-500">{{ $child->products->count() }} produse</span>
                    </a>
                    @endforeach
                </div>
            </div>
            @endif
            
            <!-- Products in Category -->
            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex justify-between items-center mb-4">
                    <h2 class="font-semibold">Produse în Categorie</h2>
                    <a href="{{ route('admin.products.index') }}?category={{ $category->id }}" class="text-sm text-blue-600 hover:text-blue-700">
                        Vezi toate
                    </a>
                </div>
                @if($category->products->count() > 0)
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                    @foreach($category->products->take(8) as $product)
                    <a href="{{ route('admin.products.show', $product->id) }}" class="border rounded-lg p-2 hover:bg-gray-50">
                        @if($product->image)
                        <img src="{{ asset($product->image) }}" alt="{{ $product->name }}" class="w-full h-20 object-cover rounded mb-2">
                        @else
                        <div class="w-full h-20 bg-gray-200 rounded mb-2 flex items-center justify-center">
                            <i class="fas fa-image text-gray-400"></i>
                        </div>
                        @endif
                        <p class="text-sm font-medium truncate">{{ $product->name }}</p>
                        <p class="text-xs text-gray-500">{{ number_format($product->price, 2) }} lei</p>
                    </a>
                    @endforeach
                </div>
                @if($category->products->count() > 8)
                <p class="text-center text-sm text-gray-500 mt-4">+ {{ $category->products->count() - 8 }} produse</p>
                @endif
                @else
                <p class="text-gray-500 text-center py-4">Nu există produse în această categorie</p>
                @endif
            </div>
        </div>
        
        <!-- Sidebar -->
        <div class="space-y-6">
            <div class="bg-white rounded-lg shadow p-6">
                <h2 class="font-semibold mb-4">Imagine</h2>
                @if($category->image)
                <img src="{{ asset($category->image) }}" alt="{{ $category->name }}" class="w-full rounded-lg">
                @else
                <div class="w-full h-48 bg-gray-200 rounded-lg flex items-center justify-center">
                    <i class="fas fa-image text-4xl text-gray-400"></i>
                </div>
                @endif
            </div>
            
            <div class="bg-white rounded-lg shadow p-6">
                <h2 class="font-semibold mb-4">Statistici</h2>
                <div class="space-y-3">
                    <div class="flex justify-between">
                        <span class="text-gray-600">Total Produse</span>
                        <span class="font-semibold">{{ $category->products->count() }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Subcategorii</span>
                        <span class="font-semibold">{{ $children->count() }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Ordine</span>
                        <span class="font-semibold">{{ $category->sort_order }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection