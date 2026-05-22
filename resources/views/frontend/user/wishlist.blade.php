@extends('layouts.app')

@section('title', 'Lista de Favorite - Magazin Online')

@section('content')
<div class="container mx-auto px-4 py-8">
    <h1 class="text-2xl font-bold mb-6">Lista de Favorite</h1>
    
    @if($wishlists->count() > 0)
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
        @foreach($wishlists as $wishlist)
        @if($wishlist->product)
        <div class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-lg transition-shadow">
            <a href="{{ route('products.show', $wishlist->product->slug) }}">
                @if($wishlist->product->image)
                <img src="{{ asset($wishlist->product->image) }}" alt="{{ $wishlist->product->name }}" class="w-full h-48 object-cover">
                @else
                <div class="w-full h-48 bg-gray-200 flex items-center justify-center">
                    <i class="fas fa-image text-4xl text-gray-400"></i>
                </div>
                @endif
            </a>
            
            <div class="p-4">
                <a href="{{ route('products.show', $wishlist->product->slug) }}" class="font-semibold text-gray-800 hover:text-blue-600 line-clamp-2">
                    {{ $wishlist->product->name }}
                </a>
                
                <div class="mt-2 flex items-center gap-2">
                    @if($wishlist->product->is_on_sale && $wishlist->product->special_price)
                    <span class="text-lg font-bold text-red-600">{{ number_format($wishlist->product->special_price, 2) }} lei</span>
                    <span class="text-sm text-gray-500 line-through">{{ number_format($wishlist->product->price, 2) }} lei</span>
                    @else
                    <span class="text-lg font-bold text-blue-600">{{ number_format($wishlist->product->price, 2) }} lei</span>
                    @endif
                </div>
                
                @if($wishlist->product->stock > 0)
                <span class="text-xs text-green-600"><i class="fas fa-check-circle mr-1"></i>În stoc</span>
                @else
                <span class="text-xs text-red-600"><i class="fas fa-times-circle mr-1"></i>Nu este în stoc</span>
                @endif
                
                <div class="mt-4 flex gap-2">
                    @if($wishlist->product->stock > 0)
                    <button onclick="addToCart({{ $wishlist->product->id }})" class="flex-1 bg-blue-600 text-white py-2 rounded-lg text-sm font-semibold hover:bg-blue-700">
                        <i class="fas fa-shopping-cart mr-1"></i>Adaugă în Coș
                    </button>
                    @endif
                    <button onclick="removeFromWishlist({{ $wishlist->product->id }})" class="px-3 py-2 border border-gray-300 rounded-lg hover:bg-gray-50">
                        <i class="fas fa-trash text-red-500"></i>
                    </button>
                </div>
            </div>
        </div>
        @endif
        @endforeach
    </div>
    @else
    <div class="text-center py-12">
        <i class="fas fa-heart text-6xl text-gray-300 mb-4"></i>
        <h3 class="text-xl font-semibold text-gray-600 mb-2">Nu ai produse favorite</h3>
        <p class="text-gray-500 mb-6">Adaugă produse la favorite pentru a le găsi ușor</p>
        <a href="{{ route('products.index') }}" class="inline-block bg-blue-600 text-white px-8 py-3 rounded-lg font-semibold hover:bg-blue-700">
            Vezi Produse
        </a>
    </div>
    @endif
</div>

<script>
function addToCart(productId) {
    fetch('{{ route("cart.add") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({ product_id: productId, quantity: 1 })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            Swal.fire({
                icon: 'success',
                title: 'Succes!',
                text: 'Produsul a fost adăugat în coș',
                showConfirmButton: false,
                timer: 1500
            });
            updateCartCount();
        } else {
            Swal.fire({
                icon: 'error',
                title: 'Eroare!',
                text: data.message
            });
        }
    });
}

function removeFromWishlist(productId) {
    fetch('{{ route("wishlist.remove") }}', {
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

function updateCartCount() {
    fetch('{{ route("cart.count") }}')
        .then(response => response.json())
        .then(data => {
            document.getElementById('cart-count').textContent = data.count;
        });
}
</script>
@endsection