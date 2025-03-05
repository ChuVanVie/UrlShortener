@extends('layouts.app')

@section('content')
<div class="container mx-auto max-w-lg p-8">
    <h1 class="text-2xl font-bold text-center mb-4">Short URL</h1>

    <form action="{{ route('shorten') }}" method="POST" class="bg-white p-6 rounded-lg shadow">
        @csrf

        <label for="long_url" class="block mb-2 font-medium">Enter your URL:</label>

        {{-- Display Safe Browsing API error --}}
        @if ($errors->has('long_url'))
            <p class="text-red-500 text-sm mb-2">{{ $errors->first('long_url') }}</p>
        @endif

        <input type="url" id="long_url" name="long_url" 
               class="w-full p-2 border rounded mb-2 @error('long_url') border-red-500 @enderror" 
               value="{{ old('long_url') }}" 
               required 
               aria-label="Enter your URL">

        <button type="submit" class="w-full bg-blue-500 text-white p-2 rounded hover:bg-blue-600 transition">
            Shorten URL
        </button>
    </form>
</div>
@endsection