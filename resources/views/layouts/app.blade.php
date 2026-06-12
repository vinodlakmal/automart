<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', config('app.name', 'Merkei Mart'))</title>

    {{-- Tailwind via CDN for zero-build dev. For production run `npm run build`
         and replace this with @vite(['resources/css/app.css','resources/js/app.js']). --}}
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: { extend: { colors: { brand: { DEFAULT:'#1b5e20', light:'#2e7d32', dark:'#0d3d12' } } } }
        }
    </script>
    @stack('head')
</head>
<body class="bg-gray-100 text-gray-800 antialiased">
    <header class="bg-brand text-white shadow">
        <div class="max-w-6xl mx-auto px-4 py-3 flex items-center justify-between gap-4">
            <a href="{{ route('home') }}" class="text-2xl font-bold tracking-tight">Merkei Mart</a>

            <form action="{{ route('search') }}" method="GET" class="flex-1 max-w-xl hidden sm:flex">
                <input type="text" name="q" value="{{ request('q') }}" placeholder="සොයන්න... (Search ads)"
                       class="w-full rounded-l px-4 py-2 text-gray-800 focus:outline-none">
                <button class="bg-yellow-400 text-brand-dark font-semibold px-4 rounded-r hover:bg-yellow-300">සොයන්න</button>
            </form>

            <nav class="flex items-center gap-3 text-sm">
                @auth
                    <a href="{{ route('favorites') }}" class="hover:underline" title="Saved Ads">❤️</a>
                    <a href="{{ route('ads.myAds') }}" class="hover:underline">මගේ දැන්වීම්</a>
                    @if(Route::has('logout'))
                        <form action="{{ route('logout') }}" method="POST">@csrf
                            <button class="hover:underline">ඉවත් වන්න</button>
                        </form>
                    @endif
                @else
                    @if(Route::has('login'))<a href="{{ route('login') }}" class="hover:underline">ඇතුල් වන්න</a>@endif
                    @if(Route::has('register'))<a href="{{ route('register') }}" class="hover:underline">ලියාපදිංචි</a>@endif
                @endauth
                <a href="{{ route('ads.create') }}" class="bg-yellow-400 text-brand-dark font-semibold px-4 py-2 rounded hover:bg-yellow-300">+ දැන්වීමක් පළ කරන්න</a>
            </nav>
        </div>
    </header>

    @if(session('status'))
        <div class="max-w-6xl mx-auto px-4 mt-4">
            <div class="bg-green-100 border border-green-300 text-green-800 px-4 py-3 rounded">{{ session('status') }}</div>
        </div>
    @endif

    <main class="max-w-6xl mx-auto px-4 py-6">
        @yield('content')
    </main>

    <footer class="bg-white border-t mt-12">
        @php $footerCategories = \App\Models\Category::roots()->active()->orderBy('sort_order')->get(); @endphp
        <div class="max-w-6xl mx-auto px-4 pt-10 pb-6">
            <div class="grid grid-cols-2 sm:grid-cols-3 gap-8 mb-8">

                {{-- Browse --}}
                <div>
                    <h3 class="text-sm font-semibold text-gray-800 uppercase tracking-wide mb-3">Browse</h3>
                    <ul class="space-y-2 text-sm text-gray-500">
                        <li><a href="{{ route('ads.index') }}" class="hover:text-brand hover:underline">All Listings</a></li>
                        <li><a href="{{ route('ads.create') }}" class="hover:text-brand hover:underline">Post a Free Ad</a></li>
                        @auth
                            <li><a href="{{ route('ads.myAds') }}" class="hover:text-brand hover:underline">My Ads</a></li>
                        @endauth
                        <li><a href="{{ route('about') }}" class="hover:text-brand hover:underline">About Us</a></li>
                        <li><a href="{{ route('contact') }}" class="hover:text-brand hover:underline">Contact</a></li>
                    </ul>
                </div>

                {{-- Categories --}}
                <div>
                    <h3 class="text-sm font-semibold text-gray-800 uppercase tracking-wide mb-3">Categories</h3>
                    <ul class="space-y-2 text-sm text-gray-500">
                        @foreach($footerCategories as $cat)
                            <li>
                                <a href="{{ route('ads.index', ['category' => $cat->id]) }}"
                                   class="hover:text-brand hover:underline">
                                    {{ $cat->icon }} {{ $cat->name }}
                                </a>
                            </li>
                        @endforeach
                    </ul>
                </div>

                {{-- Account --}}
                <div>
                    <h3 class="text-sm font-semibold text-gray-800 uppercase tracking-wide mb-3">Account</h3>
                    <ul class="space-y-2 text-sm text-gray-500">
                        @auth
                            <li><a href="{{ route('favorites') }}" class="hover:text-brand hover:underline">❤️ Saved Ads</a></li>
                            <li><a href="{{ route('ads.myAds') }}" class="hover:text-brand hover:underline">My Ads</a></li>
                            <li><a href="{{ route('ads.create') }}" class="hover:text-brand hover:underline">Post an Ad</a></li>
                            <li>
                                <form action="{{ route('logout') }}" method="POST" class="inline">
                                    @csrf
                                    <button class="hover:text-brand hover:underline text-left">Sign Out</button>
                                </form>
                            </li>
                        @else
                            <li><a href="{{ route('login') }}" class="hover:text-brand hover:underline">Sign In</a></li>
                            <li><a href="{{ route('register') }}" class="hover:text-brand hover:underline">Create Account</a></li>
                            <li><a href="{{ route('ads.create') }}" class="hover:text-brand hover:underline">Post an Ad</a></li>
                        @endauth
                    </ul>
                </div>
            </div>

            {{-- Bottom bar --}}
            <div class="border-t pt-5 flex flex-col sm:flex-row items-center justify-between gap-2 text-xs text-gray-400">
                <span>&copy; {{ date('Y') }} Merkei Mart. All rights reserved.</span>
                <span>Built with <a href="https://laravel.com" target="_blank" rel="noopener" class="hover:text-brand">Laravel</a> &amp; hosted on Docker.</span>
            </div>
        </div>
    </footer>
    @stack('scripts')
</body>
</html>
