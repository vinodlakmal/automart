@extends('layouts.app')
@section('title', __('ui.favorites.title') . ' — Merkei Mart')

@section('content')

<div class="flex items-center justify-between mb-6">
    <div>
        <h1 class="text-2xl font-bold">{{ __('ui.favorites.title') }}</h1>
        <p class="text-sm text-gray-500 mt-0.5">
            {{ trans_choice('ui.favorites.count', $ads->total(), ['count' => number_format($ads->total())]) }}
        </p>
    </div>
    <a href="{{ route('ads.index') }}"
       class="text-sm text-brand hover:underline">{{ __('ui.favorites.browse_more') }}</a>
</div>

@if($ads->isEmpty())
    <div class="bg-white rounded-xl shadow p-16 text-center">
        <p class="text-6xl mb-4">🤍</p>
        <h2 class="text-xl font-semibold text-gray-700">{{ __('ui.favorites.empty_title') }}</h2>
        <p class="text-gray-400 text-sm mt-2 mb-8">{{ __('ui.favorites.empty_sub') }}</p>
        <a href="{{ route('ads.index') }}"
           class="inline-block bg-brand text-white px-6 py-2.5 rounded-lg hover:bg-brand-light transition font-semibold">
            {{ __('ui.favorites.browse') }}
        </a>
    </div>
@else
    <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-4" id="saved-grid">
        @foreach($ads as $ad)
            <div class="bg-white rounded-lg shadow hover:shadow-md transition overflow-hidden flex flex-col" id="saved-card-{{ $ad->id }}">

                {{-- Image + remove button --}}
                <div class="aspect-video bg-gray-100 relative">
                    <a href="{{ route('ads.show', $ad) }}" class="block w-full h-full">
                        @if($ad->primaryImage)
                            <img src="{{ $ad->primaryImage->url }}"
                                 alt="{{ $ad->title }}"
                                 class="w-full h-full object-cover">
                        @else
                            <div class="w-full h-full flex items-center justify-center text-gray-400 text-4xl">
                                {{ $ad->category?->icon ?? '📦' }}
                            </div>
                        @endif
                    </a>

                    {{-- Status badge --}}
                    @if($ad->status !== 'active')
                        @php
                            $statusClass = match($ad->status) {
                                'sold'     => 'bg-gray-600 text-white',
                                'expired'  => 'bg-orange-500 text-white',
                                'pending'  => 'bg-yellow-400 text-yellow-900',
                                'rejected' => 'bg-red-600 text-white',
                                default    => 'bg-gray-500 text-white',
                            };
                        @endphp
                        <span class="absolute top-2 left-2 text-xs font-bold px-2 py-0.5 rounded {{ $statusClass }}">
                            {{ ucfirst($ad->status) }}
                        </span>
                    @elseif($ad->is_featured)
                        <span class="absolute top-2 left-2 bg-yellow-400 text-brand-dark text-xs font-bold px-2 py-1 rounded">
                            TOP AD
                        </span>
                    @endif

                    {{-- Heart / remove button --}}
                    <button type="button"
                            class="fav-card-btn absolute top-2 right-2 w-8 h-8 rounded-full bg-white/80 shadow flex items-center justify-center text-base hover:bg-white transition z-10"
                            data-url="{{ route('ads.favorite', $ad) }}"
                            data-card="saved-card-{{ $ad->id }}"
                            title="Remove from saved">
                        ❤️
                    </button>
                </div>

                {{-- Details --}}
                <a href="{{ route('ads.show', $ad) }}" class="p-3 flex-1 flex flex-col">
                    <h3 class="font-medium line-clamp-2 text-sm">{{ $ad->title }}</h3>

                    <p class="text-brand font-bold mt-1 text-sm">
                        @if(! is_null($ad->price))
                            Rs. {{ number_format($ad->price) }}
                            @if($ad->is_negotiable)
                                <span class="text-xs font-normal text-gray-400">(neg.)</span>
                            @endif
                        @else
                            <span class="text-gray-500 font-normal">Price on request</span>
                        @endif
                    </p>

                    <p class="text-xs text-gray-400 mt-auto pt-2">
                        📍 {{ $ad->city?->name }}@if($ad->city && $ad->district), @endif{{ $ad->district?->name }}
                        &middot; {{ __('ui.favorites.saved_ago', ['time' => $ad->pivot->created_at?->diffForHumans()]) }}
                    </p>
                </a>
            </div>
        @endforeach
    </div>

    <div class="mt-6">{{ $ads->links() }}</div>
@endif

@endsection

@push('scripts')
<script>
document.querySelectorAll('.fav-card-btn').forEach(btn => {
    btn.addEventListener('click', async function (e) {
        e.preventDefault();
        e.stopPropagation();
        try {
            const res = await fetch(this.dataset.url, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content,
                    'Accept': 'application/json',
                },
            });
            if (!res.ok) return;
            const data = await res.json();
            if (!data.favorited) {
                // Fade and remove card
                const card = document.getElementById(this.dataset.card);
                if (card) {
                    card.style.transition = 'opacity 0.3s, transform 0.3s';
                    card.style.opacity    = '0';
                    card.style.transform  = 'scale(0.95)';
                    setTimeout(() => card.remove(), 320);
                }
            }
        } catch (err) { /* silent */ }
    });
});
</script>
@endpush
