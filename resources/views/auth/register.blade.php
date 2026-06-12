@extends('layouts.app')
@section('title', 'ලියාපදිංචි වන්න — ikman Clone')

@section('content')
<div class="max-w-md mx-auto">
    <div class="bg-white rounded-xl shadow p-8">
        <h1 class="text-2xl font-bold mb-1">ලියාපදිංචි වන්න</h1>
        <p class="text-gray-500 text-sm mb-6">Create a free account to post ads.</p>

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
                <label class="block text-sm font-medium mb-1" for="name">නම (Full Name) <span class="text-red-500">*</span></label>
                <input id="name" type="text" name="name" value="{{ old('name') }}" required autofocus
                       class="w-full border rounded-lg px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-brand/40
                              @error('name') border-red-400 @enderror"
                       placeholder="Kamal Perera">
            </div>

            <div>
                <label class="block text-sm font-medium mb-1" for="email">ඊමේල් (Email) <span class="text-red-500">*</span></label>
                <input id="email" type="email" name="email" value="{{ old('email') }}" required
                       class="w-full border rounded-lg px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-brand/40
                              @error('email') border-red-400 @enderror"
                       placeholder="you@example.com">
            </div>

            <div>
                <label class="block text-sm font-medium mb-1" for="phone">දුරකථන අංකය (Phone)</label>
                <input id="phone" type="text" name="phone" value="{{ old('phone') }}" maxlength="10"
                       class="w-full border rounded-lg px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-brand/40
                              @error('phone') border-red-400 @enderror"
                       placeholder="0712345678">
                <p class="text-xs text-gray-400 mt-1">Format: 0712345678</p>
            </div>

            <div>
                <label class="block text-sm font-medium mb-1" for="password">මුරපදය (Password) <span class="text-red-500">*</span></label>
                <input id="password" type="password" name="password" required
                       class="w-full border rounded-lg px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-brand/40
                              @error('password') border-red-400 @enderror"
                       placeholder="Min. 8 characters">
            </div>

            <div>
                <label class="block text-sm font-medium mb-1" for="password_confirmation">මුරපදය තහවුරු කරන්න <span class="text-red-500">*</span></label>
                <input id="password_confirmation" type="password" name="password_confirmation" required
                       class="w-full border rounded-lg px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-brand/40"
                       placeholder="Repeat password">
            </div>

            <button type="submit"
                    class="w-full bg-brand text-white py-2.5 rounded-lg font-semibold hover:bg-brand-light transition">
                ගිණුම සාදන්න
            </button>
        </form>

        <p class="mt-6 text-center text-sm text-gray-500">
            දැනටමත් ගිණුමක් ඇද්ද?
            <a href="{{ route('login') }}" class="text-brand font-medium hover:underline">ඇතුල් වන්න</a>
        </p>
    </div>
</div>
@endsection
