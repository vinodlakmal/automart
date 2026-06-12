@extends('layouts.app')
@section('title', 'About — Merkei Mart')

@section('content')

{{-- Hero --}}
<section class="bg-brand text-white rounded-2xl px-6 py-14 text-center mb-10">
    <h1 class="text-4xl font-bold">About Merkei Mart</h1>
    <p class="mt-3 text-white/80 max-w-xl mx-auto text-lg">
        Sri Lanka's free online marketplace — buy, sell, and rent anything, anywhere.
    </p>
</section>

{{-- Live stats --}}
<section class="grid grid-cols-2 sm:grid-cols-4 gap-4 mb-12">
    @foreach([
        ['value' => number_format($stats['ads']),        'label' => 'Active Listings'],
        ['value' => number_format($stats['users']),      'label' => 'Registered Users'],
        ['value' => number_format($stats['categories']), 'label' => 'Categories'],
        ['value' => number_format($stats['districts']),  'label' => 'Districts Covered'],
    ] as $stat)
        <div class="bg-white rounded-xl shadow p-6 text-center">
            <div class="text-3xl font-bold text-brand">{{ $stat['value'] }}</div>
            <div class="text-sm text-gray-500 mt-1">{{ $stat['label'] }}</div>
        </div>
    @endforeach
</section>

{{-- Mission + How it works --}}
<div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-12">
    <div class="bg-white rounded-xl shadow p-8">
        <h2 class="text-xl font-bold mb-4">Our Mission</h2>
        <p class="text-gray-600 leading-relaxed">
            Merkei Mart was built to give every Sri Lankan a simple, fast, and free way to
            connect buyers and sellers. Whether you're selling a used car in Colombo, renting
            a room in Kandy, or offering tuition classes in Galle — we make it easy to reach
            the right people.
        </p>
        <p class="text-gray-600 leading-relaxed mt-4">
            We believe commerce should be accessible to everyone. That's why posting an ad
            is always free, and browsing requires no account.
        </p>
    </div>

    <div class="bg-white rounded-xl shadow p-8">
        <h2 class="text-xl font-bold mb-4">How It Works</h2>
        <ol class="space-y-4">
            @foreach([
                ['icon' => '📝', 'title' => 'Post your ad',     'desc' => 'Fill in the details, add photos, and publish in minutes — completely free.'],
                ['icon' => '🔍', 'title' => 'Buyers find you',  'desc' => 'Listings are searchable by category, location, and price with no barriers.'],
                ['icon' => '📞', 'title' => 'Connect directly', 'desc' => 'Buyers call or WhatsApp you directly — no middleman, no fees.'],
                ['icon' => '🤝', 'title' => 'Close the deal',   'desc' => 'Meet safely, inspect the item, and transact on your terms.'],
            ] as $step)
                <li class="flex gap-4">
                    <span class="text-2xl flex-shrink-0">{{ $step['icon'] }}</span>
                    <div>
                        <p class="font-semibold text-gray-800">{{ $step['title'] }}</p>
                        <p class="text-sm text-gray-500">{{ $step['desc'] }}</p>
                    </div>
                </li>
            @endforeach
        </ol>
    </div>
</div>

{{-- Categories we cover --}}
<section class="bg-white rounded-xl shadow p-8 mb-12">
    <h2 class="text-xl font-bold mb-6">What You Can Buy &amp; Sell</h2>
    @php $cats = \App\Models\Category::roots()->active()->orderBy('sort_order')->get(); @endphp
    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-7 gap-4">
        @foreach($cats as $cat)
            <a href="{{ route('ads.index', ['category' => $cat->id]) }}"
               class="flex flex-col items-center text-center p-4 rounded-lg hover:bg-gray-50 transition group">
                <span class="text-4xl group-hover:scale-110 transition-transform">{{ $cat->icon }}</span>
                <span class="text-sm font-medium mt-2 text-gray-700">{{ $cat->name }}</span>
            </a>
        @endforeach
    </div>
</section>

{{-- CTA --}}
<section class="text-center py-8">
    <h2 class="text-2xl font-bold mb-3">Ready to get started?</h2>
    <p class="text-gray-500 mb-6">Post your first ad for free — it takes less than 2 minutes.</p>
    <a href="{{ route('ads.create') }}"
       class="inline-block bg-brand text-white font-semibold px-8 py-3 rounded-lg hover:bg-brand-light transition">
        + Post a Free Ad
    </a>
    <span class="mx-4 text-gray-300">or</span>
    <a href="{{ route('ads.index') }}"
       class="inline-block border border-brand text-brand font-semibold px-8 py-3 rounded-lg hover:bg-brand hover:text-white transition">
        Browse Listings
    </a>
</section>

@endsection
