@extends('layouts.app')

@section('title', 'Editează Profil - Magazin Online')

@section('content')
<div class="container mx-auto px-4 py-8">
    <h1 class="text-2xl font-bold mb-6">Editează Profil</h1>
    
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Profile Form -->
        <div class="lg:col-span-2">
            <div class="bg-white rounded-lg shadow-md p-6">
                <form method="POST" action="{{ route('account.profile.update') }}">
                    @csrf
                    @method('PUT')
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Nume Complet *</label>
                            <input type="text" name="name" value="{{ old('name', Auth::user()->name) }}" required
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                            @error('name')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Email *</label>
                            <input type="email" name="email" value="{{ old('email', Auth::user()->email) }}" required
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                            @error('email')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Telefon</label>
                            <input type="text" name="phone" value="{{ old('phone', Auth::user()->phone) }}"
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Oraș</label>
                            <input type="text" name="city" value="{{ old('city', Auth::user()->city) }}"
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                        </div>
                    </div>
                    
                    <div class="mb-6">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Adresă</label>
                        <input type="text" name="address" value="{{ old('address', Auth::user()->address) }}"
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                    </div>
                    
                    <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded-lg font-semibold hover:bg-blue-700">
                        Salvează Modificările
                    </button>
                </form>
            </div>
            
            <!-- Change Password -->
            <div class="bg-white rounded-lg shadow-md p-6 mt-6">
                <h2 class="text-lg font-semibold mb-4">Schimbă Parola</h2>
                
                <form method="POST" action="{{ route('account.password.update') }}">
                    @csrf
                    @method('PUT')
                    
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Parola Actuală</label>
                        <input type="password" name="current_password" required
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                        @error('current_password')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Parola Nouă</label>
                        <input type="password" name="password" required
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                        @error('password')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Confirmare Parolă</label>
                        <input type="password" name="password_confirmation" required
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                    </div>
                    
                    <button type="submit" class="bg-gray-800 text-white px-6 py-2 rounded-lg font-semibold hover:bg-gray-900">
                        Schimbă Parola
                    </button>
                </form>
            </div>
        </div>
        
        <!-- Sidebar -->
        <div>
            <div class="bg-white rounded-lg shadow-md p-6">
                <div class="text-center mb-4">
                    <div class="w-24 h-24 bg-gray-200 rounded-full flex items-center justify-center mx-auto mb-3">
                        <i class="fas fa-user text-4xl text-gray-500"></i>
                    </div>
                    <h3 class="font-semibold">{{ Auth::user()->name }}</h3>
                    <p class="text-gray-500 text-sm">{{ Auth::user()->email }}</p>
                </div>
                
                <div class="border-t pt-4">
                    <a href="{{ route('account.index') }}" class="flex items-center text-gray-600 hover:text-blue-600 py-2">
                        <i class="fas fa-home w-6"></i> Dashboard
                    </a>
                    <a href="{{ route('account.profile') }}" class="flex items-center text-blue-600 py-2">
                        <i class="fas fa-user w-6"></i> Profil
                    </a>
                    <a href="{{ route('account.orders') }}" class="flex items-center text-gray-600 hover:text-blue-600 py-2">
                        <i class="fas fa-box w-6"></i> Comenzi
                    </a>
                    <a href="{{ route('wishlist.index') }}" class="flex items-center text-gray-600 hover:text-blue-600 py-2">
                        <i class="fas fa-heart w-6"></i> Favorite
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection