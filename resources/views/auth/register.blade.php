@extends('layouts.app')
@section('title', __('ui.auth.register') . ' — Merkei Mart')

@section('content')
<div class="max-w-md mx-auto">
    <div class="bg-white rounded-xl shadow p-8">
        <h1 class="text-2xl font-bold mb-1">{{ __('ui.auth.register') }}</h1>
        <p class="text-gray-500 text-sm mb-6">{{ __('ui.auth.register_sub') }}</p>

        @if($errors->any())
            <div class="bg-red-50 border border-red-200 text-red-700 rounded-lg p-4 mb-6 text-sm">
                <ul class="list-disc list-inside space-y-1">
                    @foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('register') }}" method="POST" class="space-y-5">
            @csrf

            <div>
                <label class="block text-sm font-medium mb-1" for="name">
                    {{ __('ui.auth.full_name') }} <span class="text-red-500">*</span>
                </label>
                <input id="name" type="text" name="name" value="{{ old('name') }}" required autofocus
                       class="w-full border rounded-lg px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-brand/40
                              @error('name') border-red-400 @enderror"
                       placeholder="Kamal Perera">
            </div>

            <div>
                <label class="block text-sm font-medium mb-1" for="email">
                    {{ __('ui.auth.email') }} <span class="text-red-500">*</span>
                </label>
                <input id="email" type="email" name="email" value="{{ old('email') }}" required
                       class="w-full border rounded-lg px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-brand/40
                              @error('email') border-red-400 @enderror"
                       placeholder="you@example.com">
            </div>

            <div>
                <label class="block text-sm font-medium mb-1" for="phone">{{ __('ui.auth.phone') }}</label>
                <input id="phone" type="text" name="phone" value="{{ old('phone') }}" maxlength="10"
                       class="w-full border rounded-lg px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-brand/40
                              @error('phone') border-red-400 @enderror"
                       placeholder="0712345678">
                <p class="text-xs text-gray-400 mt-1">{{ __('ui.auth.phone_format') }}</p>
            </div>

            <div>
                <label class="block text-sm font-medium mb-1" for="password">
                    {{ __('ui.auth.password') }} <span class="text-red-500">*</span>
                </label>
                <input id="password" type="password" name="password" required
                       class="w-full border rounded-lg px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-brand/40
                              @error('password') border-red-400 @enderror"
                       placeholder="{{ __('ui.auth.min_chars') }}">
            </div>

            <div>
                <label class="block text-sm font-medium mb-1" for="password_confirmation">
                    {{ __('ui.auth.confirm_password') }} <span class="text-red-500">*</span>
                </label>
                <input id="password_confirmation" type="password" name="password_confirmation" required
                       class="w-full border rounded-lg px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-brand/40"
                       placeholder="{{ __('ui.auth.repeat_password') }}">
            </div>

            <button type="submit"
                    class="w-full bg-brand text-white py-2.5 rounded-lg font-semibold hover:bg-brand-light transition">
                {{ __('ui.auth.create_account') }}
            </button>
        </form>

        <p class="mt-6 text-center text-sm text-gray-500">
            {{ __('ui.auth.have_account') }}
            <a href="{{ route('login') }}" class="text-brand font-medium hover:underline">{{ __('ui.auth.sign_in_link') }}</a>
        </p>
    </div>
</div>
@endsection
