@extends('layouts.admin')

@section('title', 'Editează Furnizor - Admin Panel')

@section('content')
<div class="p-6">
    <div class="mb-6">
        <a href="{{ route('admin.suppliers.index') }}" class="text-blue-600 hover:text-blue-700">
            <i class="fas fa-arrow-left mr-1"></i>Înapoi la furnizori
        </a>
    </div>

    <div class="bg-white rounded-lg shadow p-6">
        <h1 class="text-2xl font-bold mb-6">Editează Furnizor</h1>

        @if ($errors->any())
            <div class="mb-6 p-4 bg-red-100 border border-red-400 text-red-700 rounded">
                <p class="font-bold">Erori:</p>
                <ul class="list-disc ml-5">
                    @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('admin.suppliers.update', $supplier) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Nume Furnizor *</label>
                    <input type="text" name="name" value="{{ $supplier->name }}" required
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                           placeholder="Numele furnizorului">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">CUI</label>
                    <input type="text" name="cui" value="{{ $supplier->cui }}"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                           placeholder="Codul de identificare fiscală">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Reg. Com.</label>
                    <input type="text" name="reg_com" value="{{ $supplier->reg_com }}"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                           placeholder="Numărul de înregistrare comerț">
                </div>

                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Adresă</label>
                    <input type="text" name="address" value="{{ $supplier->address }}"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                           placeholder="Adresa completă">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Oraș</label>
                    <input type="text" name="city" value="{{ $supplier->city }}"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                           placeholder="Orașul">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Telefon</label>
                    <input type="text" name="phone" value="{{ $supplier->phone }}"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                           placeholder="Numărul de telefon">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Email</label>
                    <input type="email" name="email" value="{{ $supplier->email }}"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                           placeholder="Adresa de email">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Persoană de Contact</label>
                    <input type="text" name="contact_person" value="{{ $supplier->contact_person }}"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                           placeholder="Numele persoanei de contact">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                    <select name="is_active" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="1" {{ $supplier->is_active ? 'selected' : '' }}>Activ</option>
                        <option value="0" {{ !$supplier->is_active ? 'selected' : '' }}>Inactiv</option>
                    </select>
                </div>
            </div>

            <div class="flex justify-end gap-4">
                <a href="{{ route('admin.suppliers.index') }}" class="px-6 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700">
                    Anulează
                </a>
                <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                    <i class="fas fa-save mr-2"></i>Actualizează Furnizorul
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
