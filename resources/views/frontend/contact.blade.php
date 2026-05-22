@extends('layouts.frontend')

@section('title', 'Contact')

@section('content')
<div class="container mx-auto py-10">

    <h1 class="text-3xl font-bold mb-6">Contact</h1>

    <p class="mb-4">{{ setting('contact_text') }}</p>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">

        <div class="space-y-4">
            <p><strong>Telefon:</strong> {{ setting('phone') }}</p>
            <p><strong>Email:</strong> {{ setting('email') }}</p>
            <p><strong>Adresă:</strong> {{ setting('address') }}</p>
            <p><strong>Program:</strong> {{ setting('schedule') }}</p>

            <div class="flex gap-4 mt-4">
                <a href="{{ setting('facebook') }}" target="_blank">Facebook</a>
                <a href="{{ setting('instagram') }}" target="_blank">Instagram</a>
                <a href="{{ setting('youtube') }}" target="_blank">YouTube</a>
                <a href="https://wa.me/{{ setting('whatsapp') }}" target="_blank">WhatsApp</a>
            </div>
        </div>

        <div>
            {!! setting('map') !!}
        </div>

    </div>

</div>
@endsection
