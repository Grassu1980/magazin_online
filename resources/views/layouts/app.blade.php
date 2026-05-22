<!DOCTYPE html>
<html lang="ro">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="@yield('meta_description', 'Magazin Online - Cumpără produse de calitate la cele mai bune prețuri')">
    <title>@yield('title', 'Magazin Online')</title>

    @if(setting('favicon'))
    <link rel="icon" type="image/x-icon" href="{{ Storage::url(setting('favicon')) }}">
    @endif

    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">

    <!-- Dynamic Colors -->
    <style>
        :root {
            --primary-color: {{ setting('primary_color', '#2563eb') }};
            --secondary-color: {{ setting('secondary_color', '#1e40af') }};
        }
        .bg-primary { background-color: var(--primary-color) !important; }
        .bg-primary-hover:hover { background-color: var(--secondary-color) !important; }
        .text-primary { color: var(--primary-color) !important; }
        .border-primary { border-color: var(--primary-color) !important; }
        .from-primary { background-color: var(--primary-color) !important; }
        .to-secondary { background-color: var(--secondary-color) !important; }
    </style>

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    @stack('styles')
</head>
<body class="bg-gray-50 min-h-screen flex flex-col">
    <!-- Header -->
    @include('components.header')
    
    <!-- Main Content -->
    <main class="flex-grow">
        @yield('content')
    </main>
    
    <!-- Footer -->
    @include('components.footer')
    
    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="{{ asset('js/app.js') }}"></script>
    
    @stack('scripts')
    
    <!-- Flash Messages -->
    @if(session('success'))
    <script>
        Swal.fire({
            icon: 'success',
            title: 'Succes!',
            text: '{{ session('success') }}',
            timer: 3000,
            showConfirmButton: false
        });
    </script>
    @endif
    
    @if(session('error'))
    <script>
        Swal.fire({
            icon: 'error',
            title: 'Eroare!',
            text: '{{ session('error') }}',
            timer: 3000,
            showConfirmButton: false
        });
    </script>
    @endif
</body>
</html>