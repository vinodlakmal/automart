@extends('layouts.app')
@section('title', 'දැන්වීම් — Merkei Mart')

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
        $activeFilters['condition'] = request('condition') === 'new' ? 'New' : 'Used';
@endphp

<div class="grid grid-cols-1 lg:grid-cols-4 gap-6">

    {{-- Filters sidebar --}}
    <aside class="lg:col-span-1">
        <form id="filter-form" action="{{ route('ads.index') }}" method="GET"
              class="bg-white rounded-lg shadow p-4 space-y-4 sticky top-4">

            {{-- preserve sort when filters are resubmitted --}}
            @if(request('sort'))
                <input type="hidden" name="sort" value="{{ request('sort') }}">
            @endif

            <h2 class="font-semibold text-lg border-b pb-2">පෙරහන් (Filters)</h2>

            <div class="sm:hidden">
                <label class="block text-sm font-medium mb-1">සෙවුම</label>
                <input type="text" name="q" value="{{ request('q') }}" placeholder="Search..."
                       class="w-full border rounded px-3 py-2">
            </div>

            <div>
                <label class="block text-sm font-medium mb-1">ප්‍රවර්ගය</label>
                <select name="category" class="w-full border rounded px-3 py-2">
                    <option value="">සියලුම ප්‍රවර්ග</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat->id }}" @selected(request('category') == $cat->id)>
                            {{ $cat->icon }} {{ $cat->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-sm font-medium mb-1">දිස්ත්‍රික්කය</label>
                <select name="district" class="w-full border rounded px-3 py-2">
                    <option value="">සියලුම දිස්ත්‍රික්ක</option>
                    @foreach($districts as $d)
                        <option value="{{ $d->id }}" @selected(request('district') == $d->id)>{{ $d->name }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-sm font-medium mb-1">මිල පරාසය (Rs.)</label>
                <div class="flex gap-2">
                    <input type="number" name="min_price" value="{{ request('min_price') }}"
                           placeholder="අවම" class="w-1/2 border rounded px-2 py-2">
                    <input type="number" name="max_price" value="{{ request('max_price') }}"
                           placeholder="උපරිම" class="w-1/2 border rounded px-2 py-2">
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium mb-1">තත්ත්වය</label>
                <select name="condition" class="w-full border rounded px-3 py-2">
                    <option value="">ඕනෑම</option>
                    <option value="new"  @selected(request('condition') === 'new')>අලුත් (New)</option>
                    <option value="used" @selected(request('condition') === 'used')>පාවිච්චි කළ (Used)</option>
                </select>
            </div>

            <button class="w-full bg-brand text-white py-2 rounded hover:bg-brand-light">
                පෙරහන් කරන්න
            </button>
            <a href="{{ route('ads.index') }}"
               class="block text-center text-sm text-gray-500 hover:underline">යළි පිහිටුවන්න</a>
        </form>
    </aside>

    {{-- Results --}}
    <section class="lg:col-span-3">

        {{-- Active filter chips --}}
        @if(count($activeFilters) > 0)
            <div class="flex flex-wrap items-center gap-2 mb-4">
                @foreach($activeFilters as $key => $label)
                    @php
                        $params = Arr::except($currentParams, $key);
                    @endphp
                    <a href="{{ route('ads.index', $params) }}"
                       class="inline-flex items-center gap-1 bg-brand/10 text-brand text-sm px-3 py-1 rounded-full hover:bg-brand/20 transition">
                        {{ $label }} <span class="text-base leading-none">&times;</span>
                    </a>
                @endforeach
                <a href="{{ route('ads.index') }}"
                   class="text-sm text-gray-400 hover:text-gray-600 hover:underline ml-1">Clear all</a>
            </div>
        @endif

        {{-- Results bar: count + sort --}}
        <div class="flex items-center justify-between mb-4">
            <p class="text-gray-600 text-sm">{{ number_format($ads->total()) }} දැන්වීම් හමු විය</p>

            <select id="sort-select"
                    class="border rounded px-3 py-1.5 text-sm bg-white focus:outline-none focus:ring-1 focus:ring-brand">
                <option value=""           @selected(! request('sort'))>නවතම (Newest)</option>
                <option value="price_asc"  @selected(request('sort') === 'price_asc')>මිල: අඩුම සිට (Price ↑)</option>
                <option value="price_desc" @selected(request('sort') === 'price_desc')>මිල: වැඩිම සිට (Price ↓)</option>
                <option value="views"      @selected(request('sort') === 'views')>වඩාත් නරඹා (Most Viewed)</option>
            </select>
        </div>

        @if($ads->isEmpty())
            <div class="bg-white rounded-lg shadow p-10 text-center text-gray-500">
                දැන්වීම් කිසිවක් හමු නොවීය.
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
                                    TOP AD
                                </span>
                            @endif
                        </div>
                        <div class="p-3 flex-1 flex flex-col">
                            <h3 class="font-medium line-clamp-2 text-sm">{{ $ad->title }}</h3>
                            <p class="text-brand font-bold mt-1">
                                @if(! is_null($ad->price))
                                    Rs. {{ number_format($ad->price) }}
                                @else
                                    <span class="text-gray-500 font-normal text-sm">කතා කර තීරණය කරගත හැක</span>
                                @endif
                                @if($ad->is_negotiable)
                                    <span class="text-xs text-gray-500 font-normal">(negotiable)</span>
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
</script>
@endpush
