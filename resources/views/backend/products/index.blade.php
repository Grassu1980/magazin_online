@extends('layouts.admin')

@section('title', 'Produse - Admin Panel')
@section('page_title', 'Produse')
@section('breadcrumbs')
    <a href="{{ route('admin.dashboard') }}" class="hover:text-slate-700">Admin</a>
    <span class="mx-1">/</span>
    <span>Produse</span>
@endsection

@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between gap-3">
        <div class="text-sm text-slate-500">Gestionare produse, stoc și status.</div>
        <a href="{{ route('admin.products.create') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
            <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
            </svg>
            Produs nou
        </a>
    </div>

    <div class="bg-white rounded-xl border border-slate-200 p-4">
        <form method="GET" class="grid grid-cols-1 md:grid-cols-6 gap-4 items-end">
            <div class="md:col-span-2">
                <label class="block text-sm font-medium text-slate-700 mb-1">Caută</label>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Nume sau SKU"
                    class="w-full px-3 py-2 border border-slate-200 rounded-lg focus:ring-2 focus:ring-blue-500">
            </div>
            <div class="md:col-span-2">
                <label class="block text-sm font-medium text-slate-700 mb-1">Categorie</label>
                <select name="category" class="w-full px-3 py-2 border border-slate-200 rounded-lg">
                    <option value="">Toate</option>
                    @foreach($categories as $category)
                        <option value="{{ $category->id }}" {{ request('category') == $category->id ? 'selected' : '' }}>
                            {{ $category->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">Stoc</label>
                <select name="stock" class="w-full px-3 py-2 border border-slate-200 rounded-lg">
                    <option value="">Toate</option>
                    <option value="low" {{ request('stock') == 'low' ? 'selected' : '' }}>Redus</option>
                    <option value="out" {{ request('stock') == 'out' ? 'selected' : '' }}>0</option>
                    <option value="in" {{ request('stock') == 'in' ? 'selected' : '' }}>OK</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">Status</label>
                <select name="status" class="w-full px-3 py-2 border border-slate-200 rounded-lg">
                    <option value="">Toate</option>
                    <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                    <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                </select>
            </div>
            <div class="md:col-span-6 flex gap-2">
                <button type="submit" class="px-4 py-2 bg-slate-900 text-white rounded-lg hover:bg-slate-800">Filtrează</button>
                <a href="{{ route('admin.products.index') }}" class="px-4 py-2 border border-slate-200 rounded-lg text-slate-700 hover:bg-slate-50">Reset</a>
            </div>
        </form>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 md:hidden">
        @foreach($products as $product)
            <div class="bg-white rounded-xl border border-slate-200 overflow-hidden">
                <div class="aspect-[4/3] bg-slate-100">
                    @if($product->image)
                        <img src="{{ asset($product->image) }}" alt="{{ $product->name }}" class="w-full h-full object-cover">
                    @endif
                </div>
                <div class="p-4">
                    <div class="font-medium text-slate-900">{{ $product->name }}</div>
                    <div class="mt-1 text-sm text-slate-500">{{ $product->category->name ?? '-' }}</div>
                    <div class="mt-3 flex items-center justify-between">
                        <div class="text-sm font-semibold text-slate-900">{{ number_format($product->final_price, 2) }} lei</div>
                        <span class="text-xs font-medium px-2 py-1 rounded-full {{ $product->stock > 10 ? 'bg-emerald-50 text-emerald-700' : ($product->stock > 0 ? 'bg-amber-50 text-amber-700' : 'bg-rose-50 text-rose-700') }}">
                            Stoc: {{ $product->stock }}
                        </span>
                    </div>
                    <div class="mt-4 flex items-center justify-end gap-2">
                        <a href="{{ route('admin.products.show', $product->id) }}" class="inline-flex items-center justify-center w-9 h-9 rounded-lg border border-slate-200 text-slate-700 hover:bg-slate-50">
                            <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 12s3.75-7.5 9.75-7.5 9.75 7.5 9.75 7.5-3.75 7.5-9.75 7.5S2.25 12 2.25 12Z" />
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                            </svg>
                        </a>
                        <a href="{{ route('admin.products.edit', $product->id) }}" class="inline-flex items-center justify-center w-9 h-9 rounded-lg border border-slate-200 text-slate-700 hover:bg-slate-50">
                            <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 3.487a2.25 2.25 0 0 1 3.182 3.182L7.5 19.213 3 21l1.787-4.5L16.862 3.487Z" />
                            </svg>
                        </a>
                        <form action="{{ route('admin.products.destroy', $product->id) }}" method="POST" class="inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="inline-flex items-center justify-center w-9 h-9 rounded-lg border border-rose-200 text-rose-700 hover:bg-rose-50" onclick="return confirm('Sigur dorești să ștergi acest produs?')">
                                <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 7.5h12M9 7.5V6a1.5 1.5 0 0 1 1.5-1.5h3A1.5 1.5 0 0 1 15 6v1.5m-7.5 0V18A2.25 2.25 0 0 0 9.75 20.25h4.5A2.25 2.25 0 0 0 16.5 18V7.5" />
                                </svg>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    <div class="hidden md:block bg-white rounded-xl border border-slate-200 overflow-hidden">
        <table class="w-full">
            <thead class="bg-slate-50">
                <tr>
                    <th class="px-4 py-3 text-left text-sm font-semibold text-slate-600">Produs</th>
                    <th class="px-4 py-3 text-left text-sm font-semibold text-slate-600">SKU</th>
                    <th class="px-4 py-3 text-left text-sm font-semibold text-slate-600">Categorie</th>
                    <th class="px-4 py-3 text-right text-sm font-semibold text-slate-600">Preț fără TVA</th>
                    <th class="px-4 py-3 text-right text-sm font-semibold text-slate-600">TVA</th>
                    <th class="px-4 py-3 text-right text-sm font-semibold text-slate-600">Preț final</th>
                    <th class="px-4 py-3 text-center text-sm font-semibold text-slate-600">Stoc</th>
                    <th class="px-4 py-3 text-center text-sm font-semibold text-slate-600">Status</th>
                    <th class="px-4 py-3 text-right text-sm font-semibold text-slate-600">Acțiuni</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-200">
                @foreach($products as $product)
                    <tr class="hover:bg-slate-50">
                        <td class="px-4 py-3">
                            <div class="flex items-center gap-3">
                                <div class="w-12 h-12 bg-slate-100 rounded overflow-hidden">
                                    @if($product->image)
                                        <img src="{{ asset($product->image) }}" alt="{{ $product->name }}" class="w-full h-full object-cover">
                                    @endif
                                </div>
                                <div>
                                    <div class="font-medium text-slate-900">{{ $product->name }}</div>
                                    <div class="text-xs text-slate-500">{{ $product->views }} vizualizări</div>
                                </div>
                            </div>
                        </td>
                        <td class="px-4 py-3 text-sm text-slate-700">{{ $product->sku ?: '-' }}</td>
                        <td class="px-4 py-3 text-sm text-slate-700">{{ $product->category->name ?? '-' }}</td>
                        <td class="px-4 py-3 text-right text-sm text-slate-700">
                            {{ number_format($product->price_without_vat ?? ($product->price / (1 + ($product->vat_rate ?? 21) / 100)), 2) }} lei
                        </td>
                        <td class="px-4 py-3 text-right text-sm text-slate-700">
                            {{ $product->vat_rate ?? 21 }}%
                        </td>
                        <td class="px-4 py-3 text-right">
                            @if($product->is_on_sale && $product->special_price)
                                <div class="font-semibold text-rose-700">{{ number_format($product->special_price, 2) }} lei</div>
                                <div class="text-xs text-slate-500 line-through">{{ number_format($product->price, 2) }} lei</div>
                            @else
                                <div class="font-semibold text-slate-900">{{ number_format($product->price, 2) }} lei</div>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-center">
                            <span class="px-2 py-1 rounded-full text-xs font-medium {{ $product->stock > 10 ? 'bg-emerald-50 text-emerald-700' : ($product->stock > 0 ? 'bg-amber-50 text-amber-700' : 'bg-rose-50 text-rose-700') }}">
                                {{ $product->stock }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-center">
                            <span class="px-2 py-1 rounded-full text-xs font-medium {{ $product->is_active ? 'bg-emerald-50 text-emerald-700' : 'bg-slate-100 text-slate-700' }}">
                                {{ $product->is_active ? 'Activ' : 'Inactiv' }}
                            </span>
                        </td>
                        <td class="px-4 py-3">
                            <div class="flex justify-end gap-2">
                                <a href="{{ route('admin.products.show', $product->id) }}" class="inline-flex items-center justify-center w-9 h-9 rounded-lg border border-slate-200 text-slate-700 hover:bg-slate-50">
                                    <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 12s3.75-7.5 9.75-7.5 9.75 7.5 9.75 7.5-3.75 7.5-9.75 7.5S2.25 12 2.25 12Z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                                    </svg>
                                </a>
                                <a href="{{ route('admin.products.edit', $product->id) }}" class="inline-flex items-center justify-center w-9 h-9 rounded-lg border border-slate-200 text-slate-700 hover:bg-slate-50">
                                    <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 3.487a2.25 2.25 0 0 1 3.182 3.182L7.5 19.213 3 21l1.787-4.5L16.862 3.487Z" />
                                    </svg>
                                </a>
                                <form action="{{ route('admin.products.destroy', $product->id) }}" method="POST" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="inline-flex items-center justify-center w-9 h-9 rounded-lg border border-rose-200 text-rose-700 hover:bg-rose-50" onclick="return confirm('Sigur dorești să ștergi acest produs?')">
                                        <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 7.5h12M9 7.5V6a1.5 1.5 0 0 1 1.5-1.5h3A1.5 1.5 0 0 1 15 6v1.5m-7.5 0V18A2.25 2.25 0 0 0 9.75 20.25h4.5A2.25 2.25 0 0 0 16.5 18V7.5" />
                                        </svg>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        @if($products->hasPages())
            <div class="p-4 border-t border-slate-200">
                {{ $products->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
