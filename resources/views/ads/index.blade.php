@extends('layouts.app')
@section('title', __('ui.ads.title') . ' — Merkei Mart')

@section('content')
@php
    $knownParams = ['q', 'category', 'district', 'min_price', 'max_price', 'condition', 'sort'];
    $currentParams = collect($knownParams)
        ->mapWithKeys(fn ($k) => [$k => request($k)])
        ->filter()
        ->all();

    $activeFilters = [];
    if (request('q'))
        $activeFilters['q'] = 'Search: "' . request('q') . '"';
    if (request('category') && ($cat = $categories->firstWhere('id', request('category'))))
        $activeFilters['category'] = $cat->name;
    if (request('district') && ($dist = $districts->firstWhere('id', request('district'))))
        $activeFilters['district'] = $dist->name;
    if (request('min_price'))
        $activeFilters['min_price'] = 'Min Rs. ' . number_format(request('min_price'));
    if (request('max_price'))
        $activeFilters['max_price'] = 'Max Rs. ' . number_format(request('max_price'));
    if (request('condition'))
        $activeFilters['condition'] = request('condition') === 'new' ? __('ui.ads.new') : __('ui.ads.used');
@endphp

<div class="grid grid-cols-1 lg:grid-cols-4 gap-6">

    {{-- Filters sidebar --}}
    <aside class="lg:col-span-1">
        <form id="filter-form" action="{{ route('ads.index') }}" method="GET"
              class="bg-white rounded-lg shadow p-4 space-y-4 sticky top-4">

            @if(request('sort'))
                <input type="hidden" name="sort" value="{{ request('sort') }}">
            @endif

            <h2 class="font-semibold text-lg border-b pb-2">{{ __('ui.ads.filters') }}</h2>

            <div class="sm:hidden">
                <label class="block text-sm font-medium mb-1">{{ __('ui.ads.search_label') }}</label>
                <input type="text" name="q" value="{{ request('q') }}" placeholder="{{ __('ui.nav.search_placeholder') }}"
                       class="w-full border rounded px-3 py-2">
            </div>

            <div>
                <label class="block text-sm font-medium mb-1">{{ __('ui.ads.category') }}</label>
                <select name="category" class="w-full border rounded px-3 py-2">
                    <option value="">{{ __('ui.ads.all_categories') }}</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat->id }}" @selected(request('category') == $cat->id)>
                            {{ $cat->icon }} {{ $cat->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-sm font-medium mb-1">{{ __('ui.ads.district') }}</label>
                <select name="district" class="w-full border rounded px-3 py-2">
                    <option value="">{{ __('ui.ads.all_districts') }}</option>
                    @foreach($districts as $d)
                        <option value="{{ $d->id }}" @selected(request('district') == $d->id)>{{ $d->name }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-sm font-medium mb-1">{{ __('ui.ads.price_range') }}</label>
                <div class="flex gap-2">
                    <input type="number" name="min_price" value="{{ request('min_price') }}"
                           placeholder="{{ __('ui.ads.min') }}" class="w-1/2 border rounded px-2 py-2">
                    <input type="number" name="max_price" value="{{ request('max_price') }}"
                           placeholder="{{ __('ui.ads.max') }}" class="w-1/2 border rounded px-2 py-2">
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium mb-1">{{ __('ui.ads.condition') }}</label>
                <select name="condition" class="w-full border rounded px-3 py-2">
                    <option value="">{{ __('ui.ads.any') }}</option>
                    <option value="new"  @selected(request('condition') === 'new')>{{ __('ui.ads.new') }}</option>
                    <option value="used" @selected(request('condition') === 'used')>{{ __('ui.ads.used') }}</option>
                </select>
            </div>

            <button class="w-full bg-brand text-white py-2 rounded hover:bg-brand-light">
                {{ __('ui.ads.apply_filters') }}
            </button>
            <a href="{{ route('ads.index') }}"
               class="block text-center text-sm text-gray-500 hover:underline">{{ __('ui.ads.reset') }}</a>
        </form>
    </aside>

    {{-- Results --}}
    <section class="lg:col-span-3">

        @if(count($activeFilters) > 0)
            <div class="flex flex-wrap items-center gap-2 mb-4">
                @foreach($activeFilters as $key => $label)
                    @php $params = Arr::except($currentParams, $key); @endphp
                    <a href="{{ route('ads.index', $params) }}"
                       class="inline-flex items-center gap-1 bg-brand/10 text-brand text-sm px-3 py-1 rounded-full hover:bg-brand/20 transition">
                        {{ $label }} <span class="text-base leading-none">&times;</span>
                    </a>
                @endforeach
                <a href="{{ route('ads.index') }}"
                   class="text-sm text-gray-400 hover:text-gray-600 hover:underline ml-1">{{ __('ui.search.clear_all') }}</a>
            </div>
        @endif

        <div class="flex items-center justify-between mb-4">
            <p class="text-gray-600 text-sm">{{ __('ui.ads.found', ['count' => number_format($ads->total())]) }}</p>

            <select id="sort-select"
                    class="border rounded px-3 py-1.5 text-sm bg-white focus:outline-none focus:ring-1 focus:ring-brand">
                <option value=""           @selected(! request('sort'))>{{ __('ui.ads.newest') }}</option>
                <option value="price_asc"  @selected(request('sort') === 'price_asc')>{{ __('ui.ads.price_low_high') }}</option>
                <option value="price_desc" @selected(request('sort') === 'price_desc')>{{ __('ui.ads.price_high_low') }}</option>
                <option value="views"      @selected(request('sort') === 'views')>{{ __('ui.ads.most_viewed') }}</option>
            </select>
        </div>

        @if($ads->isEmpty())
            <div class="bg-white rounded-lg shadow p-10 text-center text-gray-500">
                {{ __('ui.ads.no_results') }}
            </div>
        @else
            <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-4">
                @foreach($ads as $ad)
                    <a href="{{ route('ads.show', $ad) }}"
                       class="bg-white rounded-lg shadow hover:shadow-md transition overflow-hidden flex flex-col">
                        <div class="aspect-video bg-gray-100 relative">
                            @if($ad->primaryImage)
                                <img src="{{ $ad->primaryImage->url }}"
                                     alt="{{ $ad->title }}"
                                     class="w-full h-full object-cover">
                            @else
                                <div class="w-full h-full flex items-center justify-center text-gray-400 text-4xl">
                                    {{ $ad->category?->icon ?? '📦' }}
                                </div>
                            @endif
                            @if($ad->is_featured)
                                <span class="absolute top-2 left-2 bg-yellow-400 text-brand-dark text-xs font-bold px-2 py-1 rounded">
                                    {{ __('ui.ads.top_ad') }}
                                </span>
                            @endif
                            @auth
                                <button type="button"
                                        class="fav-card-btn absolute top-2 right-2 w-8 h-8 rounded-full bg-white/80 shadow flex items-center justify-center text-base hover:bg-white transition z-10"
                                        data-url="{{ route('ads.favorite', $ad) }}"
                                        title="{{ in_array($ad->id, $favoriteIds) ? __('ui.ads.saved') : __('ui.ads.save_ad') }}">
                                    {{ in_array($ad->id, $favoriteIds) ? '❤️' : '🤍' }}
                                </button>
                            @endauth
                        </div>
                        <div class="p-3 flex-1 flex flex-col">
                            <h3 class="font-medium line-clamp-2 text-sm">{{ $ad->title }}</h3>
                            <p class="text-brand font-bold mt-1">
                                @if(! is_null($ad->price))
                                    {{ __('ui.common.rs') }} {{ number_format($ad->price) }}
                                @else
                                    <span class="text-gray-500 font-normal text-sm">{{ __('ui.ads.price_on_request') }}</span>
                                @endif
                                @if($ad->is_negotiable)
                                    <span class="text-xs text-gray-500 font-normal">({{ __('ui.ads.negotiable') }})</span>
                                @endif
                            </p>
                            <p class="text-xs text-gray-400 mt-auto pt-2">
                                {{ $ad->city?->name }}, {{ $ad->district?->name }}
                                &middot; {{ $ad->created_at->diffForHumans() }}
                            </p>
                        </div>
                    </a>
                @endforeach
            </div>

            <div class="mt-6">{{ $ads->links() }}</div>
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
        } catch (err) { /* silent */ }
    });
});
</script>
@endpush
