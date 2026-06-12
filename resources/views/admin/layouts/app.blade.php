<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Admin') — Merkei Mart</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: { extend: { colors: { brand: { DEFAULT:'#1b5e20', light:'#2e7d32', dark:'#0d3d12' } } } }
        }
    </script>
</head>
<body class="bg-gray-100 text-gray-800 antialiased">

<div class="flex min-h-screen">

    {{-- ── Sidebar ── --}}
    <aside class="w-56 bg-gray-900 text-gray-200 flex flex-col flex-shrink-0">

        <div class="px-5 py-4 border-b border-gray-700">
            <a href="{{ route('admin.dashboard') }}" class="text-white font-bold text-lg leading-tight">
                Merkei Mart<br>
                <span class="text-xs font-normal text-gray-400 tracking-widest uppercase">Admin Panel</span>
            </a>
        </div>

        <nav class="flex-1 px-3 py-4 space-y-1 text-sm">
            @php
                $navItems = [
                    ['route' => 'admin.dashboard',        'label' => 'Dashboard',   'icon' => '📊'],
                    ['route' => 'admin.ads.index',        'label' => 'Ads',         'icon' => '📋'],
                    ['route' => 'admin.users.index',      'label' => 'Users',       'icon' => '👥'],
                    ['route' => 'admin.categories.index', 'label' => 'Categories',  'icon' => '🏷️'],
                    ['route' => 'admin.reports.index',    'label' => 'Reports',     'icon' => '🚩'],
                ];
            @endphp

            @foreach($navItems as $item)
                <a href="{{ route($item['route']) }}"
                   class="flex items-center gap-3 px-3 py-2.5 rounded-lg transition
                          {{ request()->routeIs($item['route']) ? 'bg-brand text-white' : 'hover:bg-gray-800 text-gray-300' }}">
                    <span>{{ $item['icon'] }}</span>
                    <span>{{ $item['label'] }}</span>
                </a>
            @endforeach
        </nav>

        <div class="px-3 py-4 border-t border-gray-700 space-y-1 text-sm">
            <a href="{{ route('home') }}" target="_blank"
               class="flex items-center gap-3 px-3 py-2 rounded-lg text-gray-400 hover:bg-gray-800 hover:text-white transition">
                <span>🌐</span><span>View Site</span>
            </a>
            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button type="submit"
                        class="w-full flex items-center gap-3 px-3 py-2 rounded-lg text-gray-400 hover:bg-gray-800 hover:text-white transition text-left">
                    <span>🚪</span><span>Sign Out</span>
                </button>
            </form>
        </div>
    </aside>

    {{-- ── Main ── --}}
    <div class="flex-1 flex flex-col min-w-0">

        {{-- Top bar --}}
        <header class="bg-white border-b px-6 py-3 flex items-center justify-between flex-shrink-0">
            <h1 class="font-semibold text-gray-700">@yield('heading', 'Admin Panel')</h1>
            <span class="text-sm text-gray-500">{{ auth()->user()->name }}</span>
        </header>

        {{-- Flash messages --}}
        @if(session('status') || session('error'))
            <div class="px-6 pt-4">
                @if(session('status'))
                    <div class="bg-green-50 border border-green-200 text-green-800 text-sm px-4 py-3 rounded-lg">
                        {{ session('status') }}
                    </div>
                @endif
                @if(session('error'))
                    <div class="bg-red-50 border border-red-200 text-red-800 text-sm px-4 py-3 rounded-lg">
                        {{ session('error') }}
                    </div>
                @endif
            </div>
        @endif

        <main class="flex-1 p-6">
            @yield('content')
        </main>
    </div>
</div>

@stack('scripts')
</body>
</html>
