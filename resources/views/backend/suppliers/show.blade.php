@extends('layouts.admin')

@section('title', 'Detalii Furnizor - Admin Panel')

@section('content')
<div class="p-6">
    <div class="mb-6">
        <a href="{{ route('admin.suppliers.index') }}" class="text-blue-600 hover:text-blue-700">
            <i class="fas fa-arrow-left mr-1"></i>Înapoi la furnizori
        </a>
    </div>

    <div class="bg-white rounded-lg shadow p-6">
        <div class="flex justify-between items-start mb-6">
            <h1 class="text-2xl font-bold">{{ $supplier->name }}</h1>
            <div class="flex gap-2">
                <a href="{{ route('admin.suppliers.edit', $supplier) }}" class="px-4 py-2 bg-orange-600 text-white rounded-lg hover:bg-orange-700">
                    <i class="fas fa-edit mr-2"></i>Editează
                </a>
                <form action="{{ route('admin.suppliers.destroy', $supplier) }}" method="POST" class="inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700" onclick="return confirm('Sigur dorești să ștergi acest furnizor?')">
                        <i class="fas fa-trash mr-2"></i>Șterge
                    </button>
                </form>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <h2 class="text-lg font-semibold mb-4 text-gray-700">Informații Generale</h2>
                <div class="space-y-3">
                    <div>
                        <span class="text-sm text-gray-500">CUI:</span>
                        <p class="font-medium">{{ $supplier->cui ?? '-' }}</p>
                    </div>
                    <div>
                        <span class="text-sm text-gray-500">Reg. Com.:</span>
                        <p class="font-medium">{{ $supplier->reg_com ?? '-' }}</p>
                    </div>
                    <div>
                        <span class="text-sm text-gray-500">Adresă:</span>
                        <p class="font-medium">{{ $supplier->address ?? '-' }}</p>
                    </div>
                    <div>
                        <span class="text-sm text-gray-500">Oraș:</span>
                        <p class="font-medium">{{ $supplier->city ?? '-' }}</p>
                    </div>
                </div>
            </div>

            <div>
                <h2 class="text-lg font-semibold mb-4 text-gray-700">Contact</h2>
                <div class="space-y-3">
                    <div>
                        <span class="text-sm text-gray-500">Telefon:</span>
                        <p class="font-medium">{{ $supplier->phone ?? '-' }}</p>
                    </div>
                    <div>
                        <span class="text-sm text-gray-500">Email:</span>
                        <p class="font-medium">{{ $supplier->email ?? '-' }}</p>
                    </div>
                    <div>
                        <span class="text-sm text-gray-500">Persoană de Contact:</span>
                        <p class="font-medium">{{ $supplier->contact_person ?? '-' }}</p>
                    </div>
                </div>
            </div>

            <div>
                <h2 class="text-lg font-semibold mb-4 text-gray-700">Status TVA</h2>
                <div class="space-y-3">
                    <div>
                        <span class="text-sm text-gray-500">Status:</span>
                        @if($supplier->tva_status)
                            @if($supplier->tva_status === 'Înregistrat')
                                <p class="font-medium"><span class="px-2 py-1 bg-green-100 text-green-800 rounded-full text-xs">{{ $supplier->tva_status }}</span></p>
                            @elseif($supplier->tva_status === 'Anulat')
                                <p class="font-medium"><span class="px-2 py-1 bg-red-100 text-red-800 rounded-full text-xs">{{ $supplier->tva_status }}</span></p>
                            @else
                                <p class="font-medium"><span class="px-2 py-1 bg-gray-100 text-gray-800 rounded-full text-xs">{{ $supplier->tva_status }}</span></p>
                            @endif
                        @else
                            <p class="font-medium text-gray-400">-</p>
                        @endif
                    </div>
                    @if($supplier->tva_valid_from)
                    <div>
                        <span class="text-sm text-gray-500">Valid de la:</span>
                        <p class="font-medium">{{ $supplier->tva_valid_from->format('d.m.Y') }}</p>
                    </div>
                    @endif
                    @if($supplier->tva_valid_to)
                    <div>
                        <span class="text-sm text-gray-500">Valid până la:</span>
                        <p class="font-medium">{{ $supplier->tva_valid_to->format('d.m.Y') }}</p>
                    </div>
                    @endif
                </div>
            </div>

            <div>
                <h2 class="text-lg font-semibold mb-4 text-gray-700">Status Furnizor</h2>
                <div class="space-y-3">
                    <div>
                        <span class="text-sm text-gray-500">Status:</span>
                        @if($supplier->is_active)
                            <p class="font-medium"><span class="px-2 py-1 bg-green-100 text-green-800 rounded-full text-xs">Activ</span></p>
                        @else
                            <p class="font-medium"><span class="px-2 py-1 bg-gray-100 text-gray-800 rounded-full text-xs">Inactiv</span></p>
                        @endif
                    </div>
                    <div>
                        <span class="text-sm text-gray-500">Creat la:</span>
                        <p class="font-medium">{{ $supplier->created_at->format('d.m.Y H:i') }}</p>
                    </div>
                    <div>
                        <span class="text-sm text-gray-500">Actualizat la:</span>
                        <p class="font-medium">{{ $supplier->updated_at->format('d.m.Y H:i') }}</p>
                    </div>
                </div>
            </div>
        </div>

        @if($supplier->receipts->count() > 0)
        <div class="mt-8">
            <h2 class="text-lg font-semibold mb-4 text-gray-700">Recepții Asociate</h2>
            <div class="overflow-x-auto">
                <table class="min-w-full">
                    <thead>
                        <tr class="border-b">
                            <th class="text-left py-3 px-4">NIR #</th>
                            <th class="text-left py-3 px-4">Nr. Factură</th>
                            <th class="text-left py-3 px-4">Data Facturii</th>
                            <th class="text-left py-3 px-4">Total (fără TVA)</th>
                            <th class="text-left py-3 px-4">Total TVA</th>
                            <th class="text-left py-3 px-4">Total (cu TVA)</th>
                            <th class="text-left py-3 px-4">Acțiuni</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($supplier->receipts as $receipt)
                        <tr class="border-b hover:bg-gray-50">
                            <td class="py-3 px-4 font-medium">NIR-{{ str_pad($receipt->id, 6, '0', STR_PAD_LEFT) }}</td>
                            <td class="py-3 px-4">{{ $receipt->invoice_number }}</td>
                            <td class="py-3 px-4">{{ $receipt->invoice_date->format('d.m.Y') }}</td>
                            <td class="py-3 px-4">{{ number_format($receipt->total_without_vat, 2) }} RON</td>
                            <td class="py-3 px-4">{{ number_format($receipt->total_vat, 2) }} RON</td>
                            <td class="py-3 px-4">{{ number_format($receipt->total_with_vat, 2) }} RON</td>
                            <td class="py-3 px-4">
                                <a href="{{ route('admin.receipts.show', $receipt) }}" class="text-blue-600 hover:text-blue-700">
                                    Vezi
                                </a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        @endif
    </div>
</div>
@endsection
