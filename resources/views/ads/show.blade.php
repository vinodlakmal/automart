@extends('layouts.app')
@section('title', $ad->title . ' — ikman Clone')

@section('content')
<nav class="text-sm text-gray-500 mb-4">
    <a href="{{ route('home') }}" class="hover:underline">මුල් පිටුව</a>
    <span>/</span>
    <a href="{{ route('ads.index', ['category' => $ad->category_id]) }}" class="hover:underline">{{ $ad->category?->name }}</a>
</nav>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <div class="lg:col-span-2 space-y-6">
        {{-- Gallery --}}
        <div class="bg-white rounded-lg shadow overflow-hidden">
            @if($ad->images->isNotEmpty())
                <img id="mainImage" src="{{ $ad->images->first()->url }}" alt="{{ $ad->title }}" class="w-full max-h-[480px] object-contain bg-gray-900">
                @if($ad->images->count() > 1)
                    <div class="flex gap-2 p-3 overflow-x-auto">
                        @foreach($ad->images as $img)
                            <img src="{{ $img->url }}" data-full="{{ $img->url }}"
                                 class="thumb h-16 w-16 object-cover rounded cursor-pointer border-2 {{ $loop->first ? 'border-brand' : 'border-transparent' }}">
                        @endforeach
                    </div>
                @endif
            @else
                <div class="h-80 flex items-center justify-center text-gray-400">No images</div>
            @endif
        </div>

        {{-- Details --}}
        <div class="bg-white rounded-lg shadow p-6">
            <h1 class="text-2xl font-bold">{{ $ad->title }}</h1>
            <p class="text-gray-500 text-sm mt-1">{{ $ad->city?->name }}, {{ $ad->district?->name }} · {{ $ad->created_at->diffForHumans() }} · {{ number_format($ad->views) }} views</p>

            <p class="text-3xl font-bold text-brand mt-4">
                @if(!is_null($ad->price)) Rs. {{ number_format($ad->price, 2) }} @else කතා කර තීරණය කරගත හැක @endif
            </p>
            @if($ad->is_negotiable)<span class="inline-block mt-1 text-sm bg-gray-100 px-2 py-1 rounded">මිල සාකච්ඡා කළ හැක</span>@endif

            @if($ad->condition)
                <p class="mt-3 text-sm">තත්ත්වය: <span class="font-medium">{{ $ad->condition === 'new' ? 'අලුත් (New)' : 'පාවිච්චි කළ (Used)' }}</span></p>
            @endif

            @if($ad->attributes->isNotEmpty())
                <div class="grid grid-cols-2 sm:grid-cols-3 gap-3 mt-4 border-t pt-4">
                    @foreach($ad->attributes as $attr)
                        <div class="text-sm">
                            <span class="text-gray-500">{{ ucfirst(str_replace('_',' ', $attr->attribute_key)) }}:</span>
                            <span class="font-medium">{{ $attr->attribute_value }}</span>
                        </div>
                    @endforeach
                </div>
            @endif

            <div class="mt-6 border-t pt-4">
                <h2 class="font-semibold mb-2">විස්තරය</h2>
                <p class="whitespace-pre-line text-gray-700">{{ $ad->description }}</p>
            </div>

            @can('update', $ad)
                <div class="mt-6 border-t pt-4 flex gap-3">
                    <a href="{{ route('ads.edit', $ad) }}" class="px-4 py-2 rounded border hover:bg-gray-50">සංස්කරණය</a>
                    <form action="{{ route('ads.destroy', $ad) }}" method="POST" onsubmit="return confirm('මෙම දැන්වීම ඉවත් කරන්නද?')">
                        @csrf @method('DELETE')
                        <button class="px-4 py-2 rounded bg-red-600 text-white hover:bg-red-700">ඉවත් කරන්න</button>
                    </form>
                </div>
            @endcan
        </div>
    </div>

    {{-- Seller info --}}
    <aside class="space-y-6">
        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="font-semibold mb-3">විකුණුම්කරු</h2>
            <p class="font-medium">{{ $ad->contact_name ?? $ad->user?->name }}</p>
            <a href="tel:{{ $ad->contact_phone }}" class="mt-3 block text-center bg-brand text-white py-2.5 rounded font-semibold hover:bg-brand-light">
                {{ $ad->contact_phone }}
            </a>
            <p class="text-xs text-gray-400 mt-2 text-center">ikman Clone හරහා දුටුවා යැයි පවසන්න</p>
        </div>
    </aside>
</div>

{{-- Related ads --}}
@if($related->isNotEmpty())
<section class="mt-10">
    <h2 class="text-xl font-bold mb-4">අදාළ දැන්වීම්</h2>
    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-4">
        @foreach($related as $r)
            <a href="{{ route('ads.show', $r) }}" class="bg-white rounded-lg shadow hover:shadow-md overflow-hidden">
                <div class="aspect-square bg-gray-200">
                    @if($r->primaryImage)<img src="{{ $r->primaryImage->url }}" class="w-full h-full object-cover">@endif
                </div>
                <div class="p-2">
                    <p class="text-sm line-clamp-2">{{ $r->title }}</p>
                    <p class="text-brand font-semibold text-sm mt-1">@if(!is_null($r->price)) Rs. {{ number_format($r->price) }} @else - @endif</p>
                </div>
            </a>
        @endforeach
    </div>
</section>
@endif
@endsection

@push('scripts')
<script>
document.querySelectorAll('.thumb').forEach(t => t.addEventListener('click', () => {
    document.getElementById('mainImage').src = t.dataset.full;
    document.querySelectorAll('.thumb').forEach(x => x.classList.replace('border-brand','border-transparent'));
    t.classList.replace('border-transparent','border-brand');
}));
</script>
@endpush
