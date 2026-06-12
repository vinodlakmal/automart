@extends('layouts.app')
@section('title', __('ui.profile.title') . ' — Merkei Mart')

@section('content')
@php
    $totalAds  = $adStats->sum();
    $activeAds = $adStats->get('active', 0);
    $soldAds   = $adStats->get('sold', 0);
    $expiredAds = $adStats->get('expired', 0);
@endphp

{{-- Page header --}}
<div class="flex items-center gap-4 mb-8">
    <div class="w-16 h-16 rounded-full bg-brand flex items-center justify-center text-white text-2xl font-bold flex-shrink-0">
        {{ strtoupper(substr($user->name, 0, 1)) }}
    </div>
    <div>
        <h1 class="text-2xl font-bold leading-tight">{{ $user->name }}</h1>
        <p class="text-sm text-gray-500">{{ __('ui.profile.member_since', ['date' => $user->created_at->format('F Y')]) }}</p>
    </div>
</div>

{{-- Stats strip --}}
<div class="grid grid-cols-2 sm:grid-cols-4 gap-3 mb-8">
    <div class="bg-white rounded-xl shadow p-4 text-center">
        <p class="text-2xl font-bold text-brand">{{ $totalAds }}</p>
        <p class="text-xs text-gray-500 mt-0.5">{{ __('ui.profile.total_ads') }}</p>
    </div>
    <div class="bg-white rounded-xl shadow p-4 text-center">
        <p class="text-2xl font-bold text-green-600">{{ $activeAds }}</p>
        <p class="text-xs text-gray-500 mt-0.5">{{ __('ui.profile.active') }}</p>
    </div>
    <div class="bg-white rounded-xl shadow p-4 text-center">
        <p class="text-2xl font-bold text-blue-600">{{ $soldAds }}</p>
        <p class="text-xs text-gray-500 mt-0.5">{{ __('ui.profile.sold') }}</p>
    </div>
    <div class="bg-white rounded-xl shadow p-4 text-center">
        <p class="text-2xl font-bold text-red-500">{{ $savedCount }}</p>
        <p class="text-xs text-gray-500 mt-0.5">{{ __('ui.profile.saved_ads') }}</p>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

    {{-- ── Left: profile + password forms ── --}}
    <div class="lg:col-span-2 space-y-6">

        {{-- Profile details --}}
        <div class="bg-white rounded-xl shadow p-6">
            <h2 class="text-lg font-semibold mb-5 pb-3 border-b">{{ __('ui.profile.details') }}</h2>

            @if(session('profile_status'))
                <div class="bg-green-50 border border-green-200 text-green-800 text-sm rounded-lg px-4 py-3 mb-5">
                    {{ session('profile_status') }}
                </div>
            @endif

            <form action="{{ route('profile.update') }}" method="POST" class="space-y-4">
                @csrf @method('PUT')

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('ui.profile.full_name') }} <span class="text-red-500">*</span></label>
                    <input type="text" name="name" value="{{ old('name', $user->name) }}"
                           class="w-full border rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-brand
                                  @error('name') border-red-400 @enderror">
                    @error('name')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('ui.profile.email') }} <span class="text-red-500">*</span></label>
                    <input type="email" name="email" value="{{ old('email', $user->email) }}"
                           class="w-full border rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-brand
                                  @error('email') border-red-400 @enderror">
                    @error('email')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('ui.profile.phone') }}</label>
                    <input type="tel" name="phone" value="{{ old('phone', $user->phone) }}"
                           placeholder="0771234567"
                           class="w-full border rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-brand
                                  @error('phone') border-red-400 @enderror">
                    @error('phone')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                    <p class="text-xs text-gray-400 mt-1">{{ __('ui.profile.phone_hint') }}</p>
                </div>

                <div class="pt-2">
                    <button type="submit"
                            class="bg-brand text-white px-6 py-2.5 rounded-lg font-semibold hover:bg-brand-light transition">
                        {{ __('ui.profile.save_changes') }}
                    </button>
                </div>
            </form>
        </div>

        {{-- Change password --}}
        <div class="bg-white rounded-xl shadow p-6">
            <h2 class="text-lg font-semibold mb-5 pb-3 border-b">{{ __('ui.profile.change_password') }}</h2>

            @if(session('password_status'))
                <div class="bg-green-50 border border-green-200 text-green-800 text-sm rounded-lg px-4 py-3 mb-5">
                    {{ session('password_status') }}
                </div>
            @endif

            <form action="{{ route('profile.password') }}" method="POST" class="space-y-4">
                @csrf @method('PUT')

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('ui.profile.current_password') }} <span class="text-red-500">*</span></label>
                    <input type="password" name="current_password"
                           class="w-full border rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-brand
                                  @error('current_password') border-red-400 @enderror">
                    @error('current_password')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('ui.profile.new_password') }} <span class="text-red-500">*</span></label>
                    <input type="password" name="password"
                           class="w-full border rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-brand
                                  @error('password') border-red-400 @enderror">
                    @error('password')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                    <p class="text-xs text-gray-400 mt-1">{{ __('ui.profile.password_hint') }}</p>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('ui.profile.confirm_password') }} <span class="text-red-500">*</span></label>
                    <input type="password" name="password_confirmation"
                           class="w-full border rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-brand">
                </div>

                <div class="pt-2">
                    <button type="submit"
                            class="bg-gray-800 text-white px-6 py-2.5 rounded-lg font-semibold hover:bg-gray-700 transition">
                        {{ __('ui.profile.update_password') }}
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- ── Right: quick links + account info ── --}}
    <aside class="space-y-4">

        {{-- Quick links --}}
        <div class="bg-white rounded-xl shadow p-5">
            <h3 class="font-semibold text-sm text-gray-600 uppercase tracking-wide mb-4">Quick Links</h3>
            <nav class="space-y-1">
                <a href="{{ route('ads.myAds') }}"
                   class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm text-gray-700 hover:bg-gray-50 transition">
                    <span class="text-lg">📋</span>
                    <span>My Ads</span>
                    @if($totalAds)
                        <span class="ml-auto text-xs bg-brand/10 text-brand font-semibold px-2 py-0.5 rounded-full">{{ $totalAds }}</span>
                    @endif
                </a>
                <a href="{{ route('favorites') }}"
                   class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm text-gray-700 hover:bg-gray-50 transition">
                    <span class="text-lg">❤️</span>
                    <span>Saved Ads</span>
                    @if($savedCount)
                        <span class="ml-auto text-xs bg-red-100 text-red-600 font-semibold px-2 py-0.5 rounded-full">{{ $savedCount }}</span>
                    @endif
                </a>
                <a href="{{ route('ads.create') }}"
                   class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm text-gray-700 hover:bg-gray-50 transition">
                    <span class="text-lg">➕</span>
                    <span>Post a New Ad</span>
                </a>
            </nav>
        </div>

        {{-- Account info --}}
        <div class="bg-white rounded-xl shadow p-5 text-sm divide-y">
            <div class="flex justify-between py-2.5">
                <span class="text-gray-500">Member since</span>
                <span class="font-medium">{{ $user->created_at->format('d M Y') }}</span>
            </div>
            <div class="flex justify-between py-2.5">
                <span class="text-gray-500">Email</span>
                <span class="font-medium truncate ml-2 max-w-[140px]" title="{{ $user->email }}">{{ $user->email }}</span>
            </div>
            @if($user->phone)
                <div class="flex justify-between py-2.5">
                    <span class="text-gray-500">Phone</span>
                    <span class="font-medium">{{ $user->phone }}</span>
                </div>
            @endif
            <div class="flex justify-between py-2.5">
                <span class="text-gray-500">Expired ads</span>
                <span class="font-medium text-orange-600">{{ $expiredAds }}</span>
            </div>
        </div>

        {{-- Danger zone --}}
        <div class="bg-white rounded-xl shadow p-5">
            <h3 class="font-semibold text-sm text-gray-600 uppercase tracking-wide mb-3">Session</h3>
            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button type="submit"
                        class="w-full text-sm text-red-600 border border-red-200 rounded-lg py-2 hover:bg-red-50 transition">
                    Sign Out
                </button>
            </form>
        </div>
    </aside>

</div>
@endsection
