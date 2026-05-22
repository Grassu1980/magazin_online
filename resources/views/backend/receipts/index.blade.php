@extends('layouts.admin')

@section('title', 'Recepții (NIR) - Admin Panel')

@section('content')
<div class="p-6">
    <div class="mb-6 flex justify-between items-center">
        <h1 class="text-2xl font-bold">Recepții Marfă (NIR)</h1>
        <a href="{{ route('admin.receipts.create') }}" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
            <i class="fas fa-plus mr-2"></i>Nouă Recepție
        </a>
    </div>

    <div class="bg-white rounded-lg shadow p-6">
        <form action="{{ route('admin.receipts.index') }}" method="GET" class="mb-6 flex gap-4 flex-wrap">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Caută după număr sau furnizor..."
                   class="flex-1 px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
            <select name="supplier_id" class="px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                <option value="">Toți furnizorii</option>
                @foreach($suppliers as $supplier)
                <option value="{{ $supplier->id }}" {{ request('supplier_id') == $supplier->id ? 'selected' : '' }}>{{ $supplier->name }}</option>
                @endforeach
            </select>
            <input type="date" name="date_from" value="{{ request('date_from') }}"
                   class="px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
            <input type="date" name="date_to" value="{{ request('date_to') }}"
                   class="px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
            <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                <i class="fas fa-search"></i>
            </button>
            <a href="{{ route('admin.receipts.index') }}" class="px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700">
                <i class="fas fa-times"></i>
            </a>
        </form>

        <div class="overflow-x-auto">
            <table class="min-w-full">
                <thead>
                    <tr class="border-b">
                        <th class="text-left py-3 px-4">Număr NIR</th>
                        <th class="text-left py-3 px-4">Furnizor</th>
                        <th class="text-left py-3 px-4">Nr. Factură</th>
                        <th class="text-left py-3 px-4">Data Recepție</th>
                        <th class="text-left py-3 px-4">Total fără TVA</th>
                        <th class="text-left py-3 px-4">TVA</th>
                        <th class="text-left py-3 px-4">Total cu TVA</th>
                        <th class="text-left py-3 px-4">Acțiuni</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($receipts as $receipt)
                    <tr class="border-b hover:bg-gray-50">
                        <td class="py-3 px-4 font-medium">NIR-{{ str_pad($receipt->id, 6, '0', STR_PAD_LEFT) }}</td>
                        <td class="py-3 px-4">{{ $receipt->supplier->name ?? '-' }}</td>
                        <td class="py-3 px-4">{{ $receipt->invoice_number }}</td>
                        <td class="py-3 px-4">{{ $receipt->receipt_date->format('d.m.Y') }}</td>
                        <td class="py-3 px-4">{{ number_format($receipt->total_without_vat, 2) }} lei</td>
                        <td class="py-3 px-4">{{ number_format($receipt->total_vat, 2) }} lei</td>
                        <td class="py-3 px-4 font-bold">{{ number_format($receipt->total_with_vat, 2) }} lei</td>
                        <td class="py-3 px-4">
                            <div class="flex gap-2">
                                <a href="{{ route('admin.receipts.show', $receipt) }}" class="text-blue-600 hover:text-blue-700 px-2 py-1 border border-blue-600 rounded text-sm">
                                    Vezi
                                </a>
                                <a href="{{ route('admin.receipts.downloadPdf', $receipt) }}" class="text-green-600 hover:text-green-700 px-2 py-1 border border-green-600 rounded text-sm">
                                    PDF
                                </a>
                                <form action="{{ route('admin.receipts.destroy', $receipt) }}" method="POST" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:text-red-700 px-2 py-1 border border-red-600 rounded text-sm" onclick="return confirm('Sigur dorești să ștergi această recepție?')">
                                        Șterge
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="py-8 text-center text-gray-500">
                            Nu există recepții.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($receipts->hasPages())
        <div class="mt-6 flex justify-center">
            {{ $receipts->links() }}
        </div>
        @endif
    </div>
</div>
@endsection
