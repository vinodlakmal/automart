@extends('admin.layouts.app')
@section('title', 'Reports')
@section('heading', 'Manage Reports')

@section('content')

{{-- Filter tabs --}}
<div class="flex gap-2 mb-5">
    @foreach([''=>'All', 'open'=>'Open', 'reviewed'=>'Reviewed', 'dismissed'=>'Dismissed'] as $val => $label)
        <a href="{{ route('admin.reports.index', $val ? ['status' => $val] : []) }}"
           class="px-4 py-2 rounded-lg text-sm font-medium transition
                  {{ request('status', '') === $val ? 'bg-brand text-white' : 'bg-white shadow text-gray-600 hover:bg-gray-50' }}">
            {{ $label }}
        </a>
    @endforeach
</div>

<div class="bg-white rounded-xl shadow overflow-hidden">
    <div class="px-5 py-3 border-b">
        <p class="text-sm text-gray-500">{{ number_format($reports->total()) }} reports</p>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 text-xs text-gray-500 uppercase tracking-wide">
                <tr>
                    <th class="text-left px-4 py-3 w-8">#</th>
                    <th class="text-left px-4 py-3">Ad</th>
                    <th class="text-left px-4 py-3">Reason</th>
                    <th class="text-left px-4 py-3">Reporter</th>
                    <th class="text-left px-4 py-3">Date</th>
                    <th class="text-left px-4 py-3">Status</th>
                    <th class="text-left px-4 py-3">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y">
                @forelse($reports as $report)
                    @php
                        $statusClass = match($report->status) {
                            'open'      => 'bg-red-100 text-red-700',
                            'reviewed'  => 'bg-blue-100 text-blue-700',
                            'dismissed' => 'bg-gray-100 text-gray-500',
                            default     => 'bg-gray-100 text-gray-500',
                        };
                    @endphp
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-3 text-gray-400 font-mono text-xs">{{ $report->id }}</td>
                        <td class="px-4 py-3 max-w-[220px]">
                            @if($report->ad)
                                <a href="{{ route('ads.show', $report->ad) }}" target="_blank"
                                   class="text-brand hover:underline line-clamp-1 font-medium">{{ $report->ad->title }}</a>
                            @else
                                <span class="text-gray-400 italic">Ad deleted</span>
                            @endif
                        </td>
                        <td class="px-4 py-3">
                            <p class="font-medium capitalize">{{ str_replace('_', ' ', $report->reason) }}</p>
                            @if($report->details)
                                <p class="text-xs text-gray-400 mt-0.5 line-clamp-1">{{ $report->details }}</p>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-gray-500">{{ $report->user?->name ?? 'Guest' }}</td>
                        <td class="px-4 py-3 text-gray-400 whitespace-nowrap">{{ $report->created_at->format('d M Y') }}</td>
                        <td class="px-4 py-3">
                            <span class="text-xs font-semibold px-2 py-0.5 rounded {{ $statusClass }}">
                                {{ ucfirst($report->status) }}
                            </span>
                        </td>
                        <td class="px-4 py-3">
                            @if($report->status === 'open')
                                <form action="{{ route('admin.reports.resolve', $report) }}" method="POST" class="flex gap-1">
                                    @csrf
                                    <button name="resolution" value="reviewed"
                                            class="text-xs bg-blue-100 text-blue-700 hover:bg-blue-200 px-2 py-1 rounded transition">
                                        ✓ Reviewed
                                    </button>
                                    <button name="resolution" value="dismissed"
                                            class="text-xs bg-gray-100 text-gray-600 hover:bg-gray-200 px-2 py-1 rounded transition">
                                        ✗ Dismiss
                                    </button>
                                </form>
                            @else
                                <span class="text-xs text-gray-300">Resolved</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="7" class="px-4 py-10 text-center text-gray-400">No reports found.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="px-5 py-4 border-t">{{ $reports->links() }}</div>
</div>
@endsection
