@extends('layouts.app')
@section('title', 'Contact — Merkei Mart')

@section('content')
<div class="max-w-4xl mx-auto">

    <div class="text-center mb-10">
        <h1 class="text-3xl font-bold">Contact Us</h1>
        <p class="text-gray-500 mt-2">Have a question or feedback? We'd love to hear from you.</p>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

        {{-- Contact details --}}
        <aside class="space-y-5">
            @foreach([
                ['icon' => '📧', 'label' => 'Email',    'value' => 'hello@merkeimart.lk',   'href' => 'mailto:hello@merkeimart.lk'],
                ['icon' => '📞', 'label' => 'Phone',    'value' => '+94 11 234 5678',         'href' => 'tel:+94112345678'],
                ['icon' => '📍', 'label' => 'Address',  'value' => 'Colombo, Sri Lanka',      'href' => null],
                ['icon' => '🕐', 'label' => 'Hours',    'value' => 'Mon–Fri, 9 am – 6 pm',   'href' => null],
            ] as $item)
                <div class="bg-white rounded-xl shadow p-5 flex gap-4">
                    <span class="text-2xl flex-shrink-0">{{ $item['icon'] }}</span>
                    <div>
                        <p class="text-xs text-gray-400 uppercase font-medium">{{ $item['label'] }}</p>
                        @if($item['href'])
                            <a href="{{ $item['href'] }}" class="text-brand hover:underline font-medium">{{ $item['value'] }}</a>
                        @else
                            <p class="font-medium text-gray-700">{{ $item['value'] }}</p>
                        @endif
                    </div>
                </div>
            @endforeach

            <div class="bg-brand/5 border border-brand/20 rounded-xl p-5">
                <p class="text-sm text-gray-600 font-medium mb-1">Reporting an ad?</p>
                <p class="text-xs text-gray-500">
                    Use the report button on the listing page. We review all reports within 24 hours.
                </p>
            </div>
        </aside>

        {{-- Contact form --}}
        <div class="lg:col-span-2 bg-white rounded-xl shadow p-8">

            @if(session('contact_success'))
                <div class="bg-green-50 border border-green-200 text-green-800 rounded-lg p-5 text-center">
                    <p class="text-2xl mb-2">✅</p>
                    <p class="font-semibold">Message sent!</p>
                    <p class="text-sm mt-1">Thanks for reaching out. We'll get back to you within one business day.</p>
                </div>
            @else
                @if($errors->any())
                    <div class="bg-red-50 border border-red-200 text-red-700 rounded-lg p-4 mb-6 text-sm">
                        <ul class="list-disc list-inside space-y-1">
                            @foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach
                        </ul>
                    </div>
                @endif

                <form action="{{ route('contact.send') }}" method="POST" class="space-y-5">
                    @csrf

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                        <div>
                            <label class="block text-sm font-medium mb-1" for="name">
                                Name <span class="text-red-500">*</span>
                            </label>
                            <input id="name" type="text" name="name" value="{{ old('name', auth()->user()?->name) }}"
                                   required
                                   class="w-full border rounded-lg px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-brand/40
                                          @error('name') border-red-400 @enderror"
                                   placeholder="Your name">
                        </div>
                        <div>
                            <label class="block text-sm font-medium mb-1" for="email">
                                Email <span class="text-red-500">*</span>
                            </label>
                            <input id="email" type="email" name="email" value="{{ old('email', auth()->user()?->email) }}"
                                   required
                                   class="w-full border rounded-lg px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-brand/40
                                          @error('email') border-red-400 @enderror"
                                   placeholder="you@example.com">
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium mb-1" for="subject">
                            Subject <span class="text-red-500">*</span>
                        </label>
                        <input id="subject" type="text" name="subject" value="{{ old('subject') }}" required
                               class="w-full border rounded-lg px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-brand/40
                                      @error('subject') border-red-400 @enderror"
                               placeholder="How can we help?">
                    </div>

                    <div>
                        <label class="block text-sm font-medium mb-1" for="message">
                            Message <span class="text-red-500">*</span>
                        </label>
                        <textarea id="message" name="message" rows="6" required
                                  class="w-full border rounded-lg px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-brand/40
                                         @error('message') border-red-400 @enderror"
                                  placeholder="Tell us more…">{{ old('message') }}</textarea>
                        <p class="text-xs text-gray-400 mt-1">Min 10 characters.</p>
                    </div>

                    <button type="submit"
                            class="w-full bg-brand text-white py-2.5 rounded-lg font-semibold hover:bg-brand-light transition">
                        Send Message
                    </button>
                </form>
            @endif
        </div>
    </div>
</div>
@endsection
