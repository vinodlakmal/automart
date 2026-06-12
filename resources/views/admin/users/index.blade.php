@extends('admin.layouts.app')
@section('title', 'Users')
@section('heading', 'Manage Users')

@section('content')

{{-- Filters --}}
<form method="GET" action="{{ route('admin.users.index') }}"
      class="bg-white rounded-xl shadow p-4 mb-5 flex flex-wrap gap-3 items-end">

    <div class="flex-1 min-w-[200px]">
        <label class="block text-xs font-medium text-gray-500 mb-1">Search</label>
        <input type="text" name="q" value="{{ request('q') }}" placeholder="Name or email…"
               class="w-full border rounded px-3 py-2 text-sm focus:outline-none focus:ring-1 focus:ring-brand">
    </div>

    <div>
        <label class="block text-xs font-medium text-gray-500 mb-1">Status</label>
        <select name="status" class="border rounded px-3 py-2 text-sm bg-white">
            <option value="">All</option>
            <option value="active"   @selected(request('status') === 'active')>Active</option>
            <option value="inactive" @selected(request('status') === 'inactive')>Inactive</option>
        </select>
    </div>

    <button class="bg-brand text-white px-4 py-2 rounded text-sm hover:bg-brand-light transition">Filter</button>
    <a href="{{ route('admin.users.index') }}" class="text-sm text-gray-400 hover:underline py-2">Clear</a>
</form>

{{-- Table --}}
<div class="bg-white rounded-xl shadow overflow-hidden">
    <div class="px-5 py-3 border-b">
        <p class="text-sm text-gray-500">{{ number_format($users->total()) }} users</p>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 text-xs text-gray-500 uppercase tracking-wide">
                <tr>
                    <th class="text-left px-4 py-3 w-8">#</th>
                    <th class="text-left px-4 py-3">User</th>
                    <th class="text-left px-4 py-3">Phone</th>
                    <th class="text-left px-4 py-3">Ads</th>
                    <th class="text-left px-4 py-3">Joined</th>
                    <th class="text-left px-4 py-3">Status</th>
                    <th class="text-left px-4 py-3">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y">
                @forelse($users as $user)
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-3 text-gray-400 font-mono text-xs">{{ $user->id }}</td>
                        <td class="px-4 py-3">
                            <div class="flex items-center gap-2">
                                <div class="w-8 h-8 rounded-full bg-brand flex items-center justify-center text-white text-xs font-bold flex-shrink-0">
                                    {{ strtoupper(substr($user->name, 0, 1)) }}
                                </div>
                                <div class="min-w-0">
                                    <p class="font-medium truncate max-w-[160px]">{{ $user->name }}</p>
                                    <p class="text-xs text-gray-400 truncate max-w-[160px]">{{ $user->email }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-4 py-3 text-gray-500">{{ $user->phone ?? '—' }}</td>
                        <td class="px-4 py-3">
                            <a href="{{ route('admin.ads.index', ['q' => $user->name]) }}"
                               class="text-brand hover:underline font-semibold">{{ $user->ads_count }}</a>
                        </td>
                        <td class="px-4 py-3 text-gray-400 whitespace-nowrap">{{ $user->created_at->format('d M Y') }}</td>
                        <td class="px-4 py-3">
                            <div class="flex items-center gap-1.5 flex-wrap">
                                @if($user->is_active)
                                    <span class="text-xs bg-green-100 text-green-700 px-2 py-0.5 rounded font-semibold">Active</span>
                                @else
                                    <span class="text-xs bg-red-100 text-red-600 px-2 py-0.5 rounded font-semibold">Inactive</span>
                                @endif
                                @if($user->is_admin)
                                    <span class="text-xs bg-purple-100 text-purple-700 px-2 py-0.5 rounded font-semibold">Admin</span>
                                @endif
                            </div>
                        </td>
                        <td class="px-4 py-3">
                            <div class="flex gap-1">
                                @if($user->id !== auth()->id())
                                    <form action="{{ route('admin.users.toggleActive', $user) }}" method="POST">
                                        @csrf
                                        <button class="text-xs {{ $user->is_active ? 'bg-red-50 text-red-600 hover:bg-red-100' : 'bg-green-50 text-green-700 hover:bg-green-100' }} px-2 py-1 rounded transition">
                                            {{ $user->is_active ? 'Deactivate' : 'Activate' }}
                                        </button>
                                    </form>
                                    <form action="{{ route('admin.users.toggleAdmin', $user) }}" method="POST">
                                        @csrf
                                        <button class="text-xs {{ $user->is_admin ? 'bg-purple-50 text-purple-700 hover:bg-purple-100' : 'bg-gray-100 text-gray-600 hover:bg-gray-200' }} px-2 py-1 rounded transition">
                                            {{ $user->is_admin ? '↓ Admin' : '↑ Admin' }}
                                        </button>
                                    </form>
                                @else
                                    <span class="text-xs text-gray-300">You</span>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="7" class="px-4 py-10 text-center text-gray-400">No users found.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="px-5 py-4 border-t">{{ $users->links() }}</div>
</div>
@endsection
