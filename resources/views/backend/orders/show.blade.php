@extends('layouts.admin')

@section('title', 'Comandă ' . $order->order_number . ' - Admin Panel')
@section('page_title', 'Detalii comandă')
@section('breadcrumbs')
    <a href="{{ route('admin.dashboard') }}" class="hover:text-slate-700">Admin</a>
    <span class="mx-1">/</span>
    <a href="{{ route('admin.orders.index') }}" class="hover:text-slate-700">Comenzi</a>
    <span class="mx-1">/</span>
    <span>{{ $order->order_number }}</span>
@endsection

@section('content')
<div class="p-6 space-y-6">
    <div class="flex items-center justify-between">
        <h1 class="text-3xl font-bold">Comanda #{{ $order->order_number }}</h1>

        <a href="{{ route('admin.orders.index') }}"
           class="px-4 py-2 bg-gray-700 text-white rounded hover:bg-gray-800">
            ← Înapoi la comenzi
        </a>
    </div>

    <div class="bg-white shadow rounded p-6">
        <h2 class="text-xl font-semibold mb-4">Status comandă</h2>

        <div class="flex items-center gap-4">

            <span class="px-3 py-1 rounded text-white
                @if($order->status === 'pending') bg-yellow-500
                @elseif($order->status === 'processing') bg-blue-500
                @elseif($order->status === 'shipped') bg-indigo-500
                @elseif($order->status === 'delivered') bg-green-600
                @elseif($order->status === 'cancelled') bg-red-600
                @endif">
                {{ ucfirst($order->status) }}
            </span>

            <select id="status-select" class="px-3 py-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-blue-500">
                <option value="pending" {{ $order->status === 'pending' ? 'selected' : '' }}>Pending</option>
                <option value="processing" {{ $order->status === 'processing' ? 'selected' : '' }}>Processing</option>
                <option value="shipped" {{ $order->status === 'shipped' ? 'selected' : '' }}>Shipped</option>
                <option value="delivered" {{ $order->status === 'delivered' ? 'selected' : '' }}>Delivered</option>
                <option value="cancelled" {{ $order->status === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
            </select>

            <button onclick="updateStatus({{ $order->id }})"
                class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
                Actualizează
            </button>
        </div>
    </div>

    <div class="bg-white shadow rounded p-6">
        <h2 class="text-xl font-semibold mb-4">Status plată</h2>

        <div class="flex items-center gap-4">

            <span class="px-3 py-1 rounded text-white
                @if($order->payment_status === 'pending') bg-yellow-500
                @elseif($order->payment_status === 'paid') bg-green-600
                @elseif($order->payment_status === 'failed') bg-red-600
                @elseif($order->payment_status === 'refunded') bg-gray-600
                @endif">
                {{ ucfirst($order->payment_status) }}
            </span>

            <select id="payment-status-select" class="px-3 py-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-purple-500">
                <option value="pending" {{ $order->payment_status === 'pending' ? 'selected' : '' }}>Pending</option>
                <option value="paid" {{ $order->payment_status === 'paid' ? 'selected' : '' }}>Paid</option>
                <option value="failed" {{ $order->payment_status === 'failed' ? 'selected' : '' }}>Failed</option>
                <option value="refunded" {{ $order->payment_status === 'refunded' ? 'selected' : '' }}>Refunded</option>
            </select>

            <button onclick="updatePaymentStatus({{ $order->id }})"
                class="px-4 py-2 bg-purple-600 text-white rounded hover:bg-purple-700">
                Actualizează
            </button>
        </div>
    </div>

    <div class="bg-white shadow rounded p-6">
        <h2 class="text-xl font-semibold mb-4">Factură</h2>

        <div class="space-y-2">
            @if($invoice)
                <p class="text-green-600 font-semibold">Factura {{ $invoice->invoice_number }} a fost generată</p>
                <p class="text-gray-500 text-sm">Invoice ID: {{ $invoice->id }}</p>
                <div class="flex gap-2 flex-wrap">
                    <a href="{{ route('admin.invoices.download', $invoice) }}"
                       class="px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700">
                        Descarcă PDF
                    </a>
                </div>
                <div class="mt-2">
                    <a href="/admin/invoices/{{ $invoice->id }}/send-efactura" style="display: inline-block; padding: 10px 20px; background: orange; color: white; text-decoration: none; border-radius: 5px;">
                        Trimite în eFactura
                    </a>
                </div>
                @if($invoice->xml_path)
                <div class="mt-2">
                    <a href="{{ route('admin.invoices.downloadXml', $invoice) }}"
                       class="px-4 py-2 bg-purple-600 text-white rounded hover:bg-purple-700">
                        Descarcă XML
                    </a>
                </div>
                @endif
                @if($invoice->efactura_status)
                <p class="mt-2">
                    <span class="px-3 py-1 rounded text-white
                        @if($invoice->efactura_status === 'sent') bg-green-600
                        @elseif($invoice->efactura_status === 'error') bg-red-600
                        @else bg-gray-600
                        @endif">
                        eFactura: {{ ucfirst($invoice->efactura_status) }}
                    </span>
                </p>
                @endif
            @else
                <form action="{{ route('admin.orders.generateInvoice', $order) }}" method="POST">
                    @csrf
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Tip factură</label>
                            <select name="invoice_type" class="px-3 py-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-blue-500">
                                <option value="individual">Persoană Fizică</option>
                                @if($order->user && $order->user->is_company)
                                <option value="company">Persoană Juridică (Firmă)</option>
                                @elseif($order->customer_email)
                                <option value="company">Persoană Juridică (Firmă)</option>
                                @endif
                            </select>
                            @if($order->customer_email && (!$order->user || !$order->user->is_company))
                            <p class="text-xs text-gray-500 mt-1">Dacă selectezi persoană juridică, va căuta utilizatorul cu emailul {{ $order->customer_email }}</p>
                            @endif
                        </div>
                        <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
                            Generează Factură
                        </button>
                    </div>
                </form>
            @endif
        </div>
    </div>

    <div class="bg-white shadow rounded p-6">
        <h2 class="text-xl font-semibold mb-4">Informații client</h2>

        <div class="grid grid-cols-2 gap-4">
            <p><strong>Nume:</strong> {{ $order->customer_name }}</p>
            <p><strong>Email:</strong> {{ $order->customer_email }}</p>
            <p><strong>Telefon:</strong> {{ $order->customer_phone }}</p>
            <p><strong>Adresă:</strong> {{ $order->shipping_address }}</p>
            <p><strong>Oraș:</strong> {{ $order->shipping_city }}</p>
        </div>
    </div>

    <div class="bg-white shadow rounded p-6">
        <h2 class="text-xl font-semibold mb-4">Produse comandate</h2>

        <table class="w-full border">
            <thead>
                <tr class="bg-gray-100">
                    <th class="p-2 border">Produs</th>
                    <th class="p-2 border">Cantitate</th>
                    <th class="p-2 border">Preț</th>
                </tr>
            </thead>
            <tbody>
                @foreach($order->items as $item)
                <tr>
                    <td class="p-2 border">{{ $item->product->name ?? 'Produs șters' }}</td>
                    <td class="p-2 border">{{ $item->quantity }}</td>
                    <td class="p-2 border">{{ number_format($item->price, 2) }} RON</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

</div>
<script>
function updateStatus(orderId) {
    const statusSelect = document.getElementById('status-select');
    const currentStatus = statusSelect.value;

    fetch('{{ route("admin.orders.updateStatus", ["order" => "ORDER_ID"]) }}'.replace('ORDER_ID', orderId), {
        method: 'PUT',
        headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({ status: currentStatus })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        }
    });
}

function updatePaymentStatus(orderId) {
    const paymentStatusSelect = document.getElementById('payment-status-select');
    const currentStatus = paymentStatusSelect.value;

    fetch('{{ route("admin.orders.updatePayment", ["order" => "ORDER_ID"]) }}'.replace('ORDER_ID', orderId), {
        method: 'PUT',
        headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({ payment_status: currentStatus })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        }
    });
}

// Update hidden input when invoice type changes
document.addEventListener('DOMContentLoaded', function() {
    const invoiceTypeSelect = document.getElementById('invoice_type');
    const invoiceTypeHidden = document.getElementById('invoice_type_hidden');

    if (invoiceTypeSelect && invoiceTypeHidden) {
        invoiceTypeSelect.addEventListener('change', function() {
            invoiceTypeHidden.value = this.value;
        });
    }
});
</script>

@endsection
