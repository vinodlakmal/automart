@extends('layouts.app')
@section('title', config('app.name', 'Marketplace') . ' — Sri Lanka\'s Free Marketplace')

@section('content')

{{-- Hero --}}
<section class="bg-brand text-white rounded-2xl px-6 py-12 text-center mb-8">
    <h1 class="text-3xl sm:text-4xl font-bold">ඕනෑම දෙයක් මිලදී ගන්න, විකුණන්න, කුලියට දෙන්න</h1>
    <p class="mt-3 text-white/80">Real Estate · Vehicles · Education · Shopping & more — post your ad free.</p>

    <form action="{{ route('search') }}" method="GET" class="mt-6 max-w-2xl mx-auto flex">
        <input type="text" name="q" placeholder="මොනවද හොයන්නේ? (What are you looking for?)"
               class="w-full rounded-l-lg px-4 py-3 text-gray-800 focus:outline-none">
        <button class="bg-yellow-400 text-brand-dark font-semibold px-6 rounded-r-lg hover:bg-yellow-300">සොයන්න</button>
    </form>

    <a href="{{ route('ads.create') }}" class="inline-block mt-5 bg-white text-brand font-semibold px-6 py-2.5 rounded-lg hover:bg-gray-100">
        + නොමිලේ දැන්වීමක් පළ කරන්න
    </a>
</section>

{{-- Category grid --}}
<section class="mb-10">
    <h2 class="text-xl font-bold mb-4">ප්‍රවර්ග (Browse categories)</h2>
    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-7 gap-3">
        @foreach($categories as $cat)
            <a href="{{ route('ads.index', ['category' => $cat->id]) }}"
               class="bg-white rounded-xl shadow hover:shadow-md transition p-4 text-center">
                <div class="text-3xl">{{ $cat->icon ?? '📦' }}</div>
                <div class="mt-2 font-medium text-sm">{{ $cat->name }}</div>
                <div class="text-xs text-gray-400">{{ $cat->ads_count }} දැන්වීම්</div>
            </a>
        @endforeach
    </div>
</section>

{{-- Featured --}}
@if($featured->isNotEmpty())
<section class="mb-10">
    <h2 class="text-xl font-bold mb-4">⭐ Featured ads</h2>
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        @foreach($featured as $ad)
            <a href="{{ route('ads.show', $ad) }}" class="bg-white rounded-lg shadow hover:shadow-md overflow-hidden">
                <div class="aspect-video bg-gray-200">
                    @if($ad->primaryImage)<img src="{{ $ad->primaryImage->url }}" class="w-full h-full object-cover">@endif
                </div>
                <div class="p-3">
                    <p class="text-sm font-medium line-clamp-2">{{ $ad->title }}</p>
                    <p class="text-brand font-bold text-sm mt-1">@if(!is_null($ad->price)) Rs. {{ number_format($ad->price) }} @else - @endif</p>
                    <p class="text-xs text-gray-400 mt-1">{{ $ad->city?->name }}</p>
                </div>
            </a>
        @endforeach
    </div>
</section>
@endif

{{-- Per-category recent sections --}}
@foreach($sections as $section)
    <section class="mb-10">
        <div class="flex items-center justify-between mb-4">
            <h2 class="text-xl font-bold">{{ $section['category']->icon }} {{ $section['category']->name }}</h2>
            <a href="{{ route('ads.index', ['category' => $section['category']->id]) }}" class="text-brand text-sm hover:underline">සියල්ල බලන්න →</a>
        </div>
        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-4">
            @foreach($section['ads'] as $ad)
                <a href="{{ route('ads.show', $ad) }}" class="bg-white rounded-lg shadow hover:shadow-md overflow-hidden">
                    <div class="aspect-square bg-gray-200">
                        @if($ad->primaryImage)<img src="{{ $ad->primaryImage->url }}" class="w-full h-full object-cover">@endif
                    </div>
                    <div class="p-2">
                        <p class="text-sm line-clamp-2">{{ $ad->title }}</p>
                        <p class="text-brand font-semibold text-sm mt-1">@if(!is_null($ad->price)) Rs. {{ number_format($ad->price) }} @else - @endif</p>
                    </div>
                </a>
            @endforeach
        </div>
    </section>
@endforeach

@if($sections->isEmpty() && $featured->isEmpty())
    <div class="bg-white rounded-lg shadow p-10 text-center text-gray-500">
        තවම දැන්වීම් නැත. <a href="{{ route('ads.create') }}" class="text-brand hover:underline">පළමු දැන්වීම පළ කරන්න!</a>
    </div>
@endif

@endsection
