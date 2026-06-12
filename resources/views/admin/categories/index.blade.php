@extends('admin.layouts.app')
@section('title', 'Categories')
@section('heading', 'Manage Categories')

@section('content')

<div class="bg-white rounded-xl shadow overflow-hidden">
    <div class="px-5 py-3 border-b flex items-center justify-between">
        <p class="text-sm text-gray-500">{{ $categories->count() }} root categories</p>
    </div>

    <div class="divide-y">
        @forelse($categories as $cat)
            {{-- Parent category row --}}
            <div class="px-5 py-3 flex items-center gap-4 {{ ! $cat->is_active ? 'opacity-50' : '' }}">
                <span class="text-2xl w-8 text-center flex-shrink-0">{{ $cat->icon ?? '📦' }}</span>
                <div class="flex-1 min-w-0">
                    <p class="font-semibold">{{ $cat->name }}</p>
                    <p class="text-xs text-gray-400">
                        {{ $cat->ads_count }} ads
                        @if($cat->children->isNotEmpty())
                            · {{ $cat->children->count() }} subcategories
                        @endif
                        @if($cat->type) · {{ $cat->type }} @endif
                    </p>
                </div>
                <form action="{{ route('admin.categories.toggle', $cat) }}" method="POST">
                    @csrf
                    <button class="text-xs px-3 py-1.5 rounded transition font-semibold
                        {{ $cat->is_active ? 'bg-green-100 text-green-700 hover:bg-red-100 hover:text-red-700' : 'bg-red-100 text-red-600 hover:bg-green-100 hover:text-green-700' }}">
                        {{ $cat->is_active ? 'Enabled' : 'Disabled' }}
                    </button>
                </form>
            </div>

            {{-- Subcategory rows --}}
            @foreach($cat->children as $sub)
                <div class="pl-16 pr-5 py-2.5 flex items-center gap-3 bg-gray-50/60 {{ ! $sub->is_active ? 'opacity-50' : '' }}">
                    <div class="flex-1 min-w-0">
                        <p class="text-sm text-gray-700">{{ $sub->name }}</p>
                        <p class="text-xs text-gray-400">{{ $sub->ads_count ?? 0 }} ads @if($sub->type) · {{ $sub->type }} @endif</p>
                    </div>
                    <form action="{{ route('admin.categories.toggle', $sub) }}" method="POST">
                        @csrf
                        <button class="text-xs px-2.5 py-1 rounded transition
                            {{ $sub->is_active ? 'bg-green-100 text-green-700 hover:bg-red-100 hover:text-red-700' : 'bg-red-100 text-red-600 hover:bg-green-100 hover:text-green-700' }}">
                            {{ $sub->is_active ? 'On' : 'Off' }}
                        </button>
                    </form>
                </div>
            @endforeach
        @empty
            <p class="px-5 py-10 text-center text-gray-400">No categories found.</p>
        @endforelse
    </div>
</div>
@endsection
