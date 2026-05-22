@extends('layouts.admin')

@section('title', 'NIR-' . str_pad($receipt->id, 6, '0', STR_PAD_LEFT) . ' - Admin Panel')

@section('content')
<div class="p-6">
    <div class="mb-6 flex justify-between items-center">
        <a href="{{ route('admin.receipts.index') }}" class="text-blue-600 hover:text-blue-700">
            <i class="fas fa-arrow-left mr-1"></i>Înapoi la recepții
        </a>
        <div class="flex gap-2">
            <a href="{{ route('admin.receipts.downloadPdf', $receipt) }}" class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700">
                <i class="fas fa-file-pdf mr-2"></i>Descarcă PDF
            </a>
            <form action="{{ route('admin.receipts.destroy', $receipt) }}" method="POST" class="inline">
                @csrf
                @method('DELETE')
                <button type="submit" class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700" onclick="return confirm('Sigur dorești să ștergi această recepție?')">
                    <i class="fas fa-trash mr-2"></i>Șterge
                </button>
            </form>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Receipt Details -->
        <div class="lg:col-span-2 space-y-6">
            <div class="bg-white rounded-lg shadow p-6">
                <h2 class="text-xl font-bold mb-4">Detalii Recepție</h2>
                
                <div class="grid grid-cols-2 gap-4 mb-6">
                    <div>
                        <p class="text-sm text-gray-500">Număr NIR</p>
                        <p class="font-medium">NIR-{{ str_pad($receipt->id, 6, '0', STR_PAD_LEFT) }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">Data Recepției</p>
                        <p class="font-medium">{{ $receipt->receipt_date->format('d.m.Y') }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">Număr Factură Furnizor</p>
                        <p class="font-medium">{{ $receipt->invoice_number }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">Data Facturii</p>
                        <p class="font-medium">{{ $receipt->invoice_date->format('d.m.Y') }}</p>
                    </div>
                </div>

                <div class="border-t pt-4 mb-4">
                    <h3 class="font-semibold mb-3">Furnizor</h3>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <p class="text-sm text-gray-500">Nume</p>
                            <p class="font-medium">{{ $receipt->supplier->name ?? '-' }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500">CUI</p>
                            <p class="font-medium">{{ $receipt->supplier->cui ?? '-' }}</p>
                        </div>
                        <div class="col-span-2">
                            <p class="text-sm text-gray-500">Adresă</p>
                            <p class="font-medium">{{ $receipt->supplier->address ?? '-' }}</p>
                        </div>
                    </div>
                </div>

                @if($receipt->notes)
                <div class="border-t pt-4">
                    <p class="text-sm text-gray-500">Note</p>
                    <p>{{ $receipt->notes }}</p>
                </div>
                @endif
            </div>

            <!-- Items -->
            <div class="bg-white rounded-lg shadow p-6">
                <h2 class="text-xl font-bold mb-4">Produse</h2>
                <div class="overflow-x-auto">
                    <table class="min-w-full">
                        <thead>
                            <tr class="border-b">
                                <th class="text-left py-3 px-4">Produs</th>
                                <th class="text-left py-3 px-4">Cantitate</th>
                                <th class="text-left py-3 px-4">Preț fără TVA</th>
                                <th class="text-left py-3 px-4">TVA %</th>
                                <th class="text-left py-3 px-4">Valoare TVA</th>
                                <th class="text-left py-3 px-4">Preț cu TVA</th>
                                <th class="text-left py-3 px-4">Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($receipt->items as $item)
                            <tr class="border-b">
                                <td class="py-3 px-4">{{ $item->product->name }}</td>
                                <td class="py-3 px-4">{{ $item->quantity }}</td>
                                <td class="py-3 px-4">{{ number_format($item->purchase_price_without_vat, 2) }} lei</td>
                                <td class="py-3 px-4">{{ $item->vat_rate }}%</td>
                                <td class="py-3 px-4">{{ number_format($item->vat_value, 2) }} lei</td>
                                <td class="py-3 px-4">{{ number_format($item->purchase_price_with_vat, 2) }} lei</td>
                                <td class="py-3 px-4 font-medium">{{ number_format($item->line_total_with_vat, 2) }} lei</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Totals -->
        <div class="space-y-6">
            <div class="bg-white rounded-lg shadow p-6">
                <h2 class="text-xl font-bold mb-4">Totaluri</h2>
                <div class="space-y-4">
                    <div class="flex justify-between">
                        <span class="text-gray-600">Total fără TVA</span>
                        <span class="font-medium">{{ number_format($receipt->total_without_vat, 2) }} lei</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">TVA</span>
                        <span class="font-medium">{{ number_format($receipt->total_vat, 2) }} lei</span>
                    </div>
                    <div class="border-t pt-4 flex justify-between">
                        <span class="text-lg font-semibold">Total cu TVA</span>
                        <span class="text-xl font-bold text-green-600">{{ number_format($receipt->total_with_vat, 2) }} lei</span>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow p-6">
                <h2 class="text-xl font-bold mb-4">Stoc Actualizat</h2>
                <p class="text-sm text-gray-500 mb-4">Următoarele produse au avut stocul actualizat:</p>
                <div class="space-y-2">
                    @foreach($receipt->items as $item)
                    <div class="flex justify-between text-sm">
                        <span>{{ $item->product->name }}</span>
                        <span class="text-green-600">+{{ $item->quantity }} buc</span>
                    </div>
                    @endforeach
                </div>
            </div>

            @if($receipt->creator)
            <div class="bg-white rounded-lg shadow p-6">
                <h2 class="text-xl font-bold mb-4">Creat de</h2>
                <p class="text-sm text-gray-500">{{ $receipt->creator->name ?? '-' }}</p>
                <p class="text-xs text-gray-400">{{ $receipt->created_at->format('d.m.Y H:i') }}</p>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection
