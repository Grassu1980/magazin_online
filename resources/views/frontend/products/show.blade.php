@extends('layouts.app')

@section('title', $product->name . ' - Magazin Online')

@push('styles')
<!-- PhotoSwipe CSS -->
<link rel="stylesheet" href="https://unpkg.com/photoswipe@5.3.8/dist/photoswipe.css">
<style>
    /* Product Gallery Styles */
    .product-gallery {
        position: relative;
    }

    /* Main Image Container */
    .main-image-container {
        position: relative;
        overflow: hidden;
        background: #fff;
        border-radius: 1rem;
        box-shadow: 0 10px 40px rgba(0,0,0,0.1);
    }

    .main-image {
        width: 100%;
        height: 500px;
        object-fit: contain;
        cursor: zoom-in;
        transition: transform 0.3s ease;
    }

    /* Magnifier Zoom */
    .magnifier-result {
        position: absolute;
        top: 0;
        right: -320px;
        width: 300px;
        height: 300px;
        border: 2px solid #fff;
        border-radius: 0.5rem;
        background-repeat: no-repeat;
        background-color: #fff;
        box-shadow: 0 10px 40px rgba(0,0,0,0.2);
        display: none;
        z-index: 20;
    }

    .main-image-container:hover .magnifier-result {
        display: block;
    }

    /* Thumbnails */
    .thumbnails-container {
        display: flex;
        flex-direction: column;
        gap: 0.75rem;
    }

    .thumbnail-btn {
        position: relative;
        overflow: hidden;
        border-radius: 0.75rem;
        border: 2px solid #e5e7eb;
        transition: all 0.3s ease;
        width: 80px;
        height: 80px;
        flex-shrink: 0;
        cursor: pointer;
    }

    .thumbnail-btn:hover {
        box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        border-color: #2563eb;
    }

    .thumbnail-btn.active {
        border-color: #2563eb;
        box-shadow: 0 0 0 2px rgba(37, 99, 235, 0.3);
    }

    .thumbnail-btn img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    /* Navigation Arrows */
    .nav-arrow {
        position: absolute;
        top: 50%;
        transform: translateY(-50%);
        background: rgba(255,255,255,0.95);
        backdrop-filter: blur(8px);
        padding: 0.75rem;
        border-radius: 50%;
        box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        transition: all 0.3s ease;
        opacity: 0;
        z-index: 5;
    }

    .main-image-container:hover .nav-arrow {
        opacity: 1;
    }

    .nav-arrow:hover {
        background: #fff;
        transform: translateY(-50%) scale(1.1);
    }

    .nav-arrow.prev {
        left: 1rem;
    }

    .nav-arrow.next {
        right: 1rem;
    }

    /* Expand Button */
    .expand-btn {
        position: absolute;
        top: 1rem;
        right: 1rem;
        background: rgba(255,255,255,0.95);
        backdrop-filter: blur(8px);
        padding: 0.5rem;
        border-radius: 50%;
        box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        transition: all 0.3s ease;
        opacity: 0;
        z-index: 5;
    }

    .main-image-container:hover .expand-btn {
        opacity: 1;
    }

    .expand-btn:hover {
        background: #fff;
        transform: scale(1.1);
    }

    /* Mobile Responsive */
    @media (max-width: 1024px) {
        .thumbnails-container {
            flex-direction: row;
            overflow-x: auto;
            padding-bottom: 0.5rem;
            scrollbar-width: thin;
            scrollbar-color: #cbd5e1 #f1f5f9;
        }

        .thumbnails-container::-webkit-scrollbar {
            height: 6px;
        }

        .thumbnails-container::-webkit-scrollbar-track {
            background: #f1f5f9;
            border-radius: 3px;
        }

        .thumbnails-container::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 3px;
        }

        .magnifier-result {
            display: none !important;
        }

        .main-image {
            height: 400px;
        }
    }

    @media (max-width: 640px) {
        .main-image {
            height: 300px;
        }

        .thumbnail-btn {
            width: 60px;
            height: 60px;
        }
    }

    /* PhotoSwipe Custom Styles */
    .pswp__bg {
        background: rgba(0, 0, 0, 0.95);
    }

    .pswp__button--arrow--left::before,
    .pswp__button--arrow--right::before {
        background-color: #fff;
    }
</style>
@endpush

@section('content')
<div class="container mx-auto px-4 py-8">
    <!-- Breadcrumb -->
    <nav class="text-sm mb-6">
        <ol class="list-none p-0 inline-flex">
            <li class="flex items-center">
                <a href="{{ route('home') }}" class="text-blue-600 hover:text-blue-700">Acasă</a>
                <i class="fas fa-chevron-right text-gray-400 mx-2"></i>
            </li>
            <li class="flex items-center">
                <a href="{{ route('products.index') }}" class="text-blue-600 hover:text-blue-700">Produse</a>
                <i class="fas fa-chevron-right text-gray-400 mx-2"></i>
            </li>
            @if($product->category)
            <li class="flex items-center">
                <a href="{{ route('products.index', ['category' => $product->category->id]) }}" class="text-blue-600 hover:text-blue-700">
                    {{ $product->category->name }}
                </a>
                <i class="fas fa-chevron-right text-gray-400 mx-2"></i>
            </li>
            @endif
            <li class="text-gray-600">{{ $product->name }}</li>
        </ol>
    </nav>
    
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        <!-- Product Images Gallery -->
        <div class="product-gallery flex gap-4">
            <!-- Thumbnail Images - Vertical on Desktop, Horizontal on Mobile -->
            @if($product->all_images && count($product->all_images) > 1)
            <div class="thumbnails-container order-2 lg:order-1">
                @foreach($product->all_images as $index => $image)
                <button 
                    data-index="{{ $index }}"
                    data-src="{{ asset($image) }}"
                    onclick="changeImage('{{ asset($image) }}', this)"
                    class="thumbnail-btn {{ $index === 0 ? 'active' : '' }}">
                    <img src="{{ asset($image) }}" alt="{{ $product->name }}" loading="lazy">
                </button>
                @endforeach
            </div>
            @endif

            <!-- Main Image with Magnifier -->
            <div class="flex-1 order-1 lg:order-2">
                <div class="main-image-container" id="main-image-container">
                    @if($product->image)
                    <div class="relative">
                        <img 
                            id="main-image" 
                            src="{{ asset($product->image) }}" 
                            data-pswp-src="{{ asset($product->image) }}"
                            data-pswp-width="800" 
                            data-pswp-height="800"
                            alt="{{ $product->name }}"
                            class="main-image"
                            loading="eager"
                            onclick="openPhotoSwipe({{ $product->all_images ? json_encode(collect($product->all_images)->map(fn($img) => asset($img))->toArray()) : json_encode([asset($product->image)]) }}, {{ $product->all_images ? collect($product->all_images)->search($product->image) : 0 }})">
                        
                        <!-- Magnifier Result Window -->
                        <div id="magnifier-result" class="magnifier-result"></div>
                        
                        <!-- Navigation Arrows -->
                        @if($product->all_images && count($product->all_images) > 1)
                        <button onclick="navigateImage(-1)" class="nav-arrow prev">
                            <i class="fas fa-chevron-left text-gray-700"></i>
                        </button>
                        <button onclick="navigateImage(1)" class="nav-arrow next">
                            <i class="fas fa-chevron-right text-gray-700"></i>
                        </button>
                        @endif

                        <!-- Expand Button -->
                        <button onclick="openPhotoSwipe({{ $product->all_images ? json_encode(collect($product->all_images)->map(fn($img) => asset($img))->toArray()) : json_encode([asset($product->image)]) }}, {{ $product->all_images ? collect($product->all_images)->search($product->image) : 0 }})" class="expand-btn">
                            <i class="fas fa-expand text-gray-700"></i>
                        </button>
                    </div>
                    @else
                    <div class="w-full h-[500px] bg-gradient-to-br from-gray-100 to-gray-200 flex items-center justify-center">
                        <i class="fas fa-image text-8xl text-gray-300"></i>
                    </div>
                    @endif
                </div>
            </div>
        </div>
        
        <!-- Product Info -->
        <div>
            <h1 class="text-3xl font-bold text-gray-800 mb-2">{{ $product->name }}</h1>
            
            <div class="flex items-center gap-4 mb-4">
                <span class="text-sm text-gray-500">SKU: {{ $product->sku }}</span>
                <span class="text-sm text-gray-500">|</span>
                <span class="text-sm text-gray-500"><i class="fas fa-eye mr-1"></i>{{ $product->views }} vizualizări</span>
                <span class="text-sm text-gray-500">|</span>
                <span class="text-sm text-gray-500"><i class="fas fa-box mr-1"></i>{{ $product->sold_count }} vândute</span>
            </div>
            
            <!-- Price -->
            <div class="mb-6">
                @if($product->is_on_sale)
                <span class="text-3xl font-bold text-red-600">{{ number_format($product->special_price, 2) }} lei</span>
                <span class="text-xl text-gray-400 line-through ml-3">{{ number_format($product->price, 2) }} lei</span>
                <span class="ml-3 bg-red-500 text-white text-sm font-bold px-2 py-1 rounded">-{{ $product->discount_percentage }}%</span>
                @else
                <span class="text-3xl font-bold text-gray-800">{{ number_format($product->price, 2) }} lei</span>
                @endif
            </div>
            
            <!-- Stock Status -->
            <div class="mb-6">
                @if($product->stock > 0)
                <span class="inline-flex items-center text-green-600">
                    <i class="fas fa-check-circle mr-2"></i>
                    În stoc ({{ $product->stock }} buc)
                </span>
                @else
                <span class="inline-flex items-center text-red-600">
                    <i class="fas fa-times-circle mr-2"></i>
                    Stoc epuizat
                </span>
                @endif
            </div>
            
            <!-- Add to Cart -->
            @if($product->stock > 0)
            <form class="mb-6">
                @csrf
                <div class="flex items-center gap-4">
                    <div class="flex items-center border border-gray-300 rounded-lg">
                        <button type="button" onclick="decreaseQuantity()" class="px-4 py-2 hover:bg-gray-100">-</button>
                        <input type="number" id="quantity" name="quantity" value="1" min="1" max="{{ $product->stock }}" 
                               class="w-16 text-center border-none focus:ring-0">
                        <button type="button" onclick="increaseQuantity()" class="px-4 py-2 hover:bg-gray-100">+</button>
                    </div>
                    
                    <button type="button" onclick="addToCart({{ $product->id }})" 
                            class="flex-1 bg-blue-600 text-white py-3 px-6 rounded-lg font-semibold hover:bg-blue-700 transition-colors">
                        <i class="fas fa-cart-plus mr-2"></i>Adaugă în coș
                    </button>
                    
                    @auth
                    <button type="button" onclick="toggleWishlist({{ $product->id }})" 
                            class="border border-gray-300 p-3 rounded-lg hover:bg-gray-100">
                        <i class="fas fa-heart text-xl {{ Auth::user()->wishlists()->where('product_id', $product->id)->exists() ? 'text-red-500' : 'text-gray-400' }}"></i>
                    </button>
                    @endauth
                </div>
            </form>
            @else
            <div class="bg-gray-100 text-gray-600 py-3 px-6 rounded-lg text-center mb-6">
                Produsul nu este disponibil momentan
            </div>
            @endif
            
            <!-- Description -->
            <div class="border-t pt-6">
                <h3 class="text-lg font-semibold mb-3">Descriere</h3>
                <div class="text-gray-600 prose">
                    {!! nl2br(e($product->description)) !!}
                </div>
            </div>
        </div>
    </div>
    
    <!-- Related Products -->
    @if($relatedProducts->count() > 0)
    <section class="mt-12">
        <h2 class="text-2xl font-bold mb-6">Produse Relacionate</h2>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
            @foreach($relatedProducts as $relatedProduct)
            @include('components.product-card', ['product' => $relatedProduct])
            @endforeach
        </div>
    </section>
    @endif
</div>

<!-- PhotoSwipe Container -->
<div class="pswp" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="pswp__bg"></div>
    <div class="pswp__scroll-wrap">
        <div class="pswp__container">
            <div class="pswp__item"></div>
            <div class="pswp__item"></div>
            <div class="pswp__item"></div>
        </div>
        <div class="pswp__ui pswp__ui--hidden">
            <div class="pswp__top-bar">
                <div class="pswp__counter"></div>
                <button class="pswp__button pswp__button--close" title="Close (Esc)"></button>
                <button class="pswp__button pswp__button--share" title="Share"></button>
                <button class="pswp__button pswp__button--fs" title="Toggle fullscreen"></button>
                <button class="pswp__button pswp__button--zoom" title="Zoom in/out"></button>
                <div class="pswp__preloader">
                    <div class="pswp__preloader__icn">
                        <div class="pswp__preloader__cut">
                            <div class="pswp__preloader__donut"></div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="pswp__share-modal pswp__share-modal--hidden pswp__single-tap">
                <div class="pswp__share-tooltip"></div>
            </div>
            <button class="pswp__button pswp__button--arrow--left" title="Previous (arrow left)"></button>
            <button class="pswp__button pswp__button--arrow--right" title="Next (arrow right)"></button>
            <div class="pswp__caption">
                <div class="pswp__caption__center"></div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<!-- PhotoSwipe JS -->
<script type="module">
    import PhotoSwipeLightbox from 'https://unpkg.com/photoswipe@5.3.8/dist/photoswipe-lightbox.esm.js';
    import PhotoSwipe from 'https://unpkg.com/photoswipe@5.3.8/dist/photoswipe.esm.js';

    // Gallery state
    let currentImageIndex = 0;
    const allImages = @json(collect($product->all_images ?? [$product->image])->map(fn($img) => asset($img))->toArray());

    /**
     * Change main image when thumbnail is clicked
     * @param {string} src - Image source URL
     * @param {HTMLElement} button - Clicked thumbnail button
     */
    function changeImage(src, button) {
        const mainImage = document.getElementById('main-image');
        mainImage.src = src;
        
        // Update PhotoSwipe data attributes
        mainImage.dataset.pswpSrc = src;
        
        // Update current index
        currentImageIndex = allImages.findIndex(img => img === src);
        
        // Update thumbnail active state
        const buttons = document.querySelectorAll('.thumbnail-btn');
        buttons.forEach(btn => {
            btn.classList.remove('active');
        });
        
        if (button) {
            button.classList.add('active');
        }
        
        // Reinitialize magnifier
        initMagnifier();
    }

    /**
     * Navigate to previous/next image
     * @param {number} direction - -1 for previous, 1 for next
     */
    function navigateImage(direction) {
        currentImageIndex += direction;
        if (currentImageIndex < 0) {
            currentImageIndex = allImages.length - 1;
        } else if (currentImageIndex >= allImages.length) {
            currentImageIndex = 0;
        }
        
        const newImage = allImages[currentImageIndex];
        const mainImage = document.getElementById('main-image');
        mainImage.src = newImage;
        mainImage.dataset.pswpSrc = newImage;
        
        // Update thumbnail active state
        const buttons = document.querySelectorAll('.thumbnail-btn');
        buttons.forEach((btn, index) => {
            btn.classList.remove('active');
            if (index === currentImageIndex) {
                btn.classList.add('active');
            }
        });
        
        // Reinitialize magnifier
        initMagnifier();
    }

    /**
     * Open PhotoSwipe lightbox
     * @param {Array} images - Array of image URLs
     * @param {number} startIndex - Starting image index
     */
    function openPhotoSwipe(images, startIndex = 0) {
        const items = images.map((src, index) => ({
            src: src,
            width: 800,
            height: 800,
            alt: '{{ $product->name }}'
        }));
        
        const options = {
            dataSource: items,
            index: startIndex,
            bgOpacity: 0.95,
            zoom: true,
            pinchToClose: true,
            tapAction: 'toggle-controls',
            doubleTapAction: 'zoom',
            closeTitle: 'Închide (Esc)',
            zoomTitle: 'Mărește/Micșorează',
            arrowPrevTitle: 'Anterior',
            arrowNextTitle: 'Următor'
        };
        
        const pswp = new PhotoSwipe(options);
        pswp.init();
    }

    /**
     * Initialize magnifier zoom functionality
     */
    function initMagnifier() {
        const container = document.getElementById('main-image-container');
        const img = document.getElementById('main-image');
        const result = document.getElementById('magnifier-result');
        
        if (!container || !img || !result) return;
        
        // Disable magnifier on mobile
        if (window.innerWidth < 1024) {
            result.style.display = 'none';
            return;
        }
        
        const zoomLevel = 2.5;
        
        container.addEventListener('mousemove', function(e) {
            e.preventDefault();
            
            const rect = container.getBoundingClientRect();
            const x = e.clientX - rect.left;
            const y = e.clientY - rect.top;
            
            // Calculate background position for result
            const bgX = (x / rect.width) * 100;
            const bgY = (y / rect.height) * 100;
            
            result.style.backgroundImage = `url('${img.src}')`;
            result.style.backgroundSize = `${rect.width * zoomLevel}px ${rect.height * zoomLevel}px`;
            result.style.backgroundPosition = `${bgX}% ${bgY}%`;
        });
        
        container.addEventListener('mouseleave', function() {
            result.style.display = 'none';
        });
        
        container.addEventListener('mouseenter', function() {
            if (window.innerWidth >= 1024) {
                result.style.display = 'block';
            }
        });
    }

    /**
     * Initialize touch swipe for mobile
     */
    function initTouchSwipe() {
        const container = document.getElementById('main-image-container');
        let touchStartX = 0;
        let touchEndX = 0;
        
        container.addEventListener('touchstart', function(e) {
            touchStartX = e.changedTouches[0].screenX;
        }, false);
        
        container.addEventListener('touchend', function(e) {
            touchEndX = e.changedTouches[0].screenX;
            handleSwipe();
        }, false);
        
        function handleSwipe() {
            const swipeThreshold = 50;
            const diff = touchStartX - touchEndX;
            
            if (Math.abs(diff) > swipeThreshold) {
                if (diff > 0) {
                    // Swipe left - next image
                    navigateImage(1);
                } else {
                    // Swipe right - previous image
                    navigateImage(-1);
                }
            }
        }
    }

    /**
     * Initialize lazy loading for thumbnails
     */
    function initLazyLoading() {
        const images = document.querySelectorAll('img[loading="lazy"]');
        
        if ('loading' in HTMLImageElement.prototype) {
            // Browser supports native lazy loading
            return;
        }
        
        // Fallback for browsers without native lazy loading
        const imageObserver = new IntersectionObserver((entries, observer) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    const img = entry.target;
                    img.src = img.dataset.src;
                    img.classList.remove('lazy');
                    observer.unobserve(img);
                }
            });
        });
        
        images.forEach(img => {
            imageObserver.observe(img);
        });
    }

    /**
     * Keyboard navigation
     */
    function initKeyboardNavigation() {
        document.addEventListener('keydown', function(e) {
            if (e.key === 'ArrowLeft') {
                navigateImage(-1);
            } else if (e.key === 'ArrowRight') {
                navigateImage(1);
            }
        });
    }

    // Initialize all functionality when DOM is ready
    document.addEventListener('DOMContentLoaded', function() {
        initMagnifier();
        initTouchSwipe();
        initLazyLoading();
        initKeyboardNavigation();
        
        // Make functions globally accessible
        window.changeImage = changeImage;
        window.navigateImage = navigateImage;
        window.openPhotoSwipe = openPhotoSwipe;
    });

    // Reinitialize on window resize
    window.addEventListener('resize', function() {
        initMagnifier();
    });
</script>
@endpush

<script>
// Cart and wishlist functions (non-module)
function increaseQuantity() {
    const input = document.getElementById('quantity');
    if (parseInt(input.value) < parseInt(input.max)) {
        input.value = parseInt(input.value) + 1;
    }
}

function decreaseQuantity() {
    const input = document.getElementById('quantity');
    if (parseInt(input.value) > 1) {
        input.value = parseInt(input.value) - 1;
    }
}

function addToCart(productId) {
    const quantity = document.getElementById('quantity').value;
    fetch('{{ route("cart.add") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({ product_id: productId, quantity: parseInt(quantity) })
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
        if (data.success) {
            location.reload();
        }
    });
}
</script>
@endsection