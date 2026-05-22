@extends('layouts.admin')

@section('title', 'Setări Pagina de Contact')
@section('page_title', 'Setări contact')
@section('breadcrumbs')
    <a href="{{ route('admin.dashboard') }}" class="hover:text-slate-700">Admin</a>
    <span class="mx-1">/</span>
    <span>Setări contact</span>
@endsection

@section('content')
<div class="max-w-4xl">
    @if ($errors->any())
        <div class="mb-4 rounded-lg border border-rose-200 bg-rose-50 px-4 py-3 text-rose-800">
            <ul class="list-disc ml-5">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('admin.settings.contact.update') }}" class="bg-white rounded-xl border border-slate-200 p-6 space-y-4">
        @csrf
        @method('PUT')

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">Telefon</label>
                <input type="text" name="phone" value="{{ old('phone', $settings['phone']) }}" class="w-full px-4 py-2 border border-slate-200 rounded-lg">
            </div>
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">Email</label>
                <input type="email" name="email" value="{{ old('email', $settings['email']) }}" class="w-full px-4 py-2 border border-slate-200 rounded-lg">
            </div>
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">Facebook</label>
                <input type="text" name="facebook" value="{{ old('facebook', $settings['facebook']) }}" class="w-full px-4 py-2 border border-slate-200 rounded-lg">
            </div>
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">Instagram</label>
                <input type="text" name="instagram" value="{{ old('instagram', $settings['instagram']) }}" class="w-full px-4 py-2 border border-slate-200 rounded-lg">
            </div>
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">YouTube</label>
                <input type="text" name="youtube" value="{{ old('youtube', $settings['youtube']) }}" class="w-full px-4 py-2 border border-slate-200 rounded-lg">
            </div>
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">WhatsApp</label>
                <input type="text" name="whatsapp" value="{{ old('whatsapp', $settings['whatsapp']) }}" class="w-full px-4 py-2 border border-slate-200 rounded-lg">
            </div>
        </div>

        <div>
            <label class="block text-sm font-medium text-slate-700 mb-1">Adresă</label>
            <textarea name="address" rows="2" class="w-full px-4 py-2 border border-slate-200 rounded-lg">{{ old('address', $settings['address']) }}</textarea>
        </div>
        <div>
            <label class="block text-sm font-medium text-slate-700 mb-1">Program</label>
            <textarea name="schedule" rows="2" class="w-full px-4 py-2 border border-slate-200 rounded-lg">{{ old('schedule', $settings['schedule']) }}</textarea>
        </div>
        <div>
            <label class="block text-sm font-medium text-slate-700 mb-1">Text contact</label>
            <textarea name="contact_text" rows="4" class="w-full px-4 py-2 border border-slate-200 rounded-lg">{{ old('contact_text', $settings['contact_text']) }}</textarea>
        </div>
        <div>
            <label class="block text-sm font-medium text-slate-700 mb-1">Google Maps embed</label>
            <textarea name="map" rows="4" class="w-full px-4 py-2 border border-slate-200 rounded-lg">{{ old('map', $settings['map']) }}</textarea>
        </div>

        <div class="flex justify-end">
            <button class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">Salvează</button>
        </div>
    </form>
</div>
@endsection
