@extends('layouts.admin')

@section('title', 'Bannere - Admin Panel')
@section('page_title', 'Bannere')
@section('breadcrumbs')
    <a href="{{ route('admin.dashboard') }}" class="hover:text-slate-700">Admin</a>
    <span class="mx-1">/</span>
    <span>Bannere</span>
@endsection

@section('content')
<div class="p-6">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold">Bannere</h1>
        <a href="{{ route('admin.banners.create') }}" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700">
            + Adaugă Banner
        </a>
    </div>

    <!-- Filters -->
    <div class="bg-white rounded-lg shadow p-4 mb-6">
        <form method="GET" action="{{ route('admin.banners.index') }}" class="flex gap-4">
            <select name="position" class="px-4 py-2 border border-gray-300 rounded-lg">
                <option value="">Toate pozițiile</option>
                <option value="slider" {{ request('position') == 'slider' ? 'selected' : '' }}>Slider</option>
                <option value="top" {{ request('position') == 'top' ? 'selected' : '' }}>Top</option>
                <option value="middle" {{ request('position') == 'middle' ? 'selected' : '' }}>Middle</option>
                <option value="bottom" {{ request('position') == 'bottom' ? 'selected' : '' }}>Bottom</option>
                <option value="footer" {{ request('position') == 'footer' ? 'selected' : '' }}>Footer</option>
            </select>
            
            <select name="status" class="px-4 py-2 border border-gray-300 rounded-lg">
                <option value="">Toate statusurile</option>
                <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
            </select>
            
            <button type="submit" class="bg-gray-600 text-white px-4 py-2 rounded-lg hover:bg-gray-700">
                Filtrează
            </button>
            
            @if(request()->hasAny(['position', 'status']))
            <a href="{{ route('admin.banners.index') }}" class="text-red-600 hover:text-red-700">
                Resetează
            </a>
            @endif
        </form>
    </div>

    <!-- Banners Table -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <table class="w-full">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Imagine</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Titlu</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Poziție</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Ordine</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Perioada</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Acțiuni</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($banners as $banner)
                <tr class="hover:bg-gray-50">
                    <td class="px-6 py-4">
                        @if($banner->image_path)
                        <img src="{{ asset($banner->image_path) }}" alt="{{ $banner->title }}" class="w-16 h-16 object-cover rounded">
                        @else
                        <span class="text-gray-400">Niciuna</span>
                        @endif
                    </td>
                    <td class="px-6 py-4 font-medium text-gray-900">{{ $banner->title }}</td>
                    <td class="px-6 py-4">
                        <span class="px-2 py-1 text-xs rounded-full bg-blue-100 text-blue-800">
                            {{ $banner->position }}
                        </span>
                    </td>
                    <td class="px-6 py-4">{{ $banner->sort_order }}</td>
                    <td class="px-6 py-4">
                        @if($banner->is_active)
                        <span class="px-2 py-1 text-xs rounded-full bg-green-100 text-green-800">Activ</span>
                        @else
                        <span class="px-2 py-1 text-xs rounded-full bg-red-100 text-red-800">Inactiv</span>
                        @endif
                    </td>
                    <td class="px-6 py-4 text-sm text-gray-500">
                        @if($banner->start_date || $banner->end_date)
                        {{ $banner->start_date?->format('d.m.Y') }} - {{ $banner->end_date?->format('d.m.Y') }}
                        @else
                        Permanent
                        @endif
                    </td>
                    <td class="px-6 py-4">
                        <div class="flex gap-2">
                            <a href="{{ route('admin.banners.edit', $banner->id) }}" class="text-blue-600 hover:text-blue-700">Editează</a>
                            <button onclick="toggleStatus({{ $banner->id }})" class="text-yellow-600 hover:text-yellow-700">
                                {{ $banner->is_active ? 'Dezactivează' : 'Activează' }}
                            </button>
                            <form action="{{ route('admin.banners.destroy', $banner->id) }}" method="POST" onsubmit="return confirm('Sigur dorești să ștergi acest banner?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-600 hover:text-red-700">Șterge</button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="px-6 py-4 text-center text-gray-500">Nu există bannere</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{ $banners->links() }}
</div>

<script>
function toggleStatus(id) {
    fetch(`{{ route('admin.banners.toggleStatus', ':id') }}`.replace(':id', id), {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        }
    });
}
</script>
@endsection
