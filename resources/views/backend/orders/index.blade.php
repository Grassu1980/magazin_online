@extends('layouts.admin')

@section('title', 'Comenzi - Admin Panel')
@section('page_title', 'Comenzi')
@section('breadcrumbs')
    <a href="{{ route('admin.dashboard') }}" class="hover:text-slate-700">Admin</a>
    <span class="mx-1">/</span>
    <span>Comenzi</span>
@endsection

@section('content')
<div class="p-6">
    <h1 class="text-2xl font-bold mb-6">Comenzi</h1>
    
    <!-- Filters -->
    <div class="bg-white rounded-lg shadow mb-6 p-4">
        <form method="GET" class="flex flex-wrap gap-4 items-end">
            <div class="flex-1 min-w-[200px]">
                <label class="block text-sm text-gray-600 mb-1">Caută</label>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Număr comandă sau nume..." 
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg">
            </div>
            <div class="w-40">
                <label class="block text-sm text-gray-600 mb-1">Status</label>
                <select name="status" class="w-full px-3 py-2 border border-gray-300 rounded-lg">
                    <option value="">Toate</option>
                    <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>În așteptare</option>
                    <option value="processing" {{ request('status') == 'processing' ? 'selected' : '' }}>Se procesează</option>
                    <option value="shipped" {{ request('status') == 'shipped' ? 'selected' : '' }}>Expediat</option>
                    <option value="delivered" {{ request('status') == 'delivered' ? 'selected' : '' }}>Livrat</option>
                    <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Anulat</option>
                </select>
            </div>
            <div class="w-40">
                <label class="block text-sm text-gray-600 mb-1">Plată</label>
                <select name="payment_status" class="w-full px-3 py-2 border border-gray-300 rounded-lg">
                    <option value="">Toate</option>
                    <option value="pending" {{ request('payment_status') == 'pending' ? 'selected' : '' }}>În așteptare</option>
                    <option value="paid" {{ request('payment_status') == 'paid' ? 'selected' : '' }}>Plătit</option>
                </select>
            </div>
            <button type="submit" class="px-4 py-2 bg-gray-800 text-white rounded-lg hover:bg-gray-900">
                Filtrează
            </button>
        </form>
    </div>
    
    <!-- Orders Table -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <table class="w-full">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-4 py-3 text-left text-sm font-semibold text-gray-600">Comandă</th>
                    <th class="px-4 py-3 text-left text-sm font-semibold text-gray-600">Client</th>
                    <th class="px-4 py-3 text-right text-sm font-semibold text-gray-600">Total</th>
                    <th class="px-4 py-3 text-center text-sm font-semibold text-gray-600">Status</th>
                    <th class="px-4 py-3 text-center text-sm font-semibold text-gray-600">Plată</th>
                    <th class="px-4 py-3 text-left text-sm font-semibold text-gray-600">Data</th>
                    <th class="px-4 py-3 text-center text-sm font-semibold text-gray-600">Acțiuni</th>
                </tr>
            </thead>
            <tbody class="divide-y">
                @foreach($orders as $order)
                <tr class="hover:bg-gray-50">
                    <td class="px-4 py-3">
                        <p class="font-medium">{{ $order->order_number }}</p>
                        <p class="text-xs text-gray-500">{{ $order->items->count() }} produse</p>
                    </td>
                    <td class="px-4 py-3">
                        <p class="font-medium">{{ $order->customer_name }}</p>
                        <p class="text-xs text-gray-500">{{ $order->customer_email }}</p>
                    </td>
                    <td class="px-4 py-3 text-right font-semibold">{{ number_format($order->total, 2) }} lei</td>
                    <td class="px-4 py-3 text-center">
                        <span class="px-2 py-1 rounded-full text-xs font-medium
                            @if($order->status == 'pending') bg-yellow-100 text-yellow-700
                            @elseif($order->status == 'processing') bg-blue-100 text-blue-700
                            @elseif($order->status == 'shipped') bg-purple-100 text-purple-700
                            @elseif($order->status == 'delivered') bg-green-100 text-green-700
                            @else bg-red-100 text-red-700 @endif">
                            @if($order->status == 'pending') În așteptare
                            @elseif($order->status == 'processing') Se procesează
                            @elseif($order->status == 'shipped') Expediat
                            @elseif($order->status == 'delivered') Livrat
                            @elseif($order->status == 'cancelled') Anulat
                            @endif
                        </span>
                    </td>
                    <td class="px-4 py-3 text-center">
                        <span class="px-2 py-1 rounded-full text-xs font-medium {{ $order->payment_status == 'paid' ? 'bg-green-100 text-green-700' : 'bg-yellow-100 text-yellow-700' }}">
                            {{ $order->payment_status == 'paid' ? 'Plătit' : 'În așteptare' }}
                        </span>
                    </td>
                    <td class="px-4 py-3 text-sm text-gray-500">
                        {{ $order->created_at->format('d.m.Y H:i') }}
                    </td>
                    <td class="px-4 py-3 text-center">
                        <div class="flex justify-center gap-2">
                            <a href="{{ route('admin.orders.show', $order->id) }}" class="p-2 text-blue-600 hover:bg-blue-50 rounded">
                                <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 12s3.75-7.5 9.75-7.5 9.75 7.5 9.75 7.5-3.75 7.5-9.75 7.5S2.25 12 2.25 12Z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                                </svg>
                            </a>
                            <button onclick="updateStatus({{ $order->id }})" class="p-2 text-yellow-600 hover:bg-yellow-50 rounded">
                                <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 3.487a2.25 2.25 0 0 1 3.182 3.182L7.5 19.213 3 21l1.787-4.5L16.862 3.487Z" />
                                </svg>
                            </button>
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        
        @if($orders->hasPages())
        <div class="p-4 border-t">
            {{ $orders->links() }}
        </div>
        @endif
    </div>
</div>

<script>
function updateStatus(orderId) {
    // Simple status update - in production use a modal
    const statuses = ['pending', 'processing', 'shipped', 'delivered', 'cancelled'];
    const currentStatus = prompt('Introduceți statusul: pending, processing, shipped, delivered, cancelled');
    
    if (currentStatus && statuses.includes(currentStatus)) {
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
}
</script>
@endsection
