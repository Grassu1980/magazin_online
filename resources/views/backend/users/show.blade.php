@extends('layouts.admin')

@section('title', $user->name . ' - Admin Panel')

@section('content')
<div class="p-6">
    <div class="mb-6 flex items-center justify-between gap-4">
        <div>
            <a href="{{ route('admin.users.index') }}" class="text-blue-600 hover:text-blue-700">
                <i class="fas fa-arrow-left mr-1"></i>Înapoi la utilizatori
            </a>
            <h1 class="text-2xl font-bold mt-4">{{ $user->name }}</h1>
            <p class="text-gray-500">Detaliile utilizatorului și comenzile sale recente.</p>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('admin.users.edit', $user->id) }}" class="px-4 py-2 bg-yellow-600 text-white rounded-lg hover:bg-yellow-700">Editează</a>
            <form action="{{ route('admin.users.destroy', $user->id) }}" method="POST" class="inline">
                @csrf
                @method('DELETE')
                <button type="submit" onclick="return confirm('Sigur ștergi utilizatorul?')" class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700">Șterge</button>
            </form>
        </div>
    </div>

    <div class="grid lg:grid-cols-2 gap-6 mb-6">
        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-xl font-semibold mb-4">Informații utilizator</h2>
            <div class="space-y-3 text-sm text-gray-700">
                <div><strong>Nume:</strong> {{ $user->name }}</div>
                <div><strong>Email:</strong> {{ $user->email }}</div>
                <div><strong>Rol:</strong> {{ $user->role?->name ?? '-' }}</div>
                <div><strong>Telefon:</strong> {{ $user->phone ?? '-' }}</div>
                <div><strong>Oraș:</strong> {{ $user->city ?? '-' }}</div>
                <div><strong>Adresă:</strong> {{ $user->address ?? '-' }}</div>
                <div><strong>Status:</strong> {{ $user->is_active ? 'Activ' : 'Inactiv' }}</div>
                <div><strong>Creat la:</strong> {{ $user->created_at->format('d.m.Y H:i') }}</div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-xl font-semibold mb-4">Comenzi recente</h2>
            @if($user->orders->isEmpty())
                <p class="text-gray-500">Acest utilizator nu are comenzi.</p>
            @else
                <ul class="space-y-2 text-sm text-gray-700">
                    @foreach($user->orders->take(5) as $order)
                        <li class="border border-gray-200 rounded-lg p-3">
                            <div class="flex items-center justify-between">
                                <span>#{{ $order->order_number }}</span>
                                <span class="text-xs {{ $order->status === 'pending' ? 'text-yellow-600' : ($order->status === 'completed' ? 'text-green-600' : 'text-gray-600') }}">{{ ucfirst($order->status) }}</span>
                            </div>
                            <div class="text-gray-500">Total: {{ number_format($order->total, 2, ',', '.') }} lei</div>
                            <div class="text-gray-500">{{ $order->created_at->format('d.m.Y') }}</div>
                        </li>
                    @endforeach
                </ul>
            @endif
        </div>
    </div>

    @if($user->is_company)
    <div class="bg-white rounded-lg shadow p-6 mb-6">
        <h2 class="text-xl font-semibold mb-4">Date Firmă</h2>
        <div class="grid md:grid-cols-2 gap-6 text-sm text-gray-700">
            <div class="space-y-3">
                <div><strong>Denumire Firmă:</strong> {{ $user->company_name ?? '-' }}</div>
                <div><strong>CUI:</strong> {{ $user->company_cui ?? '-' }}</div>
                <div><strong>Nr. Reg. Comerț:</strong> {{ $user->company_reg ?? '-' }}</div>
            </div>
            <div class="space-y-3">
                <div><strong>Adresă Sediu:</strong> {{ $user->company_address ?? '-' }}</div>
                <div><strong>IBAN:</strong> {{ $user->company_iban ?? '-' }}</div>
            </div>
        </div>
    </div>
    @endif
</div>
@endsection
