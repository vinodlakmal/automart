@extends('layouts.app')
@section('title', 'මගේ දැන්වීම් — Merkei Mart')

@section('content')
@php
    $statusConfig = [
        'all'      => ['label' => 'සියල්ල',        'en' => 'All',     'color' => 'gray'],
        'active'   => ['label' => 'ක්‍රියාකාරී',   'en' => 'Active',  'color' => 'green'],
        'pending'  => ['label' => 'බලාපොරොත්තු',   'en' => 'Pending', 'color' => 'yellow'],
        'sold'     => ['label' => 'විකිණී ගිය',    'en' => 'Sold',    'color' => 'blue'],
        'expired'  => ['label' => 'කල් ඉකුත්',     'en' => 'Expired', 'color' => 'red'],
        'rejected' => ['label' => 'ප්‍රතික්ෂේප',  'en' => 'Rejected','color' => 'rose'],
    ];
    $colorMap = [
        'gray'   => ['stat' => 'bg-gray-100 text-gray-700',   'badge' => 'bg-gray-100 text-gray-600',   'ring' => 'ring-gray-400'],
        'green'  => ['stat' => 'bg-green-100 text-green-700', 'badge' => 'bg-green-100 text-green-700', 'ring' => 'ring-green-500'],
        'yellow' => ['stat' => 'bg-yellow-100 text-yellow-700','badge'=> 'bg-yellow-100 text-yellow-700','ring' => 'ring-yellow-400'],
        'blue'   => ['stat' => 'bg-blue-100 text-blue-700',   'badge' => 'bg-blue-100 text-blue-700',   'ring' => 'ring-blue-400'],
        'red'    => ['stat' => 'bg-red-100 text-red-700',     'badge' => 'bg-red-100 text-red-700',     'ring' => 'ring-red-400'],
        'rose'   => ['stat' => 'bg-rose-100 text-rose-700',   'badge' => 'bg-rose-100 text-rose-700',   'ring' => 'ring-rose-400'],
    ];
    $currentStatus = request('status');
    $totalViews = $ads->getCollection()->sum('views');
@endphp

{{-- Header --}}
<div class="flex items-center justify-between mb-6">
    <div>
        <h1 class="text-2xl font-bold">මගේ දැන්වීම්</h1>
        <p class="text-sm text-gray-500 mt-0.5">{{ auth()->user()->name }}</p>
    </div>
    <a href="{{ route('ads.create') }}"
       class="bg-brand text-white px-4 py-2 rounded-lg font-medium hover:bg-brand-light transition flex items-center gap-2">
        + නව දැන්වීම
    </a>
</div>

@if(session('status'))
    <div class="bg-green-50 border border-green-200 text-green-800 rounded-lg px-4 py-3 mb-5 text-sm">
        {{ session('status') }}
    </div>
@endif

{{-- Stats strip --}}
<div class="grid grid-cols-3 sm:grid-cols-6 gap-3 mb-6">
    @foreach($statusConfig as $key => $cfg)
        @php
            $count   = $key === 'all' ? $stats->sum() : ($stats[$key] ?? 0);
            $active  = ($key === 'all' && ! $currentStatus) || $currentStatus === $key;
            $colors  = $colorMap[$cfg['color']];
            $href    = $key === 'all' ? route('ads.myAds') : route('ads.myAds', ['status' => $key]);
        @endphp
        <a href="{{ $href }}"
           class="rounded-xl p-3 text-center transition
                  {{ $active
                      ? $colors['stat'] . ' ring-2 ' . $colors['ring']
                      : 'bg-white shadow hover:shadow-md ' . $colors['stat'] }}">
            <div class="text-2xl font-bold">{{ $count }}</div>
            <div class="text-xs mt-0.5 font-medium">{{ $cfg['en'] }}</div>
        </a>
    @endforeach
</div>

{{-- Ad list --}}
@if($ads->isEmpty())
    <div class="bg-white rounded-xl shadow p-12 text-center">
        <div class="text-5xl mb-3">📋</div>
        @if($currentStatus)
            <p class="text-gray-500">{{ $statusConfig[$currentStatus]['label'] }} දැන්වීම් නොමැත.</p>
            <a href="{{ route('ads.myAds') }}" class="mt-3 inline-block text-brand text-sm hover:underline">සියලු දැන්වීම් බලන්න</a>
        @else
            <p class="text-gray-500 mb-4">ඔබ තවම දැන්වීම් පළ කර නැත.</p>
            <a href="{{ route('ads.create') }}"
               class="inline-block bg-brand text-white px-6 py-2.5 rounded-lg font-semibold hover:bg-brand-light">
                පළමු දැන්වීම පළ කරන්න
            </a>
        @endif
    </div>
@else
    <div class="bg-white rounded-xl shadow divide-y">
        @foreach($ads as $ad)
        @php
            $cfg    = $statusConfig[$ad->status] ?? $statusConfig['active'];
            $colors = $colorMap[$cfg['color']];
            $expiringSoon = $ad->status === 'active'
                && $ad->expires_at
                && $ad->expires_at->diffInDays(now(), false) < 0
                && $ad->expires_at->diffInDays(now(), false) > -7;
        @endphp
        <div class="flex items-start gap-4 p-4 hover:bg-gray-50 transition">

            {{-- Thumbnail --}}
            <a href="{{ route('ads.show', $ad) }}"
               class="w-20 h-20 flex-shrink-0 rounded-lg overflow-hidden bg-gray-100 flex items-center justify-center">
                @if($ad->primaryImage)
                    <img src="{{ $ad->primaryImage->url }}" class="w-full h-full object-cover">
                @else
                    <span class="text-3xl">{{ $ad->category?->icon ?? '📦' }}</span>
                @endif
            </a>

            {{-- Info --}}
            <div class="flex-1 min-w-0">
                <div class="flex items-start gap-2 flex-wrap">
                    <a href="{{ route('ads.show', $ad) }}"
                       class="font-semibold hover:underline line-clamp-1 text-gray-900">
                        {{ $ad->title }}
                    </a>
                    <span class="inline-flex items-center text-xs px-2 py-0.5 rounded-full font-medium flex-shrink-0 {{ $colors['badge'] }}">
                        {{ $cfg['en'] }}
                    </span>
                    @if($ad->is_featured)
                        <span class="inline-flex items-center text-xs px-2 py-0.5 rounded-full bg-yellow-100 text-yellow-700 font-medium flex-shrink-0">
                            ⭐ TOP AD
                        </span>
                    @endif
                </div>

                <div class="flex flex-wrap gap-x-4 gap-y-0.5 mt-1 text-sm text-gray-500">
                    @if(! is_null($ad->price))
                        <span class="text-brand font-semibold text-sm">Rs. {{ number_format($ad->price) }}</span>
                    @endif
                    @if($ad->category)
                        <span>{{ $ad->category->icon }} {{ $ad->category->name }}</span>
                    @endif
                    @if($ad->city)
                        <span>📍 {{ $ad->city->name }}</span>
                    @endif
                </div>

                <div class="flex flex-wrap gap-x-4 gap-y-0.5 mt-1 text-xs text-gray-400">
                    <span>👁 {{ number_format($ad->views) }} views</span>
                    <span>📅 {{ $ad->created_at->format('d M Y') }}</span>
                    @if($ad->expires_at)
                        @if($expiringSoon)
                            <span class="text-orange-500 font-medium">⚠ Expires {{ $ad->expires_at->diffForHumans() }}</span>
                        @elseif($ad->status === 'active')
                            <span>Expires {{ $ad->expires_at->format('d M Y') }}</span>
                        @endif
                    @endif
                </div>
            </div>

            {{-- Actions --}}
            <div class="flex flex-col sm:flex-row gap-2 flex-shrink-0">
                <a href="{{ route('ads.edit', $ad) }}"
                   class="px-3 py-1.5 text-xs rounded-lg border hover:bg-gray-100 text-center">
                    සංස්කරණය
                </a>

                @if($ad->status === 'active')
                    <form action="{{ route('ads.markSold', $ad) }}" method="POST">
                        @csrf @method('PATCH')
                        <button class="w-full px-3 py-1.5 text-xs rounded-lg bg-blue-50 text-blue-700 border border-blue-200 hover:bg-blue-100">
                            විකිණී ගිය
                        </button>
                    </form>
                @endif

                <form action="{{ route('ads.destroy', $ad) }}" method="POST"
                      onsubmit="return confirm('{{ addslashes($ad->title) }} ඉවත් කරන්නද?')">
                    @csrf @method('DELETE')
                    <button class="w-full px-3 py-1.5 text-xs rounded-lg bg-red-50 text-red-700 border border-red-200 hover:bg-red-100">
                        ඉවත් කරන්න
                    </button>
                </form>
            </div>
        </div>
        @endforeach
    </div>

    <div class="mt-5">{{ $ads->links() }}</div>
@endif
@endsection
