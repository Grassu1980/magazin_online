@extends('layouts.app')

@section('title', 'Coș de Cumpărături - Magazin Online')

@section('content')
<div class="container mx-auto px-4 py-8">
    <h1 class="text-2xl font-bold mb-6">Coșul de Cumpărături</h1>
    
    @if(count($cartItems) > 0)
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Cart Items -->
        <div class="lg:col-span-2">
            <div class="bg-white rounded-lg shadow-md overflow-hidden">
                <div class="p-4 border-b bg-gray-50">
                    <div class="grid grid-cols-12 gap-4 text-sm font-semibold text-gray-600">
                        <div class="col-span-5">Produs</div>
                        <div class="col-span-2 text-center">Preț</div>
                        <div class="col-span-3 text-center">Cantitate</div>
                        <div class="col-span-2 text-right">Total</div>
                    </div>
                </div>
                
                @foreach($cartItems as $index => $item)
                <div class="p-4 border-b">
                    <div class="grid grid-cols-12 gap-4 items-center">
                        <div class="col-span-5 flex items-center gap-4">
                            @if($item['product']->image)
                            <img src="{{ asset($item['product']->image) }}" alt="{{ $item['product']->name }}" class="w-16 h-16 object-cover rounded">
                            @else
                            <div class="w-16 h-16 bg-gray-200 rounded flex items-center justify-center">
                                <i class="fas fa-image text-gray-400"></i>
                            </div>
                            @endif
                            <div>
                                <a href="{{ route('products.show', $item['product']->slug) }}" class="font-semibold text-gray-800 hover:text-blue-600">
                                    {{ $item['product']->name }}
                                </a>
                                <p class="text-sm text-gray-500">SKU: {{ $item['product']->sku }}</p>
                            </div>
                        </div>
                        
                        <div class="col-span-2 text-center">
                            {{ number_format($item['price'], 2) }} lei
                        </div>
                        
                        <div class="col-span-3 flex justify-center">
                            <div class="flex items-center border border-gray-300 rounded">
                                <button onclick="updateCart({{ $item['product']->id }}, {{ $item['quantity'] - 1 }})" 
                                        class="px-3 py-1 hover:bg-gray-100">-</button>
                                <input type="number" value="{{ $item['quantity'] }}" readonly 
                                       class="w-12 text-center border-none focus:ring-0">
                                <button onclick="updateCart({{ $item['product']->id }}, {{ $item['quantity'] + 1 }})" 
                                        class="px-3 py-1 hover:bg-gray-100">+</button>
                            </div>
                        </div>
                        
                        <div class="col-span-2 text-right flex items-center justify-end gap-2">
                            <span class="font-semibold">{{ number_format($item['subtotal'], 2) }} lei</span>
                            <button onclick="removeFromCart({{ $item['product']->id }})" 
                                    class="text-red-500 hover:text-red-700 p-1">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    </div>
                </div>
                @endforeach
                
                <div class="p-4">
                    <form action="{{ route('cart.clear') }}" method="POST">
                        @csrf
                        <button type="submit" class="text-red-600 hover:text-red-700 text-sm">
                            <i class="fas fa-trash mr-1"></i>Golește coșul
                        </button>
                    </form>
                </div>
            </div>
        </div>
        
        <!-- Cart Summary -->
        <div>
            <div class="bg-white rounded-lg shadow-md p-6">
                <h3 class="text-lg font-semibold mb-4">Sumar Comandă</h3>
                
                <div class="space-y-3 mb-6">
                    <div class="flex justify-between">
                        <span class="text-gray-600">Subtotal</span>
                        <span class="font-semibold">{{ number_format($total, 2) }} lei</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Transport</span>
                        <span class="font-semibold">{{ $total > 200 ? 'Gratuit' : '15.00 lei' }}</span>
                    </div>
                    <hr>
                    <div class="flex justify-between text-lg">
                        <span class="font-semibold">Total</span>
                        <span class="font-bold text-blue-600">{{ number_format($total + ($total > 200 ? 0 : 15), 2) }} lei</span>
                    </div>
                </div>
                
                @if($total < 200)
                <div class="bg-blue-50 text-blue-700 p-3 rounded-lg text-sm mb-6">
                    <i class="fas fa-info-circle mr-2"></i>Adaugă încă {{ number_format(200 - $total, 2) }} lei pentru transport gratuit!
                </div>
                @endif
                
                <a href="{{ route('checkout.index') }}" class="block w-full bg-blue-600 text-white text-center py-3 rounded-lg font-semibold hover:bg-blue-700 transition-colors">
                    Finalizează Comanda
                </a>
                
                <a href="{{ route('products.index') }}" class="block w-full text-center text-blue-600 hover:text-blue-700 mt-4">
                    <i class="fas fa-arrow-left mr-1"></i>Continuă cumpărăturile
                </a>
            </div>
        </div>
    </div>
    @else
    <div class="text-center py-12">
        <i class="fas fa-shopping-cart text-6xl text-gray-300 mb-4"></i>
        <h3 class="text-xl font-semibold text-gray-600 mb-2">Coșul este gol</h3>
        <p class="text-gray-500 mb-6">Adaugă produse în coș pentru a continua</p>
        <a href="{{ route('products.index') }}" class="inline-block bg-blue-600 text-white px-8 py-3 rounded-lg font-semibold hover:bg-blue-700">
            Vezi Produse
        </a>
    </div>
    @endif
</div>

<script>
function updateCart(productId, quantity) {
    fetch('{{ route("cart.update") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({ product_id: productId, quantity: quantity })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        } else {
            Swal.fire({
                icon: 'error',
                title: 'Eroare!',
                text: data.message
            });
        }
    });
}

function removeFromCart(productId) {
    fetch('{{ route("cart.remove") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({ product_id: productId })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        }
    });
}
</script>
@endsection