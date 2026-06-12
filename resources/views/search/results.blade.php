@extends('layouts.app')
@section('title', ($q ? '"' . $q . '" — ' : 'Search — ') . 'Merkei Mart')

@section('content')
@php
    $knownParams  = ['q', 'category', 'district', 'min_price', 'max_price', 'condition', 'sort'];
    $currentParams = collect($knownParams)->mapWithKeys(fn ($k) => [$k => request($k)])->filter()->all();

    $activeFilters = [];
    if (request('category') && ($cat = $categories->firstWhere('id', request('category'))))
        $activeFilters['category'] = $cat->name;
    if (request('district') && ($dist = $districts->firstWhere('id', request('district'))))
        $activeFilters['district'] = $dist->name;
    if (request('min_price'))
        $activeFilters['min_price'] = 'Min Rs. ' . number_format(request('min_price'));
    if (request('max_price'))
        $activeFilters['max_price'] = 'Max Rs. ' . number_format(request('max_price'));
    if (request('condition'))
        $activeFilters['condition'] = request('condition') === 'new' ? 'New' : 'Used';
@endphp

{{-- Search heading --}}
<div class="mb-6">
    @if($q)
        <h1 class="text-2xl font-bold">
            <span class="text-gray-400 font-normal text-lg">Results for</span>
            "{{ $q }}"
        </h1>
        <p class="text-sm text-gray-500 mt-1">{{ number_format($results->total()) }} {{ Str::plural('listing', $results->total()) }} found</p>
    @else
        <h1 class="text-2xl font-bold">All Listings</h1>
        <p class="text-sm text-gray-500 mt-1">{{ number_format($results->total()) }} active {{ Str::plural('listing', $results->total()) }}</p>
    @endif
</div>

<div class="grid grid-cols-1 lg:grid-cols-4 gap-6">

    {{-- ── Sidebar ── --}}
    <aside class="lg:col-span-1 space-y-4">

        {{-- Search box --}}
        <form action="{{ route('search') }}" method="GET"
              id="search-form"
              class="bg-white rounded-lg shadow p-4 space-y-4 sticky top-4">

            @if(request('sort'))
                <input type="hidden" name="sort" value="{{ request('sort') }}">
            @endif

            <div>
                <label class="block text-sm font-medium mb-1">සෙවුම (Search)</label>
                <div class="flex">
                    <input type="text" name="q" value="{{ $q }}"
                           placeholder="Keywords…"
                           class="w-full border rounded-l px-3 py-2 text-sm focus:outline-none focus:ring-1 focus:ring-brand">
                    <button class="bg-brand text-white px-3 rounded-r hover:bg-brand-light text-sm">Go</button>
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium mb-1">ප්‍රවර්ගය</label>
                <select name="category" class="w-full border rounded px-3 py-2 text-sm">
                    <option value="">All categories</option>
                    @foreach($categories as $cat)
                        @php $cnt = $hitCounts->get($cat->id, 0); @endphp
                        <option value="{{ $cat->id }}" @selected(request('category') == $cat->id)>
                            {{ $cat->icon }} {{ $cat->name }}
                            @if($cnt) ({{ $cnt }}) @endif
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-sm font-medium mb-1">දිස්ත්‍රික්කය</label>
                <select name="district" class="w-full border rounded px-3 py-2 text-sm">
                    <option value="">All districts</option>
                    @foreach($districts as $d)
                        <option value="{{ $d->id }}" @selected(request('district') == $d->id)>{{ $d->name }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-sm font-medium mb-1">මිල (Rs.)</label>
                <div class="flex gap-2">
                    <input type="number" name="min_price" value="{{ request('min_price') }}"
                           placeholder="Min" class="w-1/2 border rounded px-2 py-2 text-sm">
                    <input type="number" name="max_price" value="{{ request('max_price') }}"
                           placeholder="Max" class="w-1/2 border rounded px-2 py-2 text-sm">
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium mb-1">තත්ත්වය</label>
                <select name="condition" class="w-full border rounded px-3 py-2 text-sm">
                    <option value="">Any</option>
                    <option value="new"  @selected(request('condition') === 'new')>New</option>
                    <option value="used" @selected(request('condition') === 'used')>Used</option>
                </select>
            </div>

            <button class="w-full bg-brand text-white py-2 rounded text-sm hover:bg-brand-light">
                Apply Filters
            </button>
            <a href="{{ route('search', $q ? ['q' => $q] : []) }}"
               class="block text-center text-xs text-gray-400 hover:underline">Clear filters</a>
        </form>
    </aside>

    {{-- ── Results ── --}}
    <section class="lg:col-span-3 space-y-4">

        {{-- Active filter chips --}}
        @if(count($activeFilters))
            <div class="flex flex-wrap items-center gap-2">
                @foreach($activeFilters as $key => $label)
                    @php $params = Arr::except($currentParams, $key); @endphp
                    <a href="{{ route('search', $params) }}"
                       class="inline-flex items-center gap-1 bg-brand/10 text-brand text-sm px-3 py-1 rounded-full hover:bg-brand/20 transition">
                        {{ $label }} <span class="text-base leading-none">&times;</span>
                    </a>
                @endforeach
                <a href="{{ route('search', $q ? ['q' => $q] : []) }}"
                   class="text-xs text-gray-400 hover:underline ml-1">Clear all</a>
            </div>
        @endif

        {{-- Sort + count bar --}}
        <div class="flex items-center justify-between">
            <span class="text-sm text-gray-500">
                @if($results->total())
                    Showing {{ $results->firstItem() }}–{{ $results->lastItem() }}
                    of {{ number_format($results->total()) }}
                @endif
            </span>
            <select id="sort-select" class="border rounded px-3 py-1.5 text-sm bg-white focus:outline-none focus:ring-1 focus:ring-brand">
                <option value=""           @selected(! request('sort'))>Newest first</option>
                <option value="price_asc"  @selected(request('sort') === 'price_asc')>Price: Low to High</option>
                <option value="price_desc" @selected(request('sort') === 'price_desc')>Price: High to Low</option>
                <option value="views"      @selected(request('sort') === 'views')>Most Viewed</option>
            </select>
        </div>

        {{-- No results --}}
        @if($results->isEmpty())
            <div class="bg-white rounded-xl shadow p-10 text-center">
                <p class="text-4xl mb-3">🔍</p>
                @if($q)
                    <p class="text-lg font-semibold text-gray-700">No results for "{{ $q }}"</p>
                    <p class="text-gray-400 text-sm mt-2 mb-6">Try different keywords, or browse a category below.</p>
                @else
                    <p class="text-gray-500">No listings match your filters.</p>
                @endif

                <div class="grid grid-cols-3 sm:grid-cols-4 gap-3 mt-4 max-w-sm mx-auto">
                    @foreach($categories as $cat)
                        <a href="{{ route('search', ['category' => $cat->id]) }}"
                           class="flex flex-col items-center p-3 rounded-lg hover:bg-gray-50 border text-center transition">
                            <span class="text-2xl">{{ $cat->icon }}</span>
                            <span class="text-xs mt-1 text-gray-600">{{ $cat->name }}</span>
                        </a>
                    @endforeach
                </div>
            </div>

        {{-- Result list --}}
        @else
            @foreach($results as $ad)
                <a href="{{ route('ads.show', $ad) }}"
                   class="flex gap-4 bg-white rounded-xl shadow hover:shadow-md transition p-4 group relative">

                    {{-- Thumbnail --}}
                    <div class="w-28 h-24 flex-shrink-0 rounded-lg overflow-hidden bg-gray-100 flex items-center justify-center relative">
                        @if($ad->primaryImage)
                            <img src="{{ $ad->primaryImage->url }}"
                                 alt="{{ $ad->title }}"
                                 class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-200">
                        @else
                            <span class="text-4xl">{{ $ad->category?->icon ?? '📦' }}</span>
                        @endif
                        @auth
                            <button type="button"
                                    class="fav-card-btn absolute bottom-1 right-1 w-6 h-6 rounded-full bg-white/90 shadow flex items-center justify-center text-xs hover:bg-white transition z-10"
                                    data-url="{{ route('ads.favorite', $ad) }}"
                                    title="{{ in_array($ad->id, $favoriteIds) ? 'Remove from saved' : 'Save ad' }}">
                                {{ in_array($ad->id, $favoriteIds) ? '❤️' : '🤍' }}
                            </button>
                        @endauth
                    </div>

                    {{-- Details --}}
                    <div class="flex-1 min-w-0">
                        <div class="flex items-start justify-between gap-2 flex-wrap">
                            <h2 class="font-semibold text-gray-900 line-clamp-1 group-hover:text-brand transition-colors">
                                {{ $ad->title }}
                            </h2>
                            @if($ad->is_featured)
                                <span class="flex-shrink-0 text-xs bg-yellow-100 text-yellow-700 font-bold px-2 py-0.5 rounded">TOP AD</span>
                            @endif
                        </div>

                        <p class="text-brand font-bold mt-0.5">
                            @if(! is_null($ad->price))
                                Rs. {{ number_format($ad->price) }}
                                @if($ad->is_negotiable)
                                    <span class="text-xs font-normal text-gray-400">(negotiable)</span>
                                @endif
                            @else
                                <span class="text-gray-400 font-normal text-sm">Price on request</span>
                            @endif
                        </p>

                        <p class="text-sm text-gray-500 mt-1 line-clamp-2 leading-relaxed">
                            {{ Str::limit(strip_tags($ad->description), 140) }}
                        </p>

                        <div class="flex flex-wrap gap-x-3 gap-y-0.5 mt-2 text-xs text-gray-400">
                            @if($ad->category)
                                <span>{{ $ad->category->icon }} {{ $ad->category->name }}</span>
                            @endif
                            @if($ad->city || $ad->district)
                                <span>📍 {{ $ad->city?->name }}@if($ad->city && $ad->district), @endif{{ $ad->district?->name }}</span>
                            @endif
                            @if($ad->condition)
                                <span class="px-1.5 py-0.5 rounded
                                    {{ $ad->condition === 'new' ? 'bg-green-100 text-green-600' : 'bg-blue-100 text-blue-600' }}">
                                    {{ $ad->condition === 'new' ? 'New' : 'Used' }}
                                </span>
                            @endif
                            <span>{{ $ad->created_at->diffForHumans() }}</span>
                            <span>👁 {{ number_format($ad->views) }}</span>
                        </div>
                    </div>
                </a>
            @endforeach

            <div class="mt-2">{{ $results->links() }}</div>
        @endif
    </section>
</div>
@endsection

@push('scripts')
<script>
document.getElementById('sort-select').addEventListener('change', function () {
    const url = new URL(window.location.href);
    if (this.value) url.searchParams.set('sort', this.value);
    else url.searchParams.delete('sort');
    url.searchParams.delete('page');
    window.location.href = url.toString();
});

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
            this.textContent = data.favorited ? '❤️' : '🤍';
            this.title = data.favorited ? 'Remove from saved' : 'Save ad';
        } catch (err) { /* silent */ }
    });
});
</script>
@endpush
