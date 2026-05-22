@extends('layouts.app')

@section('title', 'Comenzile Mele - Magazin Online')

@section('content')
<div class="container mx-auto px-4 py-8">
    <h1 class="text-2xl font-bold mb-6">Comenzile Mele</h1>
    
    @if($orders->count() > 0)
    <div class="space-y-4">
        @foreach($orders as $order)
        <div class="bg-white rounded-lg shadow-md p-6">
            <div class="flex flex-wrap justify-between items-start gap-4 mb-4">
                <div>
                    <p class="text-sm text-gray-500">Număr Comandă</p>
                    <p class="text-lg font-bold">{{ $order->order_number }}</p>
                </div>
                <div class="text-right">
                    <p class="text-sm text-gray-500">Data</p>
                    <p class="font-medium">{{ $order->created_at->format('d.m.Y') }}</p>
                </div>
                <div>
                    <span class="px-3 py-1 rounded-full text-sm font-medium
                        @if($order->status == 'pending') bg-yellow-100 text-yellow-700
                        @elseif($order->status == 'processing') bg-blue-100 text-blue-700
                        @elseif($order->status == 'shipped') bg-purple-100 text-purple-700
                        @elseif($order->status == 'delivered') bg-green-100 text-green-700
                        @else bg-red-100 text-red-700 @endif">
                        @if($order->status == 'pending') În așteptare
                        @elseif($order->status == 'processing') Se procesează
                        @elseif($order->status == 'shipped') Expediat
                        @elseif($order->status == 'delivered') Livrat
                        @elseif($order->status == 'cancelled') Anulat
                        @endif
                    </span>
                </div>
            </div>
            
            <div class="border-t pt-4">
                <div class="flex flex-wrap gap-4 text-sm">
                    <div>
                        <p class="text-gray-500">Produse</p>
                        <p class="font-medium">{{ $order->items->count() }} produse</p>
                    </div>
                    <div>
                        <p class="text-gray-500">Total</p>
                        <p class="font-bold text-blue-600">{{ number_format($order->total, 2) }} lei</p>
                    </div>
                    <div>
                        <p class="text-gray-500">Plată</p>
                        <p class="font-medium">{{ $order->payment_method == 'cash' ? 'La livrare' : 'Card' }}</p>
                    </div>
                </div>
            </div>
            
            <div class="border-t mt-4 pt-4 flex justify-between items-center">
                <a href="{{ route('account.orders.show', $order->id) }}" class="text-blue-600 hover:text-blue-700 font-medium">
                    Vezi Detalii
                </a>
                @if($order->status == 'delivered')
                <button class="text-gray-600 hover:text-blue-600 text-sm">
                    <i class="fas fa-star mr-1"></i>Lasă o recenzie
                </button>
                @endif
            </div>
        </div>
        @endforeach
    </div>
    
    <div class="mt-6">
        {{ $orders->links() }}
    </div>
    @else
    <div class="text-center py-12">
        <i class="fas fa-box text-6xl text-gray-300 mb-4"></i>
        <h3 class="text-xl font-semibold text-gray-600 mb-2">Nu ai comenzi</h3>
        <p class="text-gray-500 mb-6">Comenzile tale vor apărea aici</p>
        <a href="{{ route('products.index') }}" class="inline-block bg-blue-600 text-white px-8 py-3 rounded-lg font-semibold hover:bg-blue-700">
            Vezi Produse
        </a>
    </div>
    @endif
</div>
@endsection