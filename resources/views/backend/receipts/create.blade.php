@extends('layouts.admin')

@section('title', 'Nouă Recepție - Admin Panel')

@section('content')
<div class="p-6">
    <div class="mb-6">
        <a href="{{ route('admin.receipts.index') }}" class="text-blue-600 hover:text-blue-700">
            <i class="fas fa-arrow-left mr-1"></i>Înapoi la recepții
        </a>
    </div>

    <div class="bg-white rounded-lg shadow p-6">
        <h1 class="text-2xl font-bold mb-6">Nouă Recepție (NIR)</h1>

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

        <form action="{{ route('admin.receipts.store') }}" method="POST" id="receiptForm">
            @csrf

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Furnizor *</label>
                    <select name="supplier_id" required
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="">Selectează furnizorul</option>
                        @foreach($suppliers as $supplier)
                        <option value="{{ $supplier->id }}">{{ $supplier->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Număr Factură Furnizor *</label>
                    <input type="text" name="invoice_number" required
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                           placeholder="Numărul facturii furnizorului">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Data Facturii *</label>
                    <input type="date" name="invoice_date" value="{{ now()->format('Y-m-d') }}" required
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Data Recepției *</label>
                    <input type="date" name="receipt_date" value="{{ now()->format('Y-m-d') }}" required
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
            </div>

            <div class="mb-6">
                <h2 class="text-lg font-semibold mb-4">Produse</h2>
                <div id="itemsContainer">
                    <div class="item-row grid grid-cols-1 md:grid-cols-6 gap-4 mb-4 items-end">
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Produs</label>
                            <select name="items[0][product_id]" required
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 product-select">
                                <option value="">Selectează produsul</option>
                                @foreach($products as $product)
                                <option value="{{ $product->id }}">{{ $product->name }} (SKU: {{ $product->sku }})</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Cantitate</label>
                            <input type="number" name="items[0][quantity]" step="0.01" min="0.01" required
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 quantity-input">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Preț cu TVA</label>
                            <input type="number" name="items[0][purchase_price_with_vat]" step="0.01" min="0" required
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 price-input">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">TVA %</label>
                            <select name="items[0][vat_rate]" required
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 tva-input">
                                <option value="21" selected>21%</option>
                                <option value="11">11%</option>
                                <option value="0">0%</option>
                            </select>
                        </div>
                        <div>
                            <button type="button" onclick="removeItem(this)" class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 hidden remove-btn">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    </div>
                </div>
                <button type="button" onclick="addItem()" class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700">
                    <i class="fas fa-plus mr-2"></i>Adaugă Produs
                </button>
            </div>

            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 mb-2">Note</label>
                <textarea name="notes" rows="3"
                          class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                          placeholder="Note suplimentare..."></textarea>
            </div>

            <div class="bg-gray-100 rounded-lg p-4 mb-6">
                <div class="grid grid-cols-3 gap-4">
                    <div>
                        <p class="text-sm text-gray-500">Total fără TVA</p>
                        <p class="text-xl font-bold" id="totalWithoutVatDisplay">0.00 lei</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">TVA</p>
                        <p class="text-xl font-bold" id="tvaDisplay">0.00 lei</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">Total cu TVA</p>
                        <p class="text-xl font-bold text-green-600" id="totalWithVatDisplay">0.00 lei</p>
                    </div>
                </div>
            </div>

            <div class="flex justify-end gap-4">
                <a href="{{ route('admin.receipts.index') }}" class="px-6 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700">
                    Anulează
                </a>
                <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                    <i class="fas fa-save mr-2"></i>Salvează Recepția
                </button>
            </div>
        </form>
    </div>
</div>

<script>
let itemCount = 1;

function addItem() {
    const container = document.getElementById('itemsContainer');
    const newRow = document.createElement('div');
    newRow.className = 'item-row grid grid-cols-1 md:grid-cols-6 gap-4 mb-4 items-end';
    newRow.innerHTML = `
        <div class="md:col-span-2">
            <label class="block text-sm font-medium text-gray-700 mb-2">Produs</label>
            <select name="items[${itemCount}][product_id]" required
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 product-select">
                <option value="">Selectează produsul</option>
                @foreach($products as $product)
                <option value="{{ $product->id }}">{{ $product->name }} (SKU: {{ $product->sku }})</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Cantitate</label>
            <input type="number" name="items[${itemCount}][quantity]" step="0.01" min="0.01" required
                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 quantity-input">
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Preț cu TVA</label>
            <input type="number" name="items[${itemCount}][purchase_price_with_vat]" step="0.01" min="0" required
                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 price-input">
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">TVA %</label>
            <select name="items[${itemCount}][vat_rate]" required
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 tva-input">
                <option value="21" selected>21%</option>
                <option value="11">11%</option>
                <option value="0">0%</option>
            </select>
        </div>
        <div>
            <button type="button" onclick="removeItem(this)" class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 remove-btn">
                <i class="fas fa-trash"></i>
            </button>
        </div>
    `;
    container.appendChild(newRow);
    itemCount++;
    updateRemoveButtons();
    attachCalculationListeners();
}

function removeItem(button) {
    button.closest('.item-row').remove();
    updateRemoveButtons();
    calculateTotals();
}

function updateRemoveButtons() {
    const rows = document.querySelectorAll('.item-row');
    rows.forEach((row, index) => {
        const removeBtn = row.querySelector('.remove-btn');
        if (removeBtn) {
            removeBtn.classList.toggle('hidden', rows.length === 1);
        }
    });
}

function calculateTotals() {
    let totalWithoutVat = 0;
    let totalVat = 0;

    document.querySelectorAll('.item-row').forEach(row => {
        const quantity = parseFloat(row.querySelector('.quantity-input').value) || 0;
        const priceWithVat = parseFloat(row.querySelector('.price-input').value) || 0;
        const vatRate = parseFloat(row.querySelector('.tva-input').value) || 0;

        const priceWithoutVat = priceWithVat / (1 + vatRate / 100);
        const vatValue = priceWithoutVat * (vatRate / 100);
        const lineTotalWithoutVat = priceWithoutVat * quantity;
        const lineTotalVat = vatValue * quantity;

        totalWithoutVat += lineTotalWithoutVat;
        totalVat += lineTotalVat;
    });

    document.getElementById('totalWithoutVatDisplay').textContent = totalWithoutVat.toFixed(2) + ' lei';
    document.getElementById('tvaDisplay').textContent = totalVat.toFixed(2) + ' lei';
    document.getElementById('totalWithVatDisplay').textContent = (totalWithoutVat + totalVat).toFixed(2) + ' lei';
}

function attachCalculationListeners() {
    document.querySelectorAll('.quantity-input, .price-input, .tva-input').forEach(input => {
        input.addEventListener('input', calculateTotals);
    });
}

document.addEventListener('DOMContentLoaded', function() {
    attachCalculationListeners();
    updateRemoveButtons();
});
</script>
@endsection
