<!DOCTYPE html>
<html lang="ro">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - @yield('title', 'Dashboard')</title>

    <style>
        body {
            margin: 0;
            font-family: Arial, sans-serif;
            background: #f3f4f6;
        }

        .sidebar {
            width: 240px;
            background: #1f2937;
            color: white;
            height: 100vh;
            position: fixed;
            left: 0;
            top: 0;
            padding: 20px;
        }

        .sidebar h2 {
            margin-top: 0;
            font-size: 22px;
            margin-bottom: 20px;
        }

        .sidebar a {
            display: block;
            color: #d1d5db;
            padding: 10px 0;
            text-decoration: none;
            font-size: 16px;
        }

        .sidebar a:hover {
            color: white;
        }

        .content {
            margin-left: 260px;
            padding: 30px;
        }

        .card {
            background: white;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 25px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }

        .btn {
            padding: 8px 14px;
            border-radius: 6px;
            border: none;
            cursor: pointer;
            color: white;
            font-size: 14px;
        }

        .btn-blue { background: #2563eb; }
        .btn-blue:hover { background: #1e40af; }

        .btn-purple { background: #7c3aed; }
        .btn-purple:hover { background: #5b21b6; }

        .btn-gray { background: #4b5563; }
        .btn-gray:hover { background: #374151; }

        .badge {
            padding: 6px 10px;
            border-radius: 6px;
            color: white;
            font-size: 14px;
        }

        .badge-yellow { background: #d97706; }
        .badge-blue { background: #2563eb; }
        .badge-indigo { background: #4f46e5; }
        .badge-green { background: #059669; }
        .badge-red { background: #dc2626; }
        .badge-gray { background: #6b7280; }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }

        th, td {
            padding: 12px;
            border: 1px solid #e5e7eb;
            text-align: left;
        }

        th {
            background: #f3f4f6;
        }
    </style>
</head>

<body>

    {{-- Sidebar --}}
    <div class="sidebar">
        <h2>Admin Panel</h2>

        <a href="{{ route('admin.orders.index') }}">Comenzi</a>
        <a href="#">Produse</a>
        <a href="#">Utilizatori</a>
        <a href="#">Setări</a>

        <br><br>

        <a href="{{ route('logout') }}"
           onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
            Logout
        </a>

        <form id="logout-form" action="{{ route('logout') }}" method="POST" class="hidden">
            @csrf
        </form>
    </div>

    {{-- Main Content --}}
    <div class="content">
        @yield('content')
    </div>

</body>
</html>
