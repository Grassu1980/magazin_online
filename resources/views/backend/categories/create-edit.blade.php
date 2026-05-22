@extends('layouts.admin')

@section('title', $category ? 'Editează Categorie' : 'Categorie Nouă - Admin Panel')
@section('page_title', $category ? 'Editează categorie' : 'Categorie nouă')
@section('breadcrumbs')
    <a href="{{ route('admin.dashboard') }}" class="hover:text-slate-700">Admin</a>
    <span class="mx-1">/</span>
    <a href="{{ route('admin.categories.index') }}" class="hover:text-slate-700">Categorii</a>
    <span class="mx-1">/</span>
    <span>{{ $category ? 'Editează' : 'Nouă' }}</span>
@endsection

@section('content')
<div class="space-y-6">
    <div class="mb-6">
        <a href="{{ route('admin.categories.index') }}" class="text-blue-600 hover:text-blue-700">
            Înapoi la categorii
        </a>
    </div>
    
    <h1 class="text-2xl font-bold mb-6">{{ $category ? 'Editează Categorie' : 'Categorie Nouă' }}</h1>
    
    <form method="POST" 
          action="{{ $category ? route('admin.categories.update', $category->id) : route('admin.categories.store') }}" 
          enctype="multipart/form-data" 
          class="max-w-2xl">
          
        @csrf
        @if($category)
            @method('PUT')
        @endif
        
        <div class="bg-white rounded-xl border border-slate-200 p-6">
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">Nume Categorie *</label>
                <input type="text" name="name" value="{{ old('name', $category->name ?? '') }}" required
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                @error('name')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">Slug</label>
                <input type="text" name="slug" value="{{ old('slug', $category->slug ?? '') }}"
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
            </div>

            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">Descriere</label>
                <textarea name="description" rows="4"
                          class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">{{ old('description', $category->description ?? '') }}</textarea>
            </div>

            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">Imagine</label>
                <input type="file" name="image" accept="image/*"
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg">

                @if($category && $category->image)
                    <div class="mt-2">
                        <img src="{{ asset($category->image) }}" alt="{{ $category->name }}" 
                             class="w-32 h-32 object-cover rounded">
                        <label class="flex items-center mt-2">
                            <input type="checkbox" name="remove_image" class="mr-2">
                            <span class="text-sm text-red-600">Șterge imaginea</span>
                        </label>
                    </div>
                @endif
            </div>

            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">Ordine Afișare</label>
                <input type="number" name="sort_order" 
                       value="{{ old('sort_order', $category->sort_order ?? 0) }}"
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
            </div>

            <div class="flex gap-4 mt-4">
                <label class="flex items-center">
                    <input type="hidden" name="is_active" value="0">
                    <input type="checkbox" name="is_active" value="1"
                           {{ old('is_active', $category->is_active ?? true) ? 'checked' : '' }}
                           class="mr-2">
                    <span class="text-sm font-medium text-gray-700">Categorie activă</span>
                </label>
            </div>

        </div>

        <div class="mt-6">
            <button type="submit" 
                    class="px-6 py-2 bg-blue-600 text-white rounded-lg font-semibold hover:bg-blue-700">
                {{ $category ? 'Salvează Modificările' : 'Creează Categorie' }}
            </button>

            <a href="{{ route('admin.categories.index') }}" 
               class="ml-4 px-6 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50">
                Anulează
            </a>
        </div>

    </form>
</div>
@endsection
