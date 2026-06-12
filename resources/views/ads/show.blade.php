@extends('layouts.app')
@section('title', $ad->title . ' — ikman Clone')

@section('content')
@php
    $waNumber = $ad->contact_phone ? '94' . ltrim($ad->contact_phone, '0') : null;
    $waText   = urlencode('Hi, I saw your ad "' . $ad->title . '" on ikman Clone.');
    $images   = $ad->images->pluck('url')->values();
@endphp

{{-- Breadcrumb --}}
<nav class="text-sm text-gray-500 mb-4 flex items-center gap-1 flex-wrap">
    <a href="{{ route('home') }}" class="hover:underline">මුල් පිටුව</a>
    <span>/</span>
    @if($ad->category?->parent)
        <a href="{{ route('ads.index', ['category' => $ad->category->parent_id]) }}" class="hover:underline">
            {{ $ad->category->parent->icon }} {{ $ad->category->parent->name }}
        </a>
        <span>/</span>
    @endif
    <a href="{{ route('ads.index', ['category' => $ad->category_id]) }}" class="hover:underline">
        {{ $ad->category?->name }}
    </a>
    <span>/</span>
    <span class="text-gray-400 line-clamp-1">{{ Str::limit($ad->title, 50) }}</span>
</nav>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

    {{-- ── Left column ── --}}
    <div class="lg:col-span-2 space-y-5">

        {{-- Gallery --}}
        <div class="bg-white rounded-lg shadow overflow-hidden">
            @if($images->isNotEmpty())
                <div class="relative bg-gray-900">
                    <img id="mainImage"
                         src="{{ $images->first() }}"
                         alt="{{ $ad->title }}"
                         class="w-full max-h-[480px] object-contain">

                    @if($images->count() > 1)
                        {{-- Prev / Next --}}
                        <button id="prevBtn"
                                class="absolute left-3 top-1/2 -translate-y-1/2 bg-black/50 hover:bg-black/75 text-white rounded-full w-10 h-10 flex items-center justify-center text-xl transition">
                            &#8249;
                        </button>
                        <button id="nextBtn"
                                class="absolute right-3 top-1/2 -translate-y-1/2 bg-black/50 hover:bg-black/75 text-white rounded-full w-10 h-10 flex items-center justify-center text-xl transition">
                            &#8250;
                        </button>
                        {{-- Counter --}}
                        <span id="imgCounter"
                              class="absolute top-3 right-3 bg-black/60 text-white text-xs font-medium px-2 py-1 rounded-full">
                            1 / {{ $images->count() }}
                        </span>
                    @endif
                </div>

                {{-- Thumbnails --}}
                @if($images->count() > 1)
                    <div class="flex gap-2 p-3 overflow-x-auto">
                        @foreach($ad->images as $i => $img)
                            <img src="{{ $img->url }}"
                                 data-index="{{ $i }}"
                                 class="thumb h-16 w-16 object-cover rounded cursor-pointer border-2 flex-shrink-0
                                        {{ $loop->first ? 'border-brand' : 'border-transparent' }}">
                        @endforeach
                    </div>
                @endif

            @else
                {{-- No image placeholder --}}
                <div class="h-72 flex flex-col items-center justify-center text-gray-400 bg-gray-50">
                    <span class="text-7xl">{{ $ad->category?->icon ?? '📦' }}</span>
                    <p class="mt-3 text-sm">No images uploaded</p>
                </div>
            @endif
        </div>

        {{-- Title & core details --}}
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-start justify-between gap-4">
                <h1 class="text-2xl font-bold leading-snug">{{ $ad->title }}</h1>
                @if($ad->is_featured)
                    <span class="flex-shrink-0 bg-yellow-400 text-brand-dark text-xs font-bold px-2 py-1 rounded">TOP AD</span>
                @endif
            </div>

            <p class="text-gray-500 text-sm mt-2">
                📍 {{ $ad->city?->name }}, {{ $ad->district?->name }}
                &nbsp;·&nbsp;
                🕐 {{ $ad->created_at->diffForHumans() }}
                &nbsp;·&nbsp;
                👁 {{ number_format($ad->views) }} views
            </p>

            <div class="mt-4 flex items-end gap-3 flex-wrap">
                <p class="text-3xl font-bold text-brand">
                    @if(! is_null($ad->price))
                        Rs. {{ number_format($ad->price) }}
                    @else
                        <span class="text-xl text-gray-500 font-normal">කතා කර තීරණය කරගත හැක</span>
                    @endif
                </p>
                @if($ad->is_negotiable)
                    <span class="text-sm bg-gray-100 text-gray-600 px-2 py-1 rounded mb-0.5">Negotiable</span>
                @endif
                @if($ad->condition)
                    <span class="text-sm px-2 py-1 rounded mb-0.5
                        {{ $ad->condition === 'new' ? 'bg-green-100 text-green-700' : 'bg-blue-100 text-blue-700' }}">
                        {{ $ad->condition === 'new' ? 'Brand New' : 'Used' }}
                    </span>
                @endif
            </div>

            {{-- Dynamic attributes --}}
            @if($ad->attributes->isNotEmpty())
                <div class="mt-5 pt-4 border-t grid grid-cols-2 sm:grid-cols-3 gap-3">
                    @foreach($ad->attributes as $attr)
                        <div class="text-sm">
                            <span class="text-gray-500 block">{{ ucfirst(str_replace('_', ' ', $attr->attribute_key)) }}</span>
                            <span class="font-medium">{{ $attr->attribute_value }}</span>
                        </div>
                    @endforeach
                </div>
            @endif

            {{-- Description --}}
            <div class="mt-5 pt-4 border-t">
                <h2 class="font-semibold mb-2">විස්තරය (Description)</h2>
                <p class="whitespace-pre-line text-gray-700 leading-relaxed">{{ $ad->description }}</p>
            </div>

            {{-- Owner actions --}}
            @can('update', $ad)
                <div class="mt-5 pt-4 border-t flex gap-3">
                    <a href="{{ route('ads.edit', $ad) }}"
                       class="px-4 py-2 rounded border hover:bg-gray-50 text-sm">
                        ✏️ සංස්කරණය
                    </a>
                    <form action="{{ route('ads.destroy', $ad) }}" method="POST"
                          onsubmit="return confirm('මෙම දැන්වීම ඉවත් කරන්නද?')">
                        @csrf @method('DELETE')
                        <button class="px-4 py-2 rounded bg-red-600 text-white hover:bg-red-700 text-sm">
                            🗑 ඉවත් කරන්න
                        </button>
                    </form>
                </div>
            @endcan
        </div>
    </div>

    {{-- ── Sidebar ── --}}
    <aside class="space-y-4">

        {{-- Seller contact --}}
        <div class="bg-white rounded-lg shadow p-5">
            <h2 class="font-semibold mb-1">විකුණුම්කරු</h2>
            <p class="text-gray-700 font-medium">{{ $ad->contact_name ?? $ad->user?->name }}</p>
            <p class="text-xs text-gray-400 mb-4">Member since {{ $ad->user?->created_at?->format('M Y') }}</p>

            <a href="tel:{{ $ad->contact_phone }}"
               class="flex items-center justify-center gap-2 w-full bg-brand text-white py-2.5 rounded-lg font-semibold hover:bg-brand-light transition">
                📞 {{ $ad->contact_phone }}
            </a>

            @if($waNumber)
                <a href="https://wa.me/{{ $waNumber }}?text={{ $waText }}"
                   target="_blank" rel="noopener"
                   class="flex items-center justify-center gap-2 w-full mt-2 bg-green-500 text-white py-2.5 rounded-lg font-semibold hover:bg-green-600 transition">
                    WhatsApp
                </a>
            @endif

            <p class="text-xs text-gray-400 mt-3 text-center">ikman Clone හරහා දෙස් කළා යැයි කියන්න</p>
        </div>

        {{-- Ad metadata --}}
        <div class="bg-white rounded-lg shadow p-4 text-sm divide-y">
            <div class="flex justify-between py-2">
                <span class="text-gray-500">Ad ID</span>
                <span class="font-mono text-gray-700">#{{ $ad->id }}</span>
            </div>
            <div class="flex justify-between py-2">
                <span class="text-gray-500">Posted</span>
                <span class="text-gray-700">{{ $ad->created_at->format('d M Y') }}</span>
            </div>
            @if($ad->expires_at)
                <div class="flex justify-between py-2">
                    <span class="text-gray-500">Expires</span>
                    <span class="text-gray-700">{{ $ad->expires_at->format('d M Y') }}</span>
                </div>
            @endif
            <div class="flex justify-between py-2">
                <span class="text-gray-500">Category</span>
                <span class="text-gray-700">{{ $ad->category?->name }}</span>
            </div>
        </div>

        {{-- Safety tips --}}
        <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
            <h3 class="font-semibold text-yellow-800 mb-2 text-sm">⚠️ ආරක්ෂිතව ගනුදෙනු කරන්න</h3>
            <ul class="text-xs text-yellow-700 space-y-1.5">
                <li>• Meet the seller in a safe, public place</li>
                <li>• Inspect the item before handing over any money</li>
                <li>• Never pay in advance or transfer money upfront</li>
                <li>• Don't share your banking or financial details</li>
                <li>• Be cautious of deals that seem too good to be true</li>
            </ul>
        </div>

    </aside>
</div>

{{-- Related ads --}}
@if($related->isNotEmpty())
<section class="mt-10">
    <h2 class="text-xl font-bold mb-4">
        අදාළ දැන්වීම්
        <span class="text-base font-normal text-gray-500">(Related ads)</span>
    </h2>
    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-4">
        @foreach($related as $r)
            <a href="{{ route('ads.show', $r) }}"
               class="bg-white rounded-lg shadow hover:shadow-md transition overflow-hidden flex flex-col">
                <div class="aspect-square bg-gray-100 flex items-center justify-center">
                    @if($r->primaryImage)
                        <img src="{{ $r->primaryImage->url }}"
                             alt="{{ $r->title }}"
                             class="w-full h-full object-cover">
                    @else
                        <span class="text-4xl">{{ $r->category?->icon ?? '📦' }}</span>
                    @endif
                </div>
                <div class="p-2 flex-1 flex flex-col">
                    <p class="text-sm line-clamp-2 flex-1">{{ $r->title }}</p>
                    <p class="text-brand font-semibold text-sm mt-1">
                        @if(! is_null($r->price)) Rs. {{ number_format($r->price) }} @else — @endif
                    </p>
                    @if($r->city)
                        <p class="text-xs text-gray-400 mt-0.5">{{ $r->city->name }}</p>
                    @endif
                </div>
            </a>
        @endforeach
    </div>
</section>
@endif

@endsection

@push('scripts')
<script>
(function () {
    const images  = @json($images);
    const mainImg = document.getElementById('mainImage');
    const counter = document.getElementById('imgCounter');
    const thumbs  = document.querySelectorAll('.thumb');
    let current = 0;

    function goTo(i) {
        current = (i + images.length) % images.length;
        mainImg.src = images[current];
        if (counter) counter.textContent = (current + 1) + ' / ' + images.length;
        thumbs.forEach((t, idx) => {
            t.classList.toggle('border-brand', idx === current);
            t.classList.toggle('border-transparent', idx !== current);
        });
    }

    document.getElementById('prevBtn')?.addEventListener('click', () => goTo(current - 1));
    document.getElementById('nextBtn')?.addEventListener('click', () => goTo(current + 1));
    thumbs.forEach((t, idx) => t.addEventListener('click', () => goTo(idx)));

    // Keyboard navigation
    document.addEventListener('keydown', e => {
        if (e.key === 'ArrowLeft')  goTo(current - 1);
        if (e.key === 'ArrowRight') goTo(current + 1);
    });
})();
</script>
@endpush
