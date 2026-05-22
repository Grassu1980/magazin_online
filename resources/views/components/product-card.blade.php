<div class="product-card group bg-white rounded-2xl shadow-lg overflow-hidden hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-1">
    <a href="{{ route('products.show', $product->slug) }}" class="block">
        <div class="relative overflow-hidden">
            @if($product->image)
            <div class="relative h-64 overflow-hidden">
                <img src="{{ asset($product->image) }}" alt="{{ $product->name }}" 
                     class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-110">
                @if($product->images && $product->images->count() > 1)
                <div class="absolute bottom-2 left-1/2 transform -translate-x-1/2 flex gap-1">
                    @foreach($product->images->take(3) as $index => $image)
                    <div class="w-2 h-2 rounded-full {{ $index === 0 ? 'bg-white' : 'bg-white/50' }}"></div>
                    @endforeach
                </div>
                @endif
            </div>
            @else
            <div class="w-full h-64 bg-gradient-to-br from-gray-100 to-gray-200 flex items-center justify-center">
                <i class="fas fa-image text-6xl text-gray-300"></i>
            </div>
            @endif

            @if($product->is_on_sale)
            <span class="absolute top-3 left-3 bg-gradient-to-r from-red-500 to-red-600 text-white text-xs font-bold px-3 py-1.5 rounded-full shadow-lg">
                -{{ $product->discount_percentage }}%
            </span>
            @endif

            @if($product->is_featured)
            <span class="absolute top-3 right-3 bg-gradient-to-r from-yellow-400 to-yellow-500 text-white text-xs font-bold px-3 py-1.5 rounded-full shadow-lg">
                <i class="fas fa-star mr-1"></i>Recomandat
            </span>
            @endif

            @if($product->stock == 0)
            <div class="absolute inset-0 bg-black/60 backdrop-blur-sm flex items-center justify-center">
                <span class="bg-red-600 text-white font-bold px-6 py-3 rounded-xl shadow-lg">Stoc Epuizat</span>
            </div>
            @endif

            <div class="absolute inset-0 bg-gradient-to-t from-black/20 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
        </div>

        <div class="p-5">
            <p class="text-xs font-medium text-blue-600 mb-2 uppercase tracking-wide">{{ $product->category->name ?? '' }}</p>
            <h3 class="font-bold text-gray-900 mb-3 line-clamp-2 text-lg group-hover:text-blue-600 transition-colors">{{ $product->name }}</h3>

            <div class="flex items-center justify-between mb-3">
                <div>
                    @if($product->is_on_sale)
                    <div class="flex items-baseline gap-2">
                        <span class="text-2xl font-bold text-red-600">{{ number_format($product->special_price, 2) }} lei</span>
                        <span class="text-sm text-gray-400 line-through">{{ number_format($product->price, 2) }} lei</span>
                    </div>
                    @else
                    <span class="text-2xl font-bold text-gray-900">{{ number_format($product->price, 2) }} lei</span>
                    @endif
                </div>
            </div>

            <div class="flex items-center justify-between text-xs text-gray-500 mb-4">
                <span class="flex items-center"><i class="fas fa-box mr-1.5 text-green-500"></i>Stoc: {{ $product->stock }}</span>
                <span class="flex items-center"><i class="fas fa-eye mr-1.5 text-blue-500"></i>{{ $product->views }}</span>
            </div>
        </div>
    </a>

    <div class="px-5 pb-5 flex gap-2">
        @auth
        <button onclick="toggleWishlist({{ $product->id }})" class="flex-1 bg-gray-100 text-gray-600 py-2.5 rounded-xl hover:bg-red-50 hover:text-red-500 transition-all duration-300 wishlist-btn" data-product-id="{{ $product->id }}">
            <i class="fas fa-heart"></i>
        </button>
        @endauth

        @if($product->stock > 0)
        <button onclick="addToCart({{ $product->id }}, 1)" class="flex-1 bg-gradient-to-r from-blue-600 to-blue-700 text-white py-2.5 rounded-xl hover:from-blue-700 hover:to-blue-800 transition-all duration-300 shadow-md hover:shadow-lg">
            <i class="fas fa-cart-plus mr-1.5"></i>Adaugă
        </button>
        @else
        <button disabled class="flex-1 bg-gray-200 text-gray-400 py-2.5 rounded-xl cursor-not-allowed">
            Indisponibil
        </button>
        @endif
    </div>
</div>

<script>
function addToCart(productId, quantity = 1) {
    fetch('{{ route("cart.add") }}', {
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
            document.getElementById('cart-count').textContent = data.cart_count;
            Swal.fire({
                icon: 'success',
                title: 'Succes!',
                text: data.message,
                timer: 2000,
                showConfirmButton: false
            });
        } else {
            Swal.fire({
                icon: 'error',
                title: 'Eroare!',
                text: data.message
            });
        }
    });
}

function toggleWishlist(productId) {
    fetch('{{ route("wishlist.toggle") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({ product_id: productId })
    })
    .then(response => response.json())
    .then(data => {
        Swal.fire({
            icon: data.success ? 'success' : 'error',
            title: data.success ? 'Succes!' : 'Eroare!',
            text: data.message,
            timer: 2000,
            showConfirmButton: false
        });
    });
}
</script>