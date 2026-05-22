@extends('layouts.app')

@section('title', 'Plată cu Cardul - MobilPay')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-2xl mx-auto">
        <div class="bg-white rounded-xl shadow-lg p-8">
            <h1 class="text-2xl font-bold text-gray-800 mb-6">Plată cu Cardul</h1>
            
            <div class="mb-6">
                <p class="text-gray-600 mb-4">
                    Vei fi redirecționat către pagina de plată securizată MobilPay pentru a finaliza comanda.
                </p>
                
                <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-6">
                    <h3 class="font-semibold text-blue-800 mb-2">Detalii comandă</h3>
                    <p class="text-sm text-blue-700">
                        <strong>Număr comandă:</strong> {{ $order->order_number }}<br>
                        <strong>Valoare:</strong> {{ number_format($order->total, 2) }} RON
                    </p>
                </div>
            </div>

            <form method="POST" action="{{ route('payment.mobilpay.start') }}">
                @csrf
                
                <input type="hidden" name="order_id" value="{{ $order->id }}">
                <input type="hidden" name="amount" value="{{ $order->total }}">
                <input type="hidden" name="currency" value="RON">
                <input type="hidden" name="first_name" value="{{ $order->customer_name }}">
                <input type="hidden" name="last_name" value="">
                <input type="hidden" name="email" value="{{ $order->customer_email }}">
                <input type="hidden" name="phone" value="{{ $order->customer_phone }}">
                <input type="hidden" name="address" value="{{ $order->shipping_address }}">
                <input type="hidden" name="city" value="{{ $order->shipping_city }}">
                <input type="hidden" name="country" value="RO">
                
                <div class="flex items-center justify-between">
                    <a href="{{ route('cart.index') }}" class="px-6 py-3 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors">
                        Înapoi la coș
                    </a>
                    
                    <button type="submit" class="px-8 py-3 bg-gradient-to-r from-blue-600 to-blue-700 text-white rounded-lg hover:from-blue-700 hover:to-blue-800 transition-all shadow-lg hover:shadow-xl flex items-center">
                        <i class="fas fa-credit-card mr-2"></i>
                        Plătește cu Cardul
                    </button>
                </div>
            </form>

            <div class="mt-6 pt-6 border-t border-gray-200">
                <div class="flex items-center justify-center space-x-4 text-gray-500 text-sm">
                    <i class="fas fa-lock"></i>
                    <span>Plată securizată prin MobilPay</span>
                </div>
                <div class="flex items-center justify-center space-x-2 mt-2">
                    <img src="https://mobilpay.ro/images/visa.png" alt="Visa" class="h-8">
                    <img src="https://mobilpay.ro/images/mastercard.png" alt="Mastercard" class="h-8">
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
