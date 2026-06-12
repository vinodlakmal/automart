@extends('admin.layouts.app')
@section('title', 'Dashboard')
@section('heading', 'Dashboard')

@section('content')

{{-- Stats strip --}}
<div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-4 mb-8">
    @php
        $tiles = [
            ['label' => 'Total Users',    'value' => $stats['users'],        'color' => 'blue',   'icon' => '👥', 'href' => route('admin.users.index')],
            ['label' => 'Active Ads',     'value' => $stats['active_ads'],   'color' => 'green',  'icon' => '✅', 'href' => route('admin.ads.index', ['status' => 'active'])],
            ['label' => 'Pending Review', 'value' => $stats['pending_ads'],  'color' => 'yellow', 'icon' => '⏳', 'href' => route('admin.ads.index', ['status' => 'pending'])],
            ['label' => 'Open Reports',   'value' => $stats['open_reports'], 'color' => 'red',    'icon' => '🚩', 'href' => route('admin.reports.index', ['status' => 'open'])],
            ['label' => 'Total Ads',      'value' => $stats['total_ads'],    'color' => 'gray',   'icon' => '📋', 'href' => route('admin.ads.index')],
            ['label' => 'Categories',     'value' => $stats['categories'],   'color' => 'purple', 'icon' => '🏷️', 'href' => route('admin.categories.index')],
        ];
        $colorMap = [
            'blue'   => 'bg-blue-50 text-blue-700',
            'green'  => 'bg-green-50 text-green-700',
            'yellow' => 'bg-yellow-50 text-yellow-700',
            'red'    => 'bg-red-50 text-red-700',
            'gray'   => 'bg-gray-50 text-gray-700',
            'purple' => 'bg-purple-50 text-purple-700',
        ];
    @endphp
    @foreach($tiles as $tile)
        <a href="{{ $tile['href'] }}"
           class="bg-white rounded-xl shadow p-4 hover:shadow-md transition text-center">
            <p class="text-2xl">{{ $tile['icon'] }}</p>
            <p class="text-2xl font-bold mt-1 {{ explode(' ', $colorMap[$tile['color']])[1] }}">
                {{ number_format($tile['value']) }}
            </p>
            <p class="text-xs text-gray-500 mt-0.5">{{ $tile['label'] }}</p>
        </a>
    @endforeach
</div>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

    {{-- Pending ads --}}
    <div class="bg-white rounded-xl shadow">
        <div class="flex items-center justify-between px-5 py-4 border-b">
            <h2 class="font-semibold">⏳ Pending Ads</h2>
            <a href="{{ route('admin.ads.index', ['status' => 'pending']) }}"
               class="text-xs text-brand hover:underline">View all →</a>
        </div>
        @if($recentPending->isEmpty())
            <p class="text-sm text-gray-400 p-5">No ads pending review.</p>
        @else
            <div class="divide-y">
                @foreach($recentPending as $ad)
                    <div class="flex items-center gap-3 px-5 py-3">
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-medium truncate">{{ $ad->title }}</p>
                            <p class="text-xs text-gray-400">{{ $ad->user?->name }} · {{ $ad->category?->name }} · {{ $ad->created_at->diffForHumans() }}</p>
                        </div>
                        <div class="flex gap-1 flex-shrink-0">
                            <form action="{{ route('admin.ads.approve', $ad) }}" method="POST">
                                @csrf
                                <button class="text-xs bg-green-100 text-green-700 hover:bg-green-200 px-2 py-1 rounded transition">Approve</button>
                            </form>
                            <form action="{{ route('admin.ads.reject', $ad) }}" method="POST">
                                @csrf
                                <button class="text-xs bg-red-100 text-red-700 hover:bg-red-200 px-2 py-1 rounded transition">Reject</button>
                            </form>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>

    {{-- Recent users --}}
    <div class="bg-white rounded-xl shadow">
        <div class="flex items-center justify-between px-5 py-4 border-b">
            <h2 class="font-semibold">👥 Recent Users</h2>
            <a href="{{ route('admin.users.index') }}" class="text-xs text-brand hover:underline">View all →</a>
        </div>
        <div class="divide-y">
            @foreach($recentUsers as $user)
                <div class="flex items-center gap-3 px-5 py-3">
                    <div class="w-8 h-8 rounded-full bg-brand flex items-center justify-center text-white text-sm font-bold flex-shrink-0">
                        {{ strtoupper(substr($user->name, 0, 1)) }}
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-medium truncate">{{ $user->name }}</p>
                        <p class="text-xs text-gray-400 truncate">{{ $user->email }} · {{ $user->created_at->diffForHumans() }}</p>
                    </div>
                    @if($user->is_admin)
                        <span class="text-xs bg-purple-100 text-purple-700 px-2 py-0.5 rounded font-semibold">Admin</span>
                    @elseif(! $user->is_active)
                        <span class="text-xs bg-red-100 text-red-600 px-2 py-0.5 rounded">Inactive</span>
                    @endif
                </div>
            @endforeach
        </div>
    </div>

    {{-- Ads by status --}}
    <div class="bg-white rounded-xl shadow p-5">
        <h2 class="font-semibold mb-4">Ads by Status</h2>
        @php
            $statusColors = [
                'active'   => 'bg-green-500',
                'pending'  => 'bg-yellow-400',
                'sold'     => 'bg-blue-500',
                'expired'  => 'bg-orange-400',
                'rejected' => 'bg-red-500',
            ];
            $total = max($adsByStatus->sum(), 1);
        @endphp
        <div class="space-y-3">
            @foreach($adsByStatus as $status => $count)
                <div>
                    <div class="flex justify-between text-sm mb-1">
                        <span class="capitalize text-gray-600">{{ $status }}</span>
                        <span class="font-semibold">{{ number_format($count) }}</span>
                    </div>
                    <div class="w-full bg-gray-100 rounded-full h-2">
                        <div class="h-2 rounded-full {{ $statusColors[$status] ?? 'bg-gray-400' }}"
                             style="width: {{ round(($count / $total) * 100) }}%"></div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

</div>
@endsection
