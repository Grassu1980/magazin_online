@extends('layouts.admin')

@section('title', 'Secțiuni Homepage - Admin Panel')
@section('page_title', 'Secțiuni Homepage')
@section('breadcrumbs')
    <a href="{{ route('admin.dashboard') }}" class="hover:text-slate-700">Admin</a>
    <span class="mx-1">/</span>
    <span>Secțiuni Homepage</span>
@endsection

@section('content')
<div class="p-6">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold">Secțiuni Homepage</h1>
        <a href="{{ route('admin.homepage-sections.create') }}" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700">
            + Adaugă Secțiune
        </a>
    </div>

    <!-- Sections List -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div id="sections-container">
            @forelse($sections as $section)
            <div class="section-item p-4 border-b flex items-center gap-4" data-id="{{ $section->id }}">
                <div class="cursor-move text-gray-400">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8h16M4 16h16"></path>
                    </svg>
                </div>
                
                <div class="flex-1">
                    <div class="flex items-center gap-2">
                        <span class="px-2 py-1 text-xs rounded-full bg-blue-100 text-blue-800">
                            {{ $section->type }}
                        </span>
                        <span class="font-medium">{{ $section->title ?? 'Fără titlu' }}</span>
                    </div>
                    <p class="text-sm text-gray-500 mt-1">Ordine: {{ $section->sort_order }}</p>
                </div>
                
                <div class="flex items-center gap-2">
                    @if($section->is_active)
                    <span class="px-2 py-1 text-xs rounded-full bg-green-100 text-green-800">Activ</span>
                    @else
                    <span class="px-2 py-1 text-xs rounded-full bg-red-100 text-red-800">Inactiv</span>
                    @endif
                </div>
                
                <div class="flex gap-2">
                    <a href="{{ route('admin.homepage-sections.edit', $section->id) }}" class="text-blue-600 hover:text-blue-700">Editează</a>
                    <button onclick="toggleStatus({{ $section->id }})" class="text-yellow-600 hover:text-yellow-700">
                        {{ $section->is_active ? 'Dezactivează' : 'Activează' }}
                    </button>
                    <form action="{{ route('admin.homepage-sections.destroy', $section->id) }}" method="POST" onsubmit="return confirm('Sigur dorești să ștergi această secțiune?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="text-red-600 hover:text-red-700">Șterge</button>
                    </form>
                </div>
            </div>
            @empty
            <div class="p-8 text-center text-gray-500">
                Nu există secțiuni configurate pentru homepage.
            </div>
            @endforelse
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const container = document.getElementById('sections-container');
    
    new Sortable(container, {
        animation: 150,
        handle: '.cursor-move',
        onEnd: function(evt) {
            const order = [];
            document.querySelectorAll('.section-item').forEach((item, index) => {
                order.push({
                    id: item.dataset.id,
                    order: index
                });
            });
            
            fetch('{{ route('admin.homepage-sections.updateOrder') }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ order: order })
            });
        }
    });
});

function toggleStatus(id) {
    fetch(`{{ route('admin.homepage-sections.toggleStatus', ':id') }}`.replace(':id', id), {
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
