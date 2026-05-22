@extends('layouts.app')

@section('title', 'Detalii Comandă - Magazin Online')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="mb-6">
        <a href="{{ route('account.orders') }}" class="text-blue-600 hover:text-blue-700">
            <i class="fas fa-arrow-left mr-1"></i>Înapoi la comenzi
        </a>
    </div>
    
    <div class="bg-white rounded-lg shadow-md p-6 mb-6">
        <div class="flex flex-wrap justify-between items-start gap-4 mb-6">
            <div>
                <h1 class="text-2xl font-bold">Comanda {{ $order->order_number }}</h1>
                <p class="text-gray-500">Plasată la {{ $order->created_at->format('d.m.Y H:i') }}</p>
            </div>
            <span class="px-3 py-1 rounded-full text-sm font-medium
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
        </div>
        
        <!-- Order Timeline -->
        <div class="mb-6">
            <h3 class="font-semibold mb-3">Status Comandă</h3>
            <div class="flex items-center justify-between">
                <div class="flex items-center">
                    <div class="w-8 h-8 rounded-full bg-green-600 flex items-center justify-center">
                        <i class="fas fa-check text-white text-sm"></i>
                    </div>
                    <span class="ml-2 text-sm font-medium">Comandă plasată</span>
                </div>
                <div class="flex-1 h-1 bg-gray-200 mx-4">
                    <div class="h-full bg-green-600" style="width: {{ $order->status != 'pending' ? '100%' : '0%' }}"></div>
                </div>
                <div class="flex items-center">
                    <div class="w-8 h-8 rounded-full {{ in_array($order->status, ['processing', 'shipped', 'delivered']) ? 'bg-green-600' : 'bg-gray-300' }} flex items-center justify-center">
                        <i class="fas fa-cog text-white text-sm"></i>
                    </div>
                    <span class="ml-2 text-sm font-medium">Se procesează</span>
                </div>
                <div class="flex-1 h-1 bg-gray-200 mx-4">
                    <div class="h-full bg-green-600" style="width: {{ in_array($order->status, ['shipped', 'delivered']) ? '100%' : '0%' }}"></div>
                </div>
                <div class="flex items-center">
                    <div class="w-8 h-8 rounded-full {{ in_array($order->status, ['shipped', 'delivered']) ? 'bg-green-600' : 'bg-gray-300' }} flex items-center justify-center">
                        <i class="fas fa-truck text-white text-sm"></i>
                    </div>
                    <span class="ml-2 text-sm font-medium">Expediat</span>
                </div>
                <div class="flex-1 h-1 bg-gray-200 mx-4">
                    <div class="h-full bg-green-600" style="width: {{ $order->status == 'delivered' ? '100%' : '0%' }}"></div>
                </div>
                <div class="flex items-center">
                    <div class="w-8 h-8 rounded-full {{ $order->status == 'delivered' ? 'bg-green-600' : 'bg-gray-300' }} flex items-center justify-center">
                        <i class="fas fa-home text-white text-sm"></i>
                    </div>
                    <span class="ml-2 text-sm font-medium">Livrat</span>
                </div>
            </div>
        </div>
    </div>
    
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Order Items -->
        <div class="lg:col-span-2">
            <div class="bg-white rounded-lg shadow-md p-6">
                <h2 class="text-lg font-semibold mb-4">Produse Comandate</h2>
                
                <div class="space-y-4">
                    @foreach($order->items as $item)
                    <div class="flex items-center gap-4 pb-4 border-b last:border-0">
                        @if($item->product && $item->product->image)
                        <img src="{{ asset($item->product->image) }}" alt="{{ $item->product_name }}" class="w-16 h-16 object-cover rounded">
                        @else
                        <div class="w-16 h-16 bg-gray-200 rounded flex items-center justify-center">
                            <i class="fas fa-image text-gray-400"></i>
                        </div>
                        @endif
                        <div class="flex-1">
                            <a href="{{ $item->product ? route('products.show', $item->product->slug) : '#' }}" class="font-semibold hover:text-blue-600">
                                {{ $item->product_name }}
                            </a>
                            <p class="text-sm text-gray-500">SKU: {{ $item->product_sku }}</p>
                        </div>
                        <div class="text-right">
                            <p class="text-sm text-gray-500">{{ $item->quantity }} x {{ number_format($item->price, 2) }} lei</p>
                            <p class="font-semibold">{{ number_format($item->subtotal, 2) }} lei</p>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
        
        <!-- Order Summary -->
        <div>
            <div class="bg-white rounded-lg shadow-md p-6 mb-6">
                <h2 class="text-lg font-semibold mb-4">Sumar Comandă</h2>
                
                <div class="space-y-3">
                    <div class="flex justify-between">
                        <span class="text-gray-600">Subtotal</span>
                        <span>{{ number_format($order->subtotal, 2) }} lei</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Transport</span>
                        <span>{{ $order->shipping_cost > 0 ? number_format($order->shipping_cost, 2) . ' lei' : 'Gratuit' }}</span>
                    </div>
                    <hr>
                    <div class="flex justify-between text-lg">
                        <span class="font-semibold">Total</span>
                        <span class="font-bold text-blue-600">{{ number_format($order->total, 2) }} lei</span>
                    </div>
                </div>
            </div>
            
            <!-- Shipping Info -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <h2 class="text-lg font-semibold mb-4">Informații Livrare</h2>
                
                <div class="space-y-2 text-sm">
                    <div>
                        <p class="text-gray-500">Nume</p>
                        <p class="font-medium">{{ $order->customer_name }}</p>
                    </div>
                    <div>
                        <p class="text-gray-500">Adresă</p>
                        <p class="font-medium">{{ $order->shipping_address }}</p>
                    </div>
                    <div>
                        <p class="text-gray-500">Oraș</p>
                        <p class="font-medium">{{ $order->shipping_city }}</p>
                    </div>
                    <div>
                        <p class="text-gray-500">Telefon</p>
                        <p class="font-medium">{{ $order->customer_phone }}</p>
                    </div>
                    @if($order->notes)
                    <div>
                        <p class="text-gray-500">Observații</p>
                        <p class="font-medium">{{ $order->notes }}</p>
                    </div>
                    @endif
                </div>
            </div>
            
            <!-- Payment Info -->
            <div class="bg-white rounded-lg shadow-md p-6 mt-6">
                <h2 class="text-lg font-semibold mb-4">Informații Plată</h2>
                
                <div class="space-y-2 text-sm">
                    <div>
                        <p class="text-gray-500">Metodă de Plată</p>
                        <p class="font-medium">{{ $order->payment_method == 'cash' ? 'Plata la livrare' : 'Plată cu cardul' }}</p>
                    </div>
                    <div>
                        <p class="text-gray-500">Status Plată</p>
                        <span class="px-2 py-1 rounded text-xs font-medium {{ $order->payment_status == 'paid' ? 'bg-green-100 text-green-700' : 'bg-yellow-100 text-yellow-700' }}">
                            {{ $order->payment_status == 'paid' ? 'Plătit' : 'În așteptare' }}
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection