<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', config('app.name', 'Merkei Mart'))</title>

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
            <a href="{{ route('home') }}" class="text-2xl font-bold tracking-tight flex-shrink-0">Merkei Mart</a>

            <form action="{{ route('search') }}" method="GET" class="flex-1 max-w-xl hidden sm:flex">
                <input type="text" name="q" value="{{ request('q') }}"
                       placeholder="{{ __('ui.nav.search_placeholder') }}"
                       class="w-full rounded-l px-4 py-2 text-gray-800 focus:outline-none">
                <button class="bg-yellow-400 text-brand-dark font-semibold px-4 rounded-r hover:bg-yellow-300">
                    {{ __('ui.nav.search') }}
                </button>
            </form>

            <nav class="flex items-center gap-3 text-sm">
                @auth
                    @if(auth()->user()->is_admin)
                        <a href="{{ route('admin.dashboard') }}"
                           class="bg-white/20 hover:bg-white/30 px-2 py-1 rounded text-xs font-semibold tracking-wide transition">
                            {{ __('ui.nav.admin') }}
                        </a>
                    @endif
                    <a href="{{ route('favorites') }}" class="hover:underline" title="{{ __('ui.nav.saved_ads') }}">❤️</a>
                    <a href="{{ route('ads.myAds') }}" class="hover:underline">{{ __('ui.nav.my_ads') }}</a>
                    <a href="{{ route('profile') }}"
                       class="flex items-center gap-1.5 bg-white/20 hover:bg-white/30 px-3 py-1.5 rounded-lg transition"
                       title="{{ __('ui.nav.profile') }}">
                        <span class="w-6 h-6 rounded-full bg-yellow-400 text-brand-dark font-bold text-xs flex items-center justify-center flex-shrink-0">
                            {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                        </span>
                        <span class="hidden sm:inline max-w-[8rem] truncate">{{ auth()->user()->name }}</span>
                    </a>
                    @if(Route::has('logout'))
                        <form action="{{ route('logout') }}" method="POST">@csrf
                            <button class="hover:underline">{{ __('ui.nav.sign_out') }}</button>
                        </form>
                    @endif
                @else
                    @if(Route::has('login'))<a href="{{ route('login') }}" class="hover:underline">{{ __('ui.nav.sign_in') }}</a>@endif
                    @if(Route::has('register'))<a href="{{ route('register') }}" class="hover:underline">{{ __('ui.nav.register') }}</a>@endif
                @endauth
                <a href="{{ route('ads.create') }}" class="bg-yellow-400 text-brand-dark font-semibold px-4 py-2 rounded hover:bg-yellow-300">
                    {{ __('ui.nav.post_ad') }}
                </a>
            </nav>
        </div>

        {{-- Language switcher --}}
        <div class="bg-brand-dark/40">
            <div class="max-w-6xl mx-auto px-4 py-1 flex items-center gap-1 justify-end text-xs">
                @foreach(['en' => 'English', 'si' => 'සිංහල', 'ta' => 'தமிழ்'] as $code => $label)
                    <a href="{{ route('locale.switch', $code) }}"
                       class="px-2 py-0.5 rounded transition
                              {{ app()->getLocale() === $code
                                  ? 'bg-white/30 text-white font-semibold'
                                  : 'text-white/70 hover:text-white hover:bg-white/20' }}">
                        {{ $label }}
                    </a>
                    @if(! $loop->last)<span class="text-white/30">|</span>@endif
                @endforeach
            </div>
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

                <div>
                    <h3 class="text-sm font-semibold text-gray-800 uppercase tracking-wide mb-3">{{ __('ui.footer.browse') }}</h3>
                    <ul class="space-y-2 text-sm text-gray-500">
                        <li><a href="{{ route('ads.index') }}" class="hover:text-brand hover:underline">{{ __('ui.footer.all_listings') }}</a></li>
                        <li><a href="{{ route('ads.create') }}" class="hover:text-brand hover:underline">{{ __('ui.footer.post_free_ad') }}</a></li>
                        @auth
                            <li><a href="{{ route('ads.myAds') }}" class="hover:text-brand hover:underline">{{ __('ui.footer.my_ads') }}</a></li>
                        @endauth
                        <li><a href="{{ route('about') }}" class="hover:text-brand hover:underline">{{ __('ui.footer.about') }}</a></li>
                        <li><a href="{{ route('contact') }}" class="hover:text-brand hover:underline">{{ __('ui.footer.contact') }}</a></li>
                    </ul>
                </div>

                <div>
                    <h3 class="text-sm font-semibold text-gray-800 uppercase tracking-wide mb-3">{{ __('ui.footer.categories') }}</h3>
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

                <div>
                    <h3 class="text-sm font-semibold text-gray-800 uppercase tracking-wide mb-3">{{ __('ui.footer.account') }}</h3>
                    <ul class="space-y-2 text-sm text-gray-500">
                        @auth
                            <li><a href="{{ route('profile') }}"   class="hover:text-brand hover:underline">{{ __('ui.footer.my_profile') }}</a></li>
                            <li><a href="{{ route('favorites') }}" class="hover:text-brand hover:underline">{{ __('ui.footer.saved_ads') }}</a></li>
                            <li><a href="{{ route('ads.myAds') }}" class="hover:text-brand hover:underline">{{ __('ui.footer.my_ads') }}</a></li>
                            <li><a href="{{ route('ads.create') }}" class="hover:text-brand hover:underline">{{ __('ui.footer.post_ad') }}</a></li>
                            <li>
                                <form action="{{ route('logout') }}" method="POST" class="inline">
                                    @csrf
                                    <button class="hover:text-brand hover:underline text-left">{{ __('ui.footer.sign_out') }}</button>
                                </form>
                            </li>
                        @else
                            <li><a href="{{ route('login') }}"    class="hover:text-brand hover:underline">{{ __('ui.footer.sign_in') }}</a></li>
                            <li><a href="{{ route('register') }}" class="hover:text-brand hover:underline">{{ __('ui.footer.create_account') }}</a></li>
                            <li><a href="{{ route('ads.create') }}" class="hover:text-brand hover:underline">{{ __('ui.footer.post_ad') }}</a></li>
                        @endauth
                    </ul>
                </div>
            </div>

            <div class="border-t pt-5 flex flex-col sm:flex-row items-center justify-between gap-2 text-xs text-gray-400">
                <span>{{ __('ui.common.copyright', ['year' => date('Y')]) }}</span>
                <span>{!! __('ui.footer.built_with', ['framework' => '<a href="https://laravel.com" target="_blank" rel="noopener" class="hover:text-brand">Laravel</a>']) !!}</span>
            </div>
        </div>
    </footer>
    @stack('scripts')
</body>
</html>
