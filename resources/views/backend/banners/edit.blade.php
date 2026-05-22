@extends('layouts.admin')

@section('title', 'Editează Banner - Admin Panel')
@section('page_title', 'Editează Banner')
@section('breadcrumbs')
    <a href="{{ route('admin.dashboard') }}" class="hover:text-slate-700">Admin</a>
    <span class="mx-1">/</span>
    <a href="{{ route('admin.banners.index') }}" class="hover:text-slate-700">Bannere</a>
    <span class="mx-1">/</span>
    <span>Editează</span>
@endsection

@section('content')
<div class="p-6">
    <div class="mb-6">
        <a href="{{ route('admin.banners.index') }}" class="text-blue-600 hover:text-blue-700">
            Înapoi la bannere
        </a>
    </div>
    
    <h1 class="text-2xl font-bold mb-6">Editează Banner</h1>
    
    @if ($errors->any())
    <div class="mb-6 p-4 bg-red-100 border border-red-400 text-red-700 rounded">
        <p class="font-bold">Erori:</p>
        <ul class="list-disc ml-5">
            @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif
    
    <form method="POST" action="{{ route('admin.banners.update', $banner->id) }}" enctype="multipart/form-data" class="bg-white rounded-lg shadow p-6">
        @csrf
        @method('PUT')
        
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Titlu *</label>
                    <input type="text" name="title" value="{{ old('title', $banner->title) }}" required
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                    @error('title')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Imagine</label>
                    <input type="file" name="image" accept="image/*"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg">
                    @if($banner->image_path)
                    <div class="mt-2">
                        <img src="{{ asset($banner->image_path) }}" alt="{{ $banner->title }}" class="w-32 h-32 object-cover rounded">
                    </div>
                    @endif
                    @error('image')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                    <p class="text-xs text-gray-500 mt-1">Lăsați gol pentru a păstra imaginea actuală. Format acceptat: JPEG, PNG, JPG, GIF, WebP. Max 2MB.</p>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Link URL</label>
                    <input type="url" name="link_url" value="{{ old('link_url', $banner->link_url) }}"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                    @error('link_url')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Poziție *</label>
                    <select name="position" required class="w-full px-4 py-2 border border-gray-300 rounded-lg">
                        <option value="slider" {{ old('position', $banner->position) == 'slider' ? 'selected' : '' }}>Slider (Carousel)</option>
                        <option value="top" {{ old('position', $banner->position) == 'top' ? 'selected' : '' }}>Top (Sus)</option>
                        <option value="middle" {{ old('position', $banner->position) == 'middle' ? 'selected' : '' }}>Middle (Mijloc)</option>
                        <option value="bottom" {{ old('position', $banner->position) == 'bottom' ? 'selected' : '' }}>Bottom (Jos)</option>
                        <option value="footer" {{ old('position', $banner->position) == 'footer' ? 'selected' : '' }}>Footer (Subsol)</option>
                    </select>
                    @error('position')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Dimensiune *</label>
                    <select name="size" required class="w-full px-4 py-2 border border-gray-300 rounded-lg">
                        <option value="small" {{ old('size', $banner->size) == 'small' ? 'selected' : '' }}>Mică (25% lățime)</option>
                        <option value="medium" {{ old('size', $banner->size) == 'medium' ? 'selected' : '' }}>Medie (50% lățime)</option>
                        <option value="large" {{ old('size', $banner->size) == 'large' ? 'selected' : '' }}>Mare (75% lățime)</option>
                        <option value="full" {{ old('size', $banner->size) == 'full' ? 'selected' : '' }}>Completă (100% lățime)</option>
                    </select>
                    @error('size')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Înălțime *</label>
                    <select name="height" required class="w-full px-4 py-2 border border-gray-300 rounded-lg">
                        <option value="small" {{ old('height', $banner->height) == 'small' ? 'selected' : '' }}>Mică (200px)</option>
                        <option value="medium" {{ old('height', $banner->height) == 'medium' ? 'selected' : '' }}>Medie (300px)</option>
                        <option value="large" {{ old('height', $banner->height) == 'large' ? 'selected' : '' }}>Mare (400px)</option>
                        <option value="xlarge" {{ old('height', $banner->height) == 'xlarge' ? 'selected' : '' }}>Extra Mare (500px)</option>
                        <option value="full" {{ old('height', $banner->height) == 'full' ? 'selected' : '' }}>Completă (100% înălțime ecran)</option>
                    </select>
                    @error('height')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Ordine Sortare</label>
                    <input type="number" name="sort_order" value="{{ old('sort_order', $banner->sort_order) }}" min="0"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                    @error('sort_order')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>
            
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Data Început</label>
                    <input type="date" name="start_date" value="{{ old('start_date', $banner->start_date ? $banner->start_date->format('Y-m-d') : '') }}"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                    @error('start_date')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                    <p class="text-xs text-gray-500 mt-1">Lăsați gol pentru a fi activ imediat</p>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Data Sfârșit</label>
                    <input type="date" name="end_date" value="{{ old('end_date', $banner->end_date ? $banner->end_date->format('Y-m-d') : '') }}"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                    @error('end_date')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                    <p class="text-xs text-gray-500 mt-1">Lăsați gol pentru a fi activ permanent</p>
                </div>
                
                <div class="pt-4">
                    <label class="flex items-center">
                        <input type="hidden" name="is_active" value="0">
                        <input type="checkbox" name="is_active" value="1" class="mr-2"
                               {{ old('is_active', $banner->is_active) ? 'checked' : '' }}>
                        <span class="text-sm font-medium text-gray-700">Banner activ</span>
                    </label>
                </div>
            </div>
        </div>
        
        <div class="mt-6 flex justify-end gap-4">
            <a href="{{ route('admin.banners.index') }}" class="px-6 py-2 border border-gray-300 rounded-lg hover:bg-gray-50">
                Anulează
            </a>
            <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700">
                Salvează Modificările
            </button>
        </div>
    </form>
</div>
@endsection
