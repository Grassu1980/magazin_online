@extends('layouts.admin')

@php $product = $product ?? null; @endphp

@section('title', $product ? 'Editează Produs' : 'Produs Nou - Admin Panel')
@section('page_title', $product ? 'Editează produs' : 'Produs nou')
@section('breadcrumbs')
    <a href="{{ route('admin.dashboard') }}" class="hover:text-slate-700">Admin</a>
    <span class="mx-1">/</span>
    <a href="{{ route('admin.products.index') }}" class="hover:text-slate-700">Produse</a>
    <span class="mx-1">/</span>
    <span>{{ $product ? 'Editează' : 'Nou' }}</span>
@endsection

@section('content')
<div class="p-6">
    <div class="mb-6">
        <a href="{{ route('admin.products.index') }}" class="text-blue-600 hover:text-blue-700">
            Înapoi la produse
        </a>
    </div>
    
    <h1 class="text-2xl font-bold mb-6">{{ $product ? 'Editează Produs' : 'Produs Nou' }}</h1>
    
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
    
    <form method="POST" action="{{ $product ? route('admin.products.update', $product->id) : route('admin.products.store') }}"
          enctype="multipart/form-data" class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        @csrf
        @if($product)
        @method('PUT')
        @endif
        
        <!-- Main Info -->
        <div class="lg:col-span-2 space-y-6">
            <div class="bg-white rounded-lg shadow p-6">
                <h2 class="font-semibold mb-4">Informații Produs</h2>
                
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Nume Produs *</label>
                    <input type="text" name="name" value="{{ old('name', $product->name ?? '') }}" required
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                    @error('name')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
                
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Slug</label>
                    <input type="text" name="slug" value="{{ old('slug', $product->slug ?? '') }}"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                    @error('slug')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
                
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">SKU</label>
                    <input type="text" name="sku" value="{{ old('sku', $product->sku ?? '') }}"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                    @error('sku')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
                
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Descriere</label>
                    <textarea name="description" rows="6" 
                              class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">{{ old('description', $product->description ?? '') }}</textarea>
                </div>
            </div>
            
            <!-- Images -->
            <div class="bg-white rounded-lg shadow p-6">
                <h2 class="font-semibold mb-4">Imagini</h2>
                
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Imagine Principală</label>
                    <input type="file" name="image" accept="image/*" 
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg">
                    @if($product && $product->image)
                    <div class="mt-2">
                        <img src="{{ asset($product->image) }}" alt="{{ $product->name }}" class="w-32 h-32 object-cover rounded">
                        <label class="flex items-center mt-2">
                            <input type="checkbox" name="remove_image" class="mr-2">
                            <span class="text-sm text-red-600">Șterge imaginea</span>
                        </label>
                    </div>
                    @endif
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Galerie Imagini</label>
                    <input type="file" name="images[]" accept="image/*" multiple
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg">
                    @if($product && $product->images->count() > 0)
                    <div class="mt-2 flex flex-wrap gap-2">
                        @foreach($product->images as $image)
                        <div class="relative">
                            <img src="{{ asset($image->image_path) }}" class="w-20 h-20 object-cover rounded">
                            <button type="button" onclick="removeImage({{ $image->id }})" 
                                    class="absolute -top-2 -right-2 w-6 h-6 bg-red-600 text-white rounded-full text-xs">
                                <svg class="w-4 h-4 mx-auto" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 6l12 12M18 6 6 18" />
                                </svg>
                            </button>
                        </div>
                        @endforeach
                    </div>
                    @endif
                </div>
            </div>
        </div>
        
        <!-- Sidebar -->
        <div class="space-y-6">
            <!-- Pricing -->
            <div class="bg-white rounded-lg shadow p-6">
                <h2 class="font-semibold mb-4">Prețuri</h2>
                
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Preț Achiziție (fără TVA)</label>
                    <div class="relative">
                        <input type="number" name="purchase_price_without_vat" step="0.01" value="{{ old('purchase_price_without_vat', $product->purchase_price_without_vat ?? '') }}" readonly
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg pr-12 bg-gray-100">
                        <span class="absolute right-4 top-1/2 -translate-y-1/2 text-gray-500">lei</span>
                    </div>
                    <p class="text-xs text-gray-500 mt-1">Prețul din NIR (readonly)</p>
                </div>

                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Preț Fără TVA</label>
                    <div class="relative">
                        <input type="number" name="price_without_vat" id="price_without_vat" step="0.01" value="{{ old('price_without_vat', $product->price_without_vat ?? '') }}"
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg pr-12">
                        <span class="absolute right-4 top-1/2 -translate-y-1/2 text-gray-500">lei</span>
                    </div>
                </div>

                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Preț Cu TVA</label>
                    <div class="relative">
                        <input type="number" name="price_with_vat" id="price_with_vat" step="0.01" value="{{ old('price_with_vat', $product->price_with_vat ?? '') }}"
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg pr-12">
                        <span class="absolute right-4 top-1/2 -translate-y-1/2 text-gray-500">lei</span>
                    </div>
                </div>

                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Adaos Comercial (%)</label>
                    <div class="relative">
                        <input type="number" name="markup_percentage" id="markup_percentage" step="0.01" value="{{ old('markup_percentage', $product ? $product->markup_percentage : '') }}"
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg pr-12">
                        <span class="absolute right-4 top-1/2 -translate-y-1/2 text-gray-500">%</span>
                    </div>
                </div>

                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Profit Brut</label>
                    <div class="relative">
                        <input type="number" id="gross_profit" step="0.01" value="{{ $product ? $product->gross_profit : 0 }}" readonly
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg pr-12 bg-gray-100">
                        <span class="absolute right-4 top-1/2 -translate-y-1/2 text-gray-500">lei</span>
                    </div>
                </div>

                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Cota TVA *</label>
                    <select name="vat_rate" id="vat_rate" required class="w-full px-4 py-2 border border-gray-300 rounded-lg">
                        <option value="0" {{ old('vat_rate', $product->vat_rate ?? 19) == 0 ? 'selected' : '' }}>0% TVA</option>
                        <option value="11" {{ old('vat_rate', $product->vat_rate ?? 19) == 11 ? 'selected' : '' }}>11% TVA</option>
                        <option value="21" {{ old('vat_rate', $product->vat_rate ?? 19) == 21 ? 'selected' : '' }}>21% TVA</option>
                    </select>
                    @error('vat_rate')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="border-t pt-4 mt-4">
                    <h3 class="font-semibold mb-3">Promoție</h3>
                    
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Preț Promoțional</label>
                        <div class="relative">
                            <input type="number" name="promo_price" step="0.01" value="{{ old('promo_price', $product->promo_price ?? '') }}"
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg pr-12">
                            <span class="absolute right-4 top-1/2 -translate-y-1/2 text-gray-500">lei</span>
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Data Început Promoție</label>
                        <input type="datetime-local" name="promo_start" value="{{ old('promo_start', $product && $product->promo_start ? $product->promo_start->format('Y-m-d\TH:i') : '') }}"
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg">
                    </div>

                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Data Sfârșit Promoție</label>
                        <input type="datetime-local" name="promo_end" value="{{ old('promo_end', $product && $product->promo_end ? $product->promo_end->format('Y-m-d\TH:i') : '') }}"
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg">
                    </div>
                </div>

                @if($product && $product->priceHistory->count() > 0)
                <div class="border-t pt-4 mt-4">
                    <h3 class="font-semibold mb-3">Istoric Prețuri</h3>
                    <div class="overflow-x-auto">
                        <table class="min-w-full text-xs">
                            <thead>
                                <tr class="border-b">
                                    <th class="text-left py-2">Vechi</th>
                                    <th class="text-left py-2">Nou</th>
                                    <th class="text-left py-2">TVA</th>
                                    <th class="text-left py-2">Data</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($product->priceHistory->take(5) as $history)
                                <tr class="border-b">
                                    <td class="py-2">{{ number_format($history->old_price_with_vat, 2) }} lei</td>
                                    <td class="py-2">{{ number_format($history->new_price_with_vat, 2) }} lei</td>
                                    <td class="py-2">{{ $history->new_vat_rate }}%</td>
                                    <td class="py-2">{{ $history->created_at->format('d.m.Y H:i') }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                @endif
            </div>
            
            <!-- Inventory -->
            <div class="bg-white rounded-lg shadow p-6">
                <h2 class="font-semibold mb-4">Stoc</h2>
                
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Cantitate în Stoc *</label>
                    <input type="number" name="stock" value="{{ old('stock', $product->stock ?? 0) }}" required min="0"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg">
                    @error('stock')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
                
            </div>
            
            <!-- Organization -->
            <div class="bg-white rounded-lg shadow p-6">
                <h2 class="font-semibold mb-4">Organizare</h2>
                
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Categorie *</label>
                    <select name="category_id" required class="w-full px-4 py-2 border border-gray-300 rounded-lg">
                        <option value="">Selectează categorie</option>
                        @foreach($categories as $category)
                        <option value="{{ $category->id }}" {{ old('category_id', $product->category_id ?? '') == $category->id ? 'selected' : '' }}>
                            {{ $category->name }}
                        </option>
                        @endforeach
                    </select>
                    @error('category_id')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
                
                <div class="space-y-3">
                    <label class="flex items-center">
                        <input type="hidden" name="is_active" value="0">
                        <input type="checkbox" name="is_active" value="1" class="mr-2"
                               {{ old('is_active', $product->is_active ?? false) ? 'checked' : '' }}>
                        <span class="text-sm font-medium text-gray-700">Produs activ</span>
                    </label>

                    <label class="flex items-center">
                        <input type="hidden" name="is_featured" value="0">
                        <input type="checkbox" name="is_featured" value="1" class="mr-2"
                               {{ old('is_featured', $product->is_featured ?? false) ? 'checked' : '' }}>
                        <span class="text-sm font-medium text-gray-700">Produs recomandat</span>
                    </label>
                </div>
            </div>
            
            <button type="submit" class="w-full bg-blue-600 text-white py-3 rounded-lg font-semibold hover:bg-blue-700">
                {{ $product ? 'Salvează Modificările' : 'Creează Produs' }}
            </button>
        </div>
    </form>
</div>

<script>
function removeImage(imageId) {
    if (confirm('Sigur dorești să ștergi această imagine?')) {
        fetch('{{ route("admin.products.deleteImage", ":id") }}'.replace(':id', imageId), {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            }
        });
    }
}

// Price calculation logic
document.addEventListener('DOMContentLoaded', function() {
    const priceWithoutVat = document.getElementById('price_without_vat');
    const priceWithVat = document.getElementById('price_with_vat');
    const markupPercentage = document.getElementById('markup_percentage');
    const grossProfit = document.getElementById('gross_profit');
    const vatRate = document.getElementById('vat_rate');
    const purchasePriceWithoutVat = document.querySelector('input[name="purchase_price_without_vat"]');

    function calculatePrices() {
        const vatRateValue = parseFloat(vatRate.value) || 0;
        const purchasePrice = parseFloat(purchasePriceWithoutVat.value) || 0;
        
        // Calculate from price without VAT
        if (priceWithoutVat.value) {
            const priceWithoutVatValue = parseFloat(priceWithoutVat.value) || 0;
            const priceWithVatValue = priceWithoutVatValue * (1 + vatRateValue / 100);
            priceWithVat.value = priceWithVatValue.toFixed(2);
            
            // Calculate markup
            if (purchasePrice > 0) {
                const markup = ((priceWithoutVatValue - purchasePrice) / purchasePrice) * 100;
                markupPercentage.value = markup.toFixed(2);
            }
            
            // Calculate gross profit
            grossProfit.value = (priceWithoutVatValue - purchasePrice).toFixed(2);
        }
        
        // Calculate from price with VAT
        if (priceWithVat.value && !priceWithoutVat.value) {
            const priceWithVatValue = parseFloat(priceWithVat.value) || 0;
            const priceWithoutVatValue = priceWithVatValue / (1 + vatRateValue / 100);
            priceWithoutVat.value = priceWithoutVatValue.toFixed(2);
            
            // Calculate markup
            if (purchasePrice > 0) {
                const markup = ((priceWithoutVatValue - purchasePrice) / purchasePrice) * 100;
                markupPercentage.value = markup.toFixed(2);
            }
            
            // Calculate gross profit
            grossProfit.value = (priceWithoutVatValue - purchasePrice).toFixed(2);
        }
        
        // Calculate from markup
        if (markupPercentage.value && purchasePrice > 0) {
            const markupValue = parseFloat(markupPercentage.value) || 0;
            const priceWithoutVatValue = purchasePrice * (1 + markupValue / 100);
            priceWithoutVat.value = priceWithoutVatValue.toFixed(2);
            
            const priceWithVatValue = priceWithoutVatValue * (1 + vatRateValue / 100);
            priceWithVat.value = priceWithVatValue.toFixed(2);
            
            // Calculate gross profit
            grossProfit.value = (priceWithoutVatValue - purchasePrice).toFixed(2);
        }
    }

    // Event listeners
    priceWithoutVat.addEventListener('input', function() {
        if (this.value) {
            markupPercentage.value = '';
        }
        calculatePrices();
    });

    priceWithVat.addEventListener('input', function() {
        if (this.value) {
            markupPercentage.value = '';
        }
        calculatePrices();
    });

    markupPercentage.addEventListener('input', function() {
        if (this.value) {
            priceWithoutVat.value = '';
            priceWithVat.value = '';
        }
        calculatePrices();
    });

    vatRate.addEventListener('change', calculatePrices);
    purchasePriceWithoutVat.addEventListener('input', calculatePrices);
});
</script>
@endsection
