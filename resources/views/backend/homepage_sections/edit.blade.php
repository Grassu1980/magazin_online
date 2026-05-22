@extends('layouts.admin')

@section('title', 'Editează Secțiune - Admin Panel')
@section('page_title', 'Editează Secțiune')
@section('breadcrumbs')
    <a href="{{ route('admin.dashboard') }}" class="hover:text-slate-700">Admin</a>
    <span class="mx-1">/</span>
    <a href="{{ route('admin.homepage-sections.index') }}" class="hover:text-slate-700">Secțiuni Homepage</a>
    <span class="mx-1">/</span>
    <span>Editează</span>
@endsection

@section('content')
<div class="p-6">
    <div class="mb-6">
        <a href="{{ route('admin.homepage-sections.index') }}" class="text-blue-600 hover:text-blue-700">
            Înapoi la secțiuni
        </a>
    </div>
    
    <h1 class="text-2xl font-bold mb-6">Editează Secțiune</h1>
    
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
    
    <form method="POST" action="{{ route('admin.homepage-sections.update', $section->id) }}" class="bg-white rounded-lg shadow p-6">
        @csrf
        @method('PUT')
        
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Tip Secțiune *</label>
                    <select name="type" id="section-type" required class="w-full px-4 py-2 border border-gray-300 rounded-lg" onchange="showConfigFields()">
                        <option value="slider" {{ old('type', $section->type) == 'slider' ? 'selected' : '' }}>Slider Bannere</option>
                        <option value="banners_row" {{ old('type', $section->type) == 'banners_row' ? 'selected' : '' }}>Rând Bannere</option>
                        <option value="products_grid" {{ old('type', $section->type) == 'products_grid' ? 'selected' : '' }}>Grid Produse</option>
                        <option value="categories_grid" {{ old('type', $section->type) == 'categories_grid' ? 'selected' : '' }}>Grid Categorii</option>
                        <option value="custom_html" {{ old('type', $section->type) == 'custom_html' ? 'selected' : '' }}>HTML Custom</option>
                        <option value="video" {{ old('type', $section->type) == 'video' ? 'selected' : '' }}>Video</option>
                        <option value="text_block" {{ old('type', $section->type) == 'text_block' ? 'selected' : '' }}>Bloc Text</option>
                    </select>
                    @error('type')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Titlu</label>
                    <input type="text" name="title" value="{{ old('title', $section->title) }}"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                    @error('title')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Ordine Sortare</label>
                    <input type="number" name="sort_order" value="{{ old('sort_order', $section->sort_order) }}" min="0"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                    @error('sort_order')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
                
                <div class="pt-4">
                    <label class="flex items-center">
                        <input type="hidden" name="is_active" value="0">
                        <input type="checkbox" name="is_active" value="1" class="mr-2"
                               {{ old('is_active', $section->is_active) ? 'checked' : '' }}>
                        <span class="text-sm font-medium text-gray-700">Secțiune activă</span>
                    </label>
                </div>
            </div>
            
            <div class="space-y-4">
                <!-- Config fields based on type -->
                <div id="config-slider" class="config-field">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Poziție Bannere</label>
                    <select name="config[position]" class="w-full px-4 py-2 border border-gray-300 rounded-lg">
                        <option value="slider" {{ old('config.position', $section->config['position'] ?? '') == 'slider' ? 'selected' : '' }}>Slider (Carousel)</option>
                        <option value="top" {{ old('config.position', $section->config['position'] ?? '') == 'top' ? 'selected' : '' }}>Top (Sus)</option>
                        <option value="middle" {{ old('config.position', $section->config['position'] ?? '') == 'middle' ? 'selected' : '' }}>Middle (Mijloc)</option>
                        <option value="bottom" {{ old('config.position', $section->config['position'] ?? '') == 'bottom' ? 'selected' : '' }}>Bottom (Jos)</option>
                        <option value="footer" {{ old('config.position', $section->config['position'] ?? '') == 'footer' ? 'selected' : '' }}>Footer (Subsol)</option>
                    </select>
                </div>
                
                <div id="config-banners_row" class="config-field hidden">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Poziție Bannere</label>
                    <select name="config[position]" class="w-full px-4 py-2 border border-gray-300 rounded-lg">
                        <option value="top" {{ old('config.position', $section->config['position'] ?? '') == 'top' ? 'selected' : '' }}>Top (Sus)</option>
                        <option value="middle" {{ old('config.position', $section->config['position'] ?? '') == 'middle' ? 'selected' : '' }}>Middle (Mijloc)</option>
                        <option value="bottom" {{ old('config.position', $section->config['position'] ?? '') == 'bottom' ? 'selected' : '' }}>Bottom (Jos)</option>
                        <option value="footer" {{ old('config.position', $section->config['position'] ?? '') == 'footer' ? 'selected' : '' }}>Footer (Subsol)</option>
                    </select>
                </div>
                
                <div id="config-products_grid" class="config-field hidden">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Număr Produse</label>
                    <input type="number" name="config[limit]" value="{{ old('config.limit', $section->config['limit'] ?? 8) }}" min="1" max="20"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg">
                    
                    <label class="block text-sm font-medium text-gray-700 mb-1 mt-4">Produse Specifice (opțional)</label>
                    <select name="config[product_ids][]" multiple class="w-full px-4 py-2 border border-gray-300 rounded-lg h-32">
                        @foreach($products as $product)
                        <option value="{{ $product->id }}" {{ in_array($product->id, $section->config['product_ids'] ?? []) ? 'selected' : '' }}>{{ $product->name }}</option>
                        @endforeach
                    </select>
                    <p class="text-xs text-gray-500 mt-1">Lăsați gol pentru a afișa produsele recomandate</p>
                </div>
                
                <div id="config-categories_grid" class="config-field hidden">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Număr Categorii</label>
                    <input type="number" name="config[limit]" value="{{ old('config.limit', $section->config['limit'] ?? 6) }}" min="1" max="12"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg">
                    
                    <label class="block text-sm font-medium text-gray-700 mb-1 mt-4">Categorii Specifice (opțional)</label>
                    <select name="config[category_ids][]" multiple class="w-full px-4 py-2 border border-gray-300 rounded-lg h-32">
                        @foreach($categories as $category)
                        <option value="{{ $category->id }}" {{ in_array($category->id, $section->config['category_ids'] ?? []) ? 'selected' : '' }}>{{ $category->name }}</option>
                        @endforeach
                    </select>
                    <p class="text-xs text-gray-500 mt-1">Lăsați gol pentru a afișa toate categoriile active</p>
                </div>
                
                <div id="config-custom_html" class="config-field hidden">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Conținut HTML</label>
                    <textarea name="config[html_content]" rows="10"
                              class="w-full px-4 py-2 border border-gray-300 rounded-lg font-mono text-sm">{{ old('config.html_content', $section->config['html_content'] ?? '') }}</textarea>
                </div>
                
                <div id="config-video" class="config-field hidden">
                    <label class="block text-sm font-medium text-gray-700 mb-1">URL Video</label>
                    <input type="url" name="config[video_url]" value="{{ old('config.video_url', $section->config['video_url'] ?? '') }}"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg">
                    
                    <label class="block text-sm font-medium text-gray-700 mb-1 mt-4">Titlu Video</label>
                    <input type="text" name="config[video_title]" value="{{ old('config.video_title', $section->config['video_title'] ?? '') }}"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg">
                    
                    <label class="block text-sm font-medium text-gray-700 mb-1 mt-4">Descriere Video</label>
                    <textarea name="config[video_description]" rows="3"
                              class="w-full px-4 py-2 border border-gray-300 rounded-lg">{{ old('config.video_description', $section->config['video_description'] ?? '') }}</textarea>
                </div>
                
                <div id="config-text_block" class="config-field hidden">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Conținut Text</label>
                    <textarea name="config[text_content]" rows="5"
                              class="w-full px-4 py-2 border border-gray-300 rounded-lg">{{ old('config.text_content', $section->config['text_content'] ?? '') }}</textarea>
                    
                    <label class="block text-sm font-medium text-gray-700 mb-1 mt-4">Culoare Fundal</label>
                    <input type="color" name="config[background_color]" value="{{ old('config.background_color', $section->config['background_color'] ?? '#ffffff') }}"
                           class="w-full h-10 px-4 py-2 border border-gray-300 rounded-lg">
                    
                    <label class="block text-sm font-medium text-gray-700 mb-1 mt-4">Culoare Text</label>
                    <input type="color" name="config[text_color]" value="{{ old('config.text_color', $section->config['text_color'] ?? '#000000') }}"
                           class="w-full h-10 px-4 py-2 border border-gray-300 rounded-lg">
                </div>
            </div>
        </div>
        
        <div class="mt-6 flex justify-end gap-4">
            <a href="{{ route('admin.homepage-sections.index') }}" class="px-6 py-2 border border-gray-300 rounded-lg hover:bg-gray-50">
                Anulează
            </a>
            <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700">
                Salvează Modificările
            </button>
        </div>
    </form>
</div>

<script>
function showConfigFields() {
    const type = document.getElementById('section-type').value;
    document.querySelectorAll('.config-field').forEach(field => {
        field.classList.add('hidden');
    });
    const activeField = document.getElementById('config-' + type);
    if (activeField) {
        activeField.classList.remove('hidden');
    }
}

document.addEventListener('DOMContentLoaded', function() {
    showConfigFields();
});
</script>
@endsection
