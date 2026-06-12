@extends('layouts.app')
@section('title', 'ඇතුල් වන්න — ikman Clone')

@section('content')
<div class="max-w-md mx-auto">
    <div class="bg-white rounded-xl shadow p-8">
        <h1 class="text-2xl font-bold mb-1">ඇතුල් වන්න</h1>
        <p class="text-gray-500 text-sm mb-6">Sign in to post or manage your ads.</p>

        @if($errors->any())
            <div class="bg-red-50 border border-red-200 text-red-700 rounded-lg p-4 mb-6 text-sm">
                @foreach($errors->all() as $error)<p>{{ $error }}</p>@endforeach
            </div>
        @endif

        @if(session('status'))
            <div class="bg-green-50 border border-green-200 text-green-700 rounded-lg p-4 mb-6 text-sm">
                {{ session('status') }}
            </div>
        @endif

        <form action="{{ route('login') }}" method="POST" class="space-y-5">
            @csrf

            <div>
                <label class="block text-sm font-medium mb-1" for="email">ඊමේල් (Email)</label>
                <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus
                       class="w-full border rounded-lg px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-brand/40
                              @error('email') border-red-400 @enderror"
                       placeholder="you@example.com">
            </div>

            <div>
                <label class="block text-sm font-medium mb-1" for="password">මුරපදය (Password)</label>
                <input id="password" type="password" name="password" required
                       class="w-full border rounded-lg px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-brand/40"
                       placeholder="••••••••">
            </div>

            <div class="flex items-center justify-between text-sm">
                <label class="flex items-center gap-2">
                    <input type="checkbox" name="remember" class="h-4 w-4">
                    මතක තබාගන්න (Remember me)
                </label>
            </div>

            <button type="submit"
                    class="w-full bg-brand text-white py-2.5 rounded-lg font-semibold hover:bg-brand-light transition">
                ඇතුල් වන්න
            </button>
        </form>

        <p class="mt-6 text-center text-sm text-gray-500">
            ගිණුමක් නැද්ද?
            <a href="{{ route('register') }}" class="text-brand font-medium hover:underline">ලියාපදිංචි වන්න</a>
        </p>
    </div>

    {{-- Demo credentials hint --}}
    <div class="mt-4 bg-blue-50 border border-blue-200 rounded-lg p-4 text-sm text-blue-700">
        <strong>Demo account:</strong> demo@ikman.test / password
    </div>
</div>
@endsection
