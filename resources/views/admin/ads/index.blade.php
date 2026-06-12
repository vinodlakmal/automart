@extends('admin.layouts.app')
@section('title', 'Ads')
@section('heading', 'Manage Ads')

@section('content')

{{-- Filters --}}
<form method="GET" action="{{ route('admin.ads.index') }}"
      class="bg-white rounded-xl shadow p-4 mb-5 flex flex-wrap gap-3 items-end">

    <div class="flex-1 min-w-[180px]">
        <label class="block text-xs font-medium text-gray-500 mb-1">Search</label>
        <input type="text" name="q" value="{{ request('q') }}" placeholder="Title…"
               class="w-full border rounded px-3 py-2 text-sm focus:outline-none focus:ring-1 focus:ring-brand">
    </div>

    <div>
        <label class="block text-xs font-medium text-gray-500 mb-1">Status</label>
        <select name="status" class="border rounded px-3 py-2 text-sm bg-white">
            <option value="">All</option>
            @foreach(['active','pending','sold','expired','rejected'] as $s)
                <option value="{{ $s }}" @selected(request('status') === $s)>{{ ucfirst($s) }}</option>
            @endforeach
        </select>
    </div>

    <div>
        <label class="block text-xs font-medium text-gray-500 mb-1">Category</label>
        <select name="category" class="border rounded px-3 py-2 text-sm bg-white">
            <option value="">All</option>
            @foreach($categories as $cat)
                <option value="{{ $cat->id }}" @selected(request('category') == $cat->id)>{{ $cat->name }}</option>
            @endforeach
        </select>
    </div>

    <button class="bg-brand text-white px-4 py-2 rounded text-sm hover:bg-brand-light transition">Filter</button>
    <a href="{{ route('admin.ads.index') }}" class="text-sm text-gray-400 hover:underline py-2">Clear</a>
</form>

{{-- Table --}}
<div class="bg-white rounded-xl shadow overflow-hidden">
    <div class="px-5 py-3 border-b flex items-center justify-between">
        <p class="text-sm text-gray-500">{{ number_format($ads->total()) }} ads</p>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 text-xs text-gray-500 uppercase tracking-wide">
                <tr>
                    <th class="text-left px-4 py-3 w-8">#</th>
                    <th class="text-left px-4 py-3">Title</th>
                    <th class="text-left px-4 py-3">User</th>
                    <th class="text-left px-4 py-3">Category</th>
                    <th class="text-left px-4 py-3">Price</th>
                    <th class="text-left px-4 py-3">Status</th>
                    <th class="text-left px-4 py-3">Views</th>
                    <th class="text-left px-4 py-3">Posted</th>
                    <th class="text-left px-4 py-3">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y">
                @forelse($ads as $ad)
                    @php
                        $statusClass = match($ad->status) {
                            'active'   => 'bg-green-100 text-green-700',
                            'pending'  => 'bg-yellow-100 text-yellow-700',
                            'sold'     => 'bg-blue-100 text-blue-700',
                            'expired'  => 'bg-orange-100 text-orange-700',
                            'rejected' => 'bg-red-100 text-red-700',
                            default    => 'bg-gray-100 text-gray-600',
                        };
                    @endphp
                    <tr class="hover:bg-gray-50 {{ $ad->trashed() ? 'opacity-50' : '' }}">
                        <td class="px-4 py-3 text-gray-400 font-mono text-xs">{{ $ad->id }}</td>
                        <td class="px-4 py-3 max-w-xs">
                            <a href="{{ route('ads.show', $ad) }}" target="_blank"
                               class="font-medium text-brand hover:underline line-clamp-1">{{ $ad->title }}</a>
                            @if($ad->is_featured)
                                <span class="text-xs bg-yellow-100 text-yellow-700 px-1.5 py-0.5 rounded ml-1">⭐ TOP</span>
                            @endif
                            @if($ad->trashed())
                                <span class="text-xs bg-gray-200 text-gray-500 px-1.5 py-0.5 rounded ml-1">Deleted</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-gray-600 whitespace-nowrap">{{ $ad->user?->name }}</td>
                        <td class="px-4 py-3 text-gray-500">{{ $ad->category?->name }}</td>
                        <td class="px-4 py-3 text-gray-700 whitespace-nowrap">
                            @if(! is_null($ad->price)) Rs. {{ number_format($ad->price) }} @else — @endif
                        </td>
                        <td class="px-4 py-3">
                            <span class="text-xs font-semibold px-2 py-0.5 rounded {{ $statusClass }}">
                                {{ ucfirst($ad->status) }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-gray-500">{{ number_format($ad->views) }}</td>
                        <td class="px-4 py-3 text-gray-400 whitespace-nowrap">{{ $ad->created_at->format('d M Y') }}</td>
                        <td class="px-4 py-3">
                            <div class="flex items-center gap-1 flex-nowrap">
                                @if($ad->status === 'pending')
                                    <form action="{{ route('admin.ads.approve', $ad) }}" method="POST">
                                        @csrf
                                        <button class="text-xs bg-green-100 text-green-700 hover:bg-green-200 px-2 py-1 rounded transition">✓ Approve</button>
                                    </form>
                                    <form action="{{ route('admin.ads.reject', $ad) }}" method="POST">
                                        @csrf
                                        <button class="text-xs bg-red-100 text-red-700 hover:bg-red-200 px-2 py-1 rounded transition">✗ Reject</button>
                                    </form>
                                @endif
                                @if(! $ad->trashed())
                                    <form action="{{ route('admin.ads.toggleFeatured', $ad) }}" method="POST">
                                        @csrf
                                        <button class="text-xs {{ $ad->is_featured ? 'bg-yellow-100 text-yellow-700' : 'bg-gray-100 text-gray-600' }} hover:bg-yellow-200 px-2 py-1 rounded transition">
                                            ⭐
                                        </button>
                                    </form>
                                    <form action="{{ route('admin.ads.destroy', $ad) }}" method="POST"
                                          onsubmit="return confirm('Permanently delete this ad?')">
                                        @csrf @method('DELETE')
                                        <button class="text-xs bg-red-50 text-red-500 hover:bg-red-100 px-2 py-1 rounded transition">🗑</button>
                                    </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="9" class="px-4 py-10 text-center text-gray-400">No ads found.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="px-5 py-4 border-t">{{ $ads->links() }}</div>
</div>
@endsection
