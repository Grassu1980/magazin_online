@extends('layouts.app')

@section('title', 'Checkout - Magazin Online')

@section('content')
<div class="container mx-auto px-4 py-8">
    <h1 class="text-2xl font-bold mb-6">Finalizează Comanda</h1>
    
    <form action="{{ route('checkout.store') }}" method="POST">
        @csrf
        
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Checkout Form -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Customer Info -->
                <div class="bg-white rounded-lg shadow-md p-6">
                    <h3 class="text-lg font-semibold mb-4">Informații Client</h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Nume Complet *</label>
                            <input type="text" name="customer_name" value="{{ old('customer_name', Auth::user()->name ?? '') }}" required
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                            @error('customer_name')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Email *</label>
                            <input type="email" name="customer_email" value="{{ old('customer_email', Auth::user()->email ?? '') }}" required
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                            @error('customer_email')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Telefon *</label>
                            <input type="text" name="customer_phone" value="{{ old('customer_phone', Auth::user()->phone ?? '') }}" required
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                            @error('customer_phone')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>
                
                <!-- Shipping Address -->
                <div class="bg-white rounded-lg shadow-md p-6">
                    <h3 class="text-lg font-semibold mb-4">Adresă Livrare</h3>
                    
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Adresă *</label>
                            <input type="text" name="shipping_address" value="{{ old('shipping_address', Auth::user()->address ?? '') }}" required
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                            @error('shipping_address')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Oraș *</label>
                            <input type="text" name="shipping_city" value="{{ old('shipping_city', Auth::user()->city ?? '') }}" required
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                            @error('shipping_city')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Observații</label>
                            <textarea name="notes" rows="3" 
                                      class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                                      placeholder="Observații despre comandă...">{{ old('notes') }}</textarea>
                        </div>
                    </div>
                </div>
                
                <!-- Payment Method -->
                <div class="bg-white rounded-lg shadow-md p-6">
                    <h3 class="text-lg font-semibold mb-4">Metodă de Plată</h3>
                    
                    <div class="space-y-3">
                        <label class="flex items-center p-4 border border-gray-300 rounded-lg cursor-pointer hover:bg-gray-50">
                            <input type="radio" name="payment_method" value="cash" {{ old('payment_method') == 'cash' ? 'checked' : '' }} required
                                   class="mr-3">
                            <i class="fas fa-money-bill-wave text-2xl text-green-600 mr-3"></i>
                            <div>
                                <span class="font-semibold">Plata la livrare</span>
                                <p class="text-sm text-gray-500">Plătești când primești comanda</p>
                            </div>
                        </label>
                        
                        <label class="flex items-center p-4 border border-gray-300 rounded-lg cursor-pointer hover:bg-gray-50">
                            <input type="radio" name="payment_method" value="card" {{ old('payment_method') == 'card' ? 'checked' : '' }} required
                                   class="mr-3">
                            <i class="fas fa-credit-card text-2xl text-blue-600 mr-3"></i>
                            <div>
                                <span class="font-semibold">Plată cu cardul</span>
                                <p class="text-sm text-gray-500">Plătești online securizat</p>
                            </div>
                        </label>
                    </div>
                    @error('payment_method')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>
            
            <!-- Order Summary -->
            <div>
                <div class="bg-white rounded-lg shadow-md p-6 sticky top-24">
                    <h3 class="text-lg font-semibold mb-4">Sumar Comandă</h3>
                    
                    <div class="space-y-3 mb-6 max-h-64 overflow-y-auto">
                        @foreach($cartItems as $item)
                        <div class="flex justify-between items-center">
                            <div class="flex items-center gap-2">
                                <span class="text-gray-600">{{ $item['quantity'] }}x</span>
                                <span class="text-sm truncate max-w-[150px]">{{ $item['product']->name }}</span>
                            </div>
                            <span class="font-semibold">{{ number_format($item['subtotal'], 2) }} lei</span>
                        </div>
                        @endforeach
                    </div>
                    
                    <hr class="my-4">
                    
                    <div class="space-y-2">
                        <div class="flex justify-between">
                            <span class="text-gray-600">Subtotal</span>
                            <span>{{ number_format($subtotal, 2) }} lei</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Transport</span>
                            <span>{{ $shippingCost == 0 ? 'Gratuit' : number_format($shippingCost, 2) . ' lei' }}</span>
                        </div>
                        <hr>
                        <div class="flex justify-between text-lg">
                            <span class="font-semibold">Total</span>
                            <span class="font-bold text-blue-600">{{ number_format($total, 2) }} lei</span>
                        </div>
                    </div>
                    
                    @if($subtotal < 200)
                    <div class="bg-blue-50 text-blue-700 p-3 rounded-lg text-sm mt-4">
                        <i class="fas fa-truck mr-2"></i>Transport gratuit pentru comenzi peste 200 lei
                    </div>
                    @endif
                    
                    <button type="submit" class="w-full bg-blue-600 text-white py-3 rounded-lg font-semibold hover:bg-blue-700 mt-6">
                        Plasează Comanda
                    </button>
                    
                    <p class="text-xs text-gray-500 text-center mt-4">
                        Prin plasarea comenzii accepti termenii și condițiile
                    </p>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection