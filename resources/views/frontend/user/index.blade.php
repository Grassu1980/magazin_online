@extends('layouts.app')

@section('title', 'Contul Meu - Magazin Online')

@section('content')
<div class="container mx-auto px-4 py-8">
    <h1 class="text-2xl font-bold mb-6">Bine ai venit, {{ Auth::user()->name }}!</h1>
    
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <!-- Orders -->
        <a href="{{ route('account.orders') }}" class="bg-white rounded-lg shadow-md p-6 hover:shadow-lg transition-shadow">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center">
                    <i class="fas fa-box text-blue-600 text-xl"></i>
                </div>
                <div>
                    <p class="text-2xl font-bold">{{ Auth::user()->orders->count() }}</p>
                    <p class="text-gray-600 text-sm">Comenzi</p>
                </div>
            </div>
        </a>
        
        <!-- Wishlist -->
        <a href="{{ route('wishlist.index') }}" class="bg-white rounded-lg shadow-md p-6 hover:shadow-lg transition-shadow">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 bg-red-100 rounded-full flex items-center justify-center">
                    <i class="fas fa-heart text-red-600 text-xl"></i>
                </div>
                <div>
                    <p class="text-2xl font-bold">{{ Auth::user()->wishlists->count() }}</p>
                    <p class="text-gray-600 text-sm">Favorite</p>
                </div>
            </div>
        </a>
        
        <!-- Reviews -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 bg-yellow-100 rounded-full flex items-center justify-center">
                    <i class="fas fa-star text-yellow-600 text-xl"></i>
                </div>
                <div>
                    <p class="text-2xl font-bold">0</p>
                    <p class="text-gray-600 text-sm">Recenzii</p>
                </div>
            </div>
        </div>
        
        <!-- Points -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 bg-green-100 rounded-full flex items-center justify-center">
                    <i class="fas fa-coins text-green-600 text-xl"></i>
                </div>
                <div>
                    <p class="text-2xl font-bold">0</p>
                    <p class="text-gray-600 text-sm">Puncte</p>
                </div>
            </div>
        </div>
    </div>
    
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Quick Actions -->
        <div class="lg:col-span-2">
            <div class="bg-white rounded-lg shadow-md p-6">
                <h2 class="text-lg font-semibold mb-4">Acțiuni Rapide</h2>
                <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
                    <a href="{{ route('account.profile') }}" class="flex flex-col items-center p-4 border border-gray-200 rounded-lg hover:bg-gray-50">
                        <i class="fas fa-user text-2xl text-gray-600 mb-2"></i>
                        <span class="text-sm font-medium">Editează Profil</span>
                    </a>
                    <a href="{{ route('account.orders') }}" class="flex flex-col items-center p-4 border border-gray-200 rounded-lg hover:bg-gray-50">
                        <i class="fas fa-shopping-bag text-2xl text-gray-600 mb-2"></i>
                        <span class="text-sm font-medium">Comenzile Mele</span>
                    </a>
                    <a href="{{ route('wishlist.index') }}" class="flex flex-col items-center p-4 border border-gray-200 rounded-lg hover:bg-gray-50">
                        <i class="fas fa-heart text-2xl text-gray-600 mb-2"></i>
                        <span class="text-sm font-medium">Favorite</span>
                    </a>
                    <a href="#" class="flex flex-col items-center p-4 border border-gray-200 rounded-lg hover:bg-gray-50">
                        <i class="fas fa-map-marker-alt text-2xl text-gray-600 mb-2"></i>
                        <span class="text-sm font-medium">Adrese</span>
                    </a>
                    <a href="#" class="flex flex-col items-center p-4 border border-gray-200 rounded-lg hover:bg-gray-50">
                        <i class="fas fa-credit-card text-2xl text-gray-600 mb-2"></i>
                        <span class="text-sm font-medium">Metode Plată</span>
                    </a>
                    <a href="#" class="flex flex-col items-center p-4 border border-gray-200 rounded-lg hover:bg-gray-50">
                        <i class="fas fa-bell text-2xl text-gray-600 mb-2"></i>
                        <span class="text-sm font-medium">Notificări</span>
                    </a>
                </div>
            </div>
        </div>
        
        <!-- Account Info -->
        <div>
            <div class="bg-white rounded-lg shadow-md p-6">
                <h2 class="text-lg font-semibold mb-4">Informații Cont</h2>
                <div class="space-y-3">
                    <div>
                        <p class="text-sm text-gray-500">Nume</p>
                        <p class="font-medium">{{ Auth::user()->name }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">Email</p>
                        <p class="font-medium">{{ Auth::user()->email }}</p>
                    </div>
                    @if(Auth::user()->phone)
                    <div>
                        <p class="text-sm text-gray-500">Telefon</p>
                        <p class="font-medium">{{ Auth::user()->phone }}</p>
                    </div>
                    @endif
                    @if(Auth::user()->address)
                    <div>
                        <p class="text-sm text-gray-500">Adresă</p>
                        <p class="font-medium">{{ Auth::user()->address }}, {{ Auth::user()->city }}</p>
                    </div>
                    @endif
                    <div>
                        <p class="text-sm text-gray-500">Membru din</p>
                        <p class="font-medium">{{ Auth::user()->created_at->format('d.m.Y') }}</p>
                    </div>
                </div>
                <a href="{{ route('account.profile') }}" class="block text-center text-blue-600 hover:text-blue-700 mt-4 text-sm">
                    Editează informațiile
                </a>
            </div>
        </div>
    </div>
</div>
@endsection