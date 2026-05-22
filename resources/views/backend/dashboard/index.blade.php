@extends('layouts.admin')

@section('title', 'Dashboard - Admin Panel')
@section('page_title', 'Dashboard')
@section('breadcrumbs')
    <a href="{{ route('admin.dashboard') }}" class="hover:text-slate-700">Admin</a>
    <span class="mx-1">/</span>
    <span>Dashboard</span>
@endsection

@section('content')
<div class="space-y-6">
    <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4">
        <div class="bg-white rounded-xl border border-slate-200 p-5">
            <div class="text-sm text-slate-500">Total produse</div>
            <div class="mt-2 text-2xl font-semibold text-slate-900">{{ $stats['total_products'] }}</div>
            <div class="mt-3">
                <a href="{{ route('admin.products.index') }}" class="text-sm text-blue-600 hover:text-blue-700">Administrare produse</a>
            </div>
        </div>
        <div class="bg-white rounded-xl border border-slate-200 p-5">
            <div class="text-sm text-slate-500">Total comenzi</div>
            <div class="mt-2 text-2xl font-semibold text-slate-900">{{ $stats['total_orders'] }}</div>
            <div class="mt-3">
                <a href="{{ route('admin.orders.index') }}" class="text-sm text-blue-600 hover:text-blue-700">Administrare comenzi</a>
            </div>
        </div>
        <div class="bg-white rounded-xl border border-slate-200 p-5">
            <div class="text-sm text-slate-500">Total utilizatori</div>
            <div class="mt-2 text-2xl font-semibold text-slate-900">{{ $stats['total_users'] }}</div>
            <div class="mt-3">
                <a href="{{ route('admin.users.index') }}" class="text-sm text-blue-600 hover:text-blue-700">Administrare utilizatori</a>
            </div>
        </div>
        <div class="bg-white rounded-xl border border-slate-200 p-5">
            <div class="text-sm text-slate-500">Total categorii</div>
            <div class="mt-2 text-2xl font-semibold text-slate-900">{{ $stats['total_categories'] }}</div>
            <div class="mt-3">
                <a href="{{ route('admin.categories.index') }}" class="text-sm text-blue-600 hover:text-blue-700">Administrare categorii</a>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 xl:grid-cols-3 gap-6">
        <div class="xl:col-span-2 bg-white rounded-xl border border-slate-200 p-5">
            <div class="flex items-center justify-between">
                <div class="font-semibold text-slate-900">Comenzi (ultimele 7 zile)</div>
            </div>
            <div class="mt-4">
                <canvas id="ordersChart" height="110"></canvas>
            </div>
        </div>

        <div class="bg-white rounded-xl border border-slate-200 overflow-hidden">
            <div class="px-5 py-4 border-b border-slate-200 flex items-center justify-between">
                <div class="font-semibold text-slate-900">Ultimele 5 comenzi</div>
                <a href="{{ route('admin.orders.index') }}" class="text-sm text-blue-600 hover:text-blue-700">Vezi toate</a>
            </div>
            <div class="divide-y divide-slate-200">
                @forelse($recentOrders as $order)
                    <a href="{{ route('admin.orders.show', $order->id) }}" class="block px-5 py-4 hover:bg-slate-50">
                        <div class="flex items-center justify-between gap-3">
                            <div class="min-w-0">
                                <div class="font-medium text-slate-900 truncate">{{ $order->order_number }}</div>
                                <div class="text-sm text-slate-500 truncate">{{ $order->customer_name }}</div>
                            </div>
                            <div class="text-sm font-semibold text-slate-900 whitespace-nowrap">{{ number_format($order->total, 2) }} lei</div>
                        </div>
                    </a>
                @empty
                    <div class="px-5 py-6 text-sm text-slate-500">Nu există comenzi.</div>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
<script>
    const ctx = document.getElementById('ordersChart');
    const labels = @json($chartLabels);
    const data = @json($chartData);

    new Chart(ctx, {
        type: 'line',
        data: {
            labels,
            datasets: [{
                label: 'Comenzi',
                data,
                borderColor: '#2563eb',
                backgroundColor: 'rgba(37, 99, 235, 0.15)',
                tension: 0.35,
                fill: true
            }]
        },
        options: {
            plugins: { legend: { display: false } },
            scales: {
                y: { beginAtZero: true, ticks: { precision: 0 } }
            }
        }
    });
</script>
@endpush
