<footer class="bg-gray-900 text-white mt-16">
    <div class="container mx-auto px-4 py-12">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
            <!-- About -->
            <div>
                <h3 class="text-xl font-bold mb-4 flex items-center">
                    @if(setting('logo'))
                        <img src="{{ Storage::url(setting('logo')) }}" alt="{{ setting('site_name', 'MagazinOnline') }}" class="h-8 mr-2">
                    @else
                        <i class="fas fa-shopping-bag mr-2"></i>
                    @endif
                    {{ setting('site_name', 'MagazinOnline') }}
                </h3>
                <p class="text-gray-400 text-sm">
                    {{ setting('meta_description', 'Magazinul tău online de încredere. Oferim produse de calitate la cele mai bune prețuri.') }}
                </p>
                <div class="flex space-x-4 mt-4">
                    @if(setting('facebook'))
                    <a href="{{ setting('facebook') }}" target="_blank" class="text-gray-400 hover:text-white"><i class="fab fa-facebook text-xl"></i></a>
                    @endif
                    @if(setting('instagram'))
                    <a href="{{ setting('instagram') }}" target="_blank" class="text-gray-400 hover:text-white"><i class="fab fa-instagram text-xl"></i></a>
                    @endif
                    @if(setting('youtube'))
                    <a href="{{ setting('youtube') }}" target="_blank" class="text-gray-400 hover:text-white"><i class="fab fa-youtube text-xl"></i></a>
                    @endif
                </div>
            </div>
            
            <!-- Quick Links -->
            <div>
                <h4 class="text-lg font-semibold mb-4">Link-uri Rapide</h4>
                <ul class="space-y-2">
                    <li><a href="{{ route('home') }}" class="text-gray-400 hover:text-white">Acasă</a></li>
                    <li><a href="{{ route('products.index') }}" class="text-gray-400 hover:text-white">Produse</a></li>
                    <li><a href="{{ route('cart.index') }}" class="text-gray-400 hover:text-white">Coș</a></li>
                    @auth
                    <li><a href="{{ route('account.orders') }}" class="text-gray-400 hover:text-white">Comenzile mele</a></li>
                    @else
                    <li><a href="{{ route('login') }}" class="text-gray-400 hover:text-white">Autentificare</a></li>
                    @endauth
                </ul>
            </div>
            
            <!-- Categories -->
            <div>
                <h4 class="text-lg font-semibold mb-4">Categorii</h4>
                <ul class="space-y-2">
                    @foreach(\App\Models\Category::where('is_active', true)->orderBy('name')->take(5)->get() as $category)
                    <li>
                        <a href="{{ route('products.index', ['category' => $category->id]) }}" class="text-gray-400 hover:text-white">
                            {{ $category->name }}
                        </a>
                    </li>
                    @endforeach
                </ul>
            </div>
            
            <!-- Contact -->
            <div>
                <h4 class="text-lg font-semibold mb-4">Contact</h4>
                <ul class="space-y-2 text-gray-400">
                    @if(setting('address'))
                    <li><i class="fas fa-map-marker-alt mr-2"></i>{{ setting('address') }}</li>
                    @endif
                    @if(setting('phone'))
                    <li><i class="fas fa-phone mr-2"></i>{{ setting('phone') }}</li>
                    @endif
                    @if(setting('email'))
                    <li><i class="fas fa-envelope mr-2"></i>{{ setting('email') }}</li>
                    @endif
                </ul>
            </div>
        </div>
        
        <div class="border-t border-gray-800 mt-8 pt-8 text-center text-gray-400 text-sm">
            <p>&copy; {{ date('Y') }} {{ setting('site_name', 'MagazinOnline') }}. Toate drepturile rezervate.</p>
        </div>
    </div>
</footer>