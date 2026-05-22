@extends('layouts.admin')

@section('title', 'Furnizori - Admin Panel')

@section('content')
<div class="p-6">
    <div class="mb-6 flex justify-between items-center">
        <h1 class="text-2xl font-bold">Furnizori</h1>
        <a href="{{ route('admin.suppliers.create') }}" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
            <i class="fas fa-plus mr-2"></i>Nou Furnizor
        </a>
    </div>

    <div class="bg-white rounded-lg shadow p-6">
        <form action="{{ route('admin.suppliers.index') }}" method="GET" class="mb-6 flex gap-4">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Caută după nume sau CUI..."
                   class="flex-1 px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
            <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                <i class="fas fa-search"></i>
            </button>
            <a href="{{ route('admin.suppliers.index') }}" class="px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700">
                <i class="fas fa-times"></i>
            </a>
        </form>

        <div class="overflow-x-auto">
            <table class="min-w-full">
                <thead>
                    <tr class="border-b">
                        <th class="text-left py-3 px-4">Nume</th>
                        <th class="text-left py-3 px-4">CUI</th>
                        <th class="text-left py-3 px-4">Reg. Com.</th>
                        <th class="text-left py-3 px-4">Oraș</th>
                        <th class="text-left py-3 px-4">Telefon</th>
                        <th class="text-left py-3 px-4">Status TVA</th>
                        <th class="text-left py-3 px-4">Status</th>
                        <th class="text-left py-3 px-4">Acțiuni</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($suppliers as $supplier)
                    <tr class="border-b hover:bg-gray-50">
                        <td class="py-3 px-4 font-medium">{{ $supplier->name }}</td>
                        <td class="py-3 px-4">{{ $supplier->cui ?? '-' }}</td>
                        <td class="py-3 px-4">{{ $supplier->reg_com ?? '-' }}</td>
                        <td class="py-3 px-4">{{ $supplier->city ?? '-' }}</td>
                        <td class="py-3 px-4">{{ $supplier->phone ?? '-' }}</td>
                        <td class="py-3 px-4">
                            @if($supplier->tva_status)
                                @if($supplier->tva_status === 'Înregistrat')
                                    <span class="px-2 py-1 bg-green-100 text-green-800 rounded-full text-xs font-medium">{{ $supplier->tva_status }}</span>
                                @elseif($supplier->tva_status === 'Anulat')
                                    <span class="px-2 py-1 bg-red-100 text-red-800 rounded-full text-xs font-medium">{{ $supplier->tva_status }}</span>
                                @else
                                    <span class="px-2 py-1 bg-gray-100 text-gray-800 rounded-full text-xs font-medium">{{ $supplier->tva_status }}</span>
                                @endif
                            @else
                                <span class="text-gray-400">-</span>
                            @endif
                        </td>
                        <td class="py-3 px-4">
                            @if($supplier->is_active)
                                <span class="px-2 py-1 bg-green-100 text-green-800 rounded-full text-xs font-medium">Activ</span>
                            @else
                                <span class="px-2 py-1 bg-gray-100 text-gray-800 rounded-full text-xs font-medium">Inactiv</span>
                            @endif
                        </td>
                        <td class="py-3 px-4">
                            <div class="flex gap-2">
                                <a href="{{ route('admin.suppliers.show', $supplier) }}" class="text-blue-600 hover:text-blue-700 px-2 py-1 border border-blue-600 rounded text-sm">
                                    Vezi
                                </a>
                                <a href="{{ route('admin.suppliers.edit', $supplier) }}" class="text-orange-600 hover:text-orange-700 px-2 py-1 border border-orange-600 rounded text-sm">
                                    Editează
                                </a>
                                <form action="{{ route('admin.suppliers.destroy', $supplier) }}" method="POST" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:text-red-700 px-2 py-1 border border-red-600 rounded text-sm" onclick="return confirm('Sigur dorești să ștergi acest furnizor?')">
                                        Șterge
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="py-8 text-center text-gray-500">
                            Nu există furnizori.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($suppliers->hasPages())
        <div class="mt-6 flex justify-center">
            {{ $suppliers->links() }}
        </div>
        @endif
    </div>
</div>
@endsection
