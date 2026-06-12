@extends('layouts.app')
@section('title', 'මගේ දැන්වීම් — ikman Clone')

@section('content')
<div class="flex items-center justify-between mb-6">
    <h1 class="text-2xl font-bold">මගේ දැන්වීම්</h1>
    <a href="{{ route('ads.create') }}" class="bg-brand text-white px-4 py-2 rounded hover:bg-brand-light">+ නව දැන්වීම</a>
</div>

@if($ads->isEmpty())
    <div class="bg-white rounded-lg shadow p-10 text-center text-gray-500">ඔබ තවම දැන්වීම් පළ කර නැත.</div>
@else
    <div class="bg-white rounded-lg shadow divide-y">
        @foreach($ads as $ad)
            <div class="flex items-center gap-4 p-4">
                <div class="w-20 h-20 bg-gray-200 rounded overflow-hidden flex-shrink-0">
                    @if($ad->primaryImage)<img src="{{ $ad->primaryImage->url }}" class="w-full h-full object-cover">@endif
                </div>
                <div class="flex-1 min-w-0">
                    <a href="{{ route('ads.show', $ad) }}" class="font-medium hover:underline line-clamp-1">{{ $ad->title }}</a>
                    <p class="text-sm text-gray-500">@if(!is_null($ad->price)) Rs. {{ number_format($ad->price) }} @endif · {{ number_format($ad->views) }} views</p>
                    <span class="inline-block text-xs px-2 py-0.5 rounded mt-1
                        {{ $ad->status==='active' ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-600' }}">{{ $ad->status }}</span>
                </div>
                <div class="flex gap-2">
                    <a href="{{ route('ads.edit', $ad) }}" class="px-3 py-1.5 text-sm rounded border hover:bg-gray-50">සංස්කරණය</a>
                    <form action="{{ route('ads.destroy', $ad) }}" method="POST" onsubmit="return confirm('ඉවත් කරන්නද?')">
                        @csrf @method('DELETE')
                        <button class="px-3 py-1.5 text-sm rounded bg-red-600 text-white hover:bg-red-700">ඉවත් කරන්න</button>
                    </form>
                </div>
            </div>
        @endforeach
    </div>
    <div class="mt-6">{{ $ads->links() }}</div>
@endif
@endsection
