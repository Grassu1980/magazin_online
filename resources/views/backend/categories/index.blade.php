@extends('layouts.admin')

@section('title', 'Categorii - Admin Panel')
@section('page_title', 'Categorii')
@section('breadcrumbs')
    <a href="{{ route('admin.dashboard') }}" class="hover:text-slate-700">Admin</a>
    <span class="mx-1">/</span>
    <span>Categorii</span>
@endsection

@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between gap-3">
        <div class="text-sm text-slate-500">Gestionează categoriile magazinului.</div>
        <a href="{{ route('admin.categories.create') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
            <x-heroicon-outline-plus class="w-5 h-5" />
            Categorie nouă
        </a>
    </div>
    
    <div class="bg-white rounded-xl border border-slate-200 overflow-hidden">
        <table class="w-full">
            <thead class="bg-slate-50">
                <tr>
                    <th class="px-4 py-3 text-left text-sm font-semibold text-slate-600">Categorie</th>
                    <th class="px-4 py-3 text-left text-sm font-semibold text-slate-600">Slug</th>
                    <th class="px-4 py-3 text-center text-sm font-semibold text-slate-600">Produse</th>
                    <th class="px-4 py-3 text-center text-sm font-semibold text-slate-600">Status</th>
                    <th class="px-4 py-3 text-right text-sm font-semibold text-slate-600">Acțiuni</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-200">
                @foreach($categories as $category)
                <tr class="hover:bg-slate-50">
                    <td class="px-4 py-3">
                        <div class="flex items-center gap-3">
                            @if($category->image)
                            <img src="{{ asset($category->image) }}" alt="{{ $category->name }}" class="w-10 h-10 object-cover rounded">
                            @else
                            <div class="w-10 h-10 bg-slate-100 rounded flex items-center justify-center text-slate-400">
                                <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h6l1.5 1.5H20.25v9.75A2.25 2.25 0 0 1 18 20.25H6A2.25 2.25 0 0 1 3.75 18V6.75Z" />
                                </svg>
                            </div>
                            @endif
                            <div>
                                <p class="font-medium text-slate-900">{{ $category->name }}</p>
                            </div>
                        </div>
                    </td>
                    <td class="px-4 py-3 text-sm text-slate-500">{{ $category->slug }}</td>
                    <td class="px-4 py-3 text-center">
                        <span class="px-2 py-1 rounded-full text-xs font-medium bg-blue-50 text-blue-700">
                            {{ $category->products_count }} produse
                        </span>
                    </td>
                    <td class="px-4 py-3 text-center">
                        <span class="px-2 py-1 rounded-full text-xs font-medium {{ $category->is_active ? 'bg-emerald-50 text-emerald-700' : 'bg-slate-100 text-slate-700' }}">
                            {{ $category->is_active ? 'Activă' : 'Inactivă' }}
                        </span>
                    </td>
                    <td class="px-4 py-3">
                        <div class="flex justify-end gap-2">
                            <a href="{{ route('admin.categories.show', $category->id) }}" class="inline-flex items-center justify-center w-9 h-9 rounded-lg border border-slate-200 text-slate-700 hover:bg-slate-50">
                                <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 12s3.75-7.5 9.75-7.5 9.75 7.5 9.75 7.5-3.75 7.5-9.75 7.5S2.25 12 2.25 12Z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                                </svg>
                            </a>
                            <a href="{{ route('admin.categories.edit', $category->id) }}" class="inline-flex items-center justify-center w-9 h-9 rounded-lg border border-slate-200 text-slate-700 hover:bg-slate-50">
                                <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 3.487a2.25 2.25 0 0 1 3.182 3.182L7.5 19.213 3 21l1.787-4.5L16.862 3.487Z" />
                                </svg>
                            </a>
                            <form action="{{ route('admin.categories.destroy', $category->id) }}" method="POST" class="inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="inline-flex items-center justify-center w-9 h-9 rounded-lg border border-rose-200 text-rose-700 hover:bg-rose-50" onclick="return confirm('Sigur dorești să ștergi această categorie?')">
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
        
        @if($categories->hasPages())
        <div class="p-4 border-t border-slate-200">
            {{ $categories->links() }}
        </div>
        @endif
    </div>
</div>
@endsection
