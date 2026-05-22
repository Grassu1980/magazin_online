<header class="bg-white shadow-md sticky top-0 z-50">
    <!-- Top Bar -->
    <div class="bg-primary text-white py-2">
        <div class="container mx-auto px-4 flex justify-between items-center text-sm">
            <div class="flex items-center space-x-4">
                @if(setting('logo'))
                <img src="{{ Storage::url(setting('logo')) }}" alt="{{ setting('site_name', 'MagazinOnline') }}" class="{{ setting('logo_size', 'h-8') }}">
                @endif
                <span><i class="fas fa-phone mr-2"></i>{{ setting('phone') }}</span>
                <span><i class="fas fa-envelope mr-2"></i>{{ setting('email') }}</span>
            </div>
            <div class="flex items-center space-x-4">
                @if(setting('facebook'))
                <a href="{{ setting('facebook') }}" target="_blank" class="hover:opacity-80"><i class="fab fa-facebook"></i></a>
                @endif
                @if(setting('instagram'))
                <a href="{{ setting('instagram') }}" target="_blank" class="hover:opacity-80"><i class="fab fa-instagram"></i></a>
                @endif
                @if(setting('youtube'))
                <a href="{{ setting('youtube') }}" target="_blank" class="hover:opacity-80"><i class="fab fa-youtube"></i></a>
                @endif
                @if(setting('whatsapp'))
                <a href="{{ setting('whatsapp') }}" target="_blank" class="hover:opacity-80"><i class="fab fa-whatsapp"></i></a>
                @endif
            </div>
        </div>
    </div>
    
    <!-- Main Header -->
    <div class="container mx-auto px-4" style="padding-top: 0.1cm; padding-bottom: 0.1cm;">
        <div class="flex items-center justify-between">
            <!-- Logo -->
            <a href="{{ route('home') }}" class="flex items-center">
                @if(setting('logo'))
                    <img src="{{ Storage::url(setting('logo')) }}" alt="{{ setting('site_name', 'MagazinOnline') }}" class="{{ setting('logo_size', 'h-8') }} mr-2">
                @else
                    <i class="fas fa-shopping-bag mr-2 text-xl text-blue-600"></i>
                @endif
                <span class="text-xl font-bold text-blue-600">{{ setting('site_name', 'MagazinOnline') }}</span>
            </a>

            <!-- Search -->
            <form action="{{ route('products.index') }}" method="GET" class="flex-1 max-w-xl mx-4">
                <div class="flex">
                    <input type="text" name="search" placeholder="Caută..." 
                           class="w-full px-2 py-1 text-sm border border-gray-300 rounded-l-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                           value="{{ request('search') }}">
                    <button type="submit" class="bg-blue-600 text-white px-3 py-1 text-sm rounded-r-lg hover:bg-blue-700">
                        <i class="fas fa-search"></i>
                    </button>
                </div>
            </form>
            
            <!-- Actions -->
            <div class="flex items-center space-x-4">
                <!-- Wishlist -->
                @auth
                <a href="{{ route('wishlist.index') }}" class="relative text-gray-600 hover:text-blue-600">
                    <i class="fas fa-heart text-lg"></i>
                    @if(Auth::user()->wishlists()->count() > 0)
                    <span class="absolute -top-1 -right-1 bg-red-500 text-white text-xs rounded-full w-4 h-4 flex items-center justify-center">
                        {{ Auth::user()->wishlists()->count() }}
                    </span>
                    @endif
                </a>
                @endauth

                <!-- Cart -->
                <a href="{{ route('cart.index') }}" class="relative text-gray-600 hover:text-blue-600">
                    <i class="fas fa-shopping-cart text-lg"></i>
                    <span id="cart-count" class="absolute -top-1 -right-1 bg-red-500 text-white text-xs rounded-full w-4 h-4 flex items-center justify-center">
                        {{ collect(session('cart', []))->sum('quantity') }}
                    </span>
                </a>

                <!-- User Menu -->
                @auth
                <div class="relative group">
                    <button class="text-gray-600 hover:text-blue-600 flex items-center text-sm">
                        <i class="fas fa-user-circle text-lg mr-1"></i>
                        <span class="ml-1 hidden lg:inline">{{ Auth::user()->name }}</span>
                        <i class="fas fa-chevron-down ml-1 text-xs"></i>
                    </button>
                    <div class="absolute right-0 mt-2 w-48 bg-white rounded-lg shadow-lg py-2 hidden group-hover:block">
                        <a href="{{ route('account.index') }}" class="block px-4 py-2 text-gray-700 hover:bg-blue-50">
                            <i class="fas fa-user mr-2"></i>Contul meu
                        </a>
                        <a href="{{ route('account.orders') }}" class="block px-4 py-2 text-gray-700 hover:bg-blue-50">
                            <i class="fas fa-box mr-2"></i>Comenzile mele
                        </a>
                        <hr class="my-2">
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="block w-full text-left px-4 py-2 text-red-600 hover:bg-red-50">
                                <i class="fas fa-sign-out-alt mr-2"></i>Deconectare
                            </button>
                        </form>
                    </div>
                </div>
                @else
                <div class="flex items-center space-x-2">
                    <a href="{{ route('login') }}" class="text-gray-600 hover:text-blue-600 flex items-center text-sm">
                        <i class="fas fa-sign-in-alt text-lg"></i>
                        <span class="ml-1 hidden lg:inline">Autentificare</span>
                    </a>
                    <a href="{{ route('register') }}" class="bg-blue-600 text-white px-3 py-1 rounded-lg hover:bg-blue-700 text-sm flex items-center">
                        <i class="fas fa-user-plus mr-1"></i>
                        <span class="hidden lg:inline">Înregistrare</span>
                    </a>
                </div>
                @endauth
            </div>
        </div>
    </div>
    
    <!-- Navigation -->
    <nav class="bg-gray-800 text-white">
        <div class="container mx-auto px-4">
            <div class="flex items-center space-x-8 py-3 overflow-x-auto">
                <a href="{{ route('home') }}" class="hover:text-blue-400 whitespace-nowrap">Acasă</a>
                <a href="{{ route('products.index') }}" class="hover:text-blue-400 whitespace-nowrap">Toate Produsele</a>
                @foreach(\App\Models\Category::where('is_active', true)->orderBy('name')->take(6)->get() as $category)
                <a href="{{ route('products.index', ['category' => $category->id]) }}" class="hover:text-blue-400 whitespace-nowrap">
                    {{ $category->name }}
                </a>
                @endforeach
            </div>
        </div>
    </nav>
</header>

<script>
function updateCartCount() {
    fetch('{{ route("cart.count") }}')
        .then(response => response.json())
        .then(data => {
            document.getElementById('cart-count').textContent = data.count;
        });
}
</script>