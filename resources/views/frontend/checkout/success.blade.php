@extends('layouts.app')

@section('title', 'Comandă Finalizată - Magazin Online')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-lg mx-auto text-center">
        <div class="bg-white rounded-lg shadow-md p-8">
            <div class="w-20 h-20 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-6">
                <i class="fas fa-check text-4xl text-green-600"></i>
            </div>
            
            <h1 class="text-2xl font-bold text-gray-800 mb-2">Comandă Finalizată!</h1>
            <p class="text-gray-600 mb-6">Mulțumim pentru comanda ta. Vei primi un email de confirmare.</p>
            
            <div class="bg-gray-50 rounded-lg p-4 mb-6">
                <p class="text-sm text-gray-600">Număr Comandă</p>
                <p class="text-xl font-bold text-blue-600">{{ $order->order_number }}</p>
            </div>
            
            <div class="space-y-2 text-sm text-gray-600 mb-6">
                <p><i class="fas fa-envelope mr-2"></i>Un email de confirmare a fost trimis la {{ $order->customer_email }}</p>
                <p><i class="fas fa-clock mr-2"></i>Vei primi actualizări despre statusul comenzii</p>
            </div>
            
            <div class="flex flex-col gap-3">
                <a href="{{ route('account.orders') }}" class="block bg-blue-600 text-white py-3 rounded-lg font-semibold hover:bg-blue-700">
                    Vezi Comenzile Mele
                </a>
                <a href="{{ route('home') }}" class="block bg-gray-200 text-gray-700 py-3 rounded-lg font-semibold hover:bg-gray-300">
                    Înapoi la Acasă
                </a>
            </div>
        </div>
    </div>
</div>
@endsection