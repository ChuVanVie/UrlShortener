@extends('layouts.app')

@section('content')
<div class="container mx-auto max-w-lg p-8">
    <h1 class="text-2xl font-bold text-center mb-4">Your shortened URL</h1>
    <div class="bg-white p-6 rounded-lg shadow">
        <input type="text" value="{{ url($url->short_code) }}" id="shortUrl" class="w-full p-2 border rounded mb-2" readonly>

        <div class="relative">
            <button onclick="copyToClipboard()" class="w-full bg-blue-500 text-white p-2 rounded relative">
                Copy URL
            </button>
            <span id="tooltip" class="absolute top-1/2 right-0 ml-2 transform translate-x-full -translate-y-1/2 bg-gray-800 text-white text-xs px-2 py-1 rounded opacity-0 transition-opacity duration-300 whitespace-nowrap">
                Copied!
            </span>
        </div>

        <p class="mt-4">Long URL: <a href="{{ $url->long_url }}" class="text-blue-500 break-all">{{ $url->long_url }}</a></p>

        <a href="/" class="block text-center bg-gray-500 text-white p-2 rounded mt-4">Shorten another URL</a>
    </div>
</div>

<script>
    function copyToClipboard() {
        let copyText = document.getElementById("shortUrl");
        copyText.select();
        copyText.setSelectionRange(0, 99999); // For mobile devices
        document.execCommand("copy");

        let tooltip = document.getElementById("tooltip");
        tooltip.classList.remove("opacity-0");
        setTimeout(() => {
            tooltip.classList.add("opacity-0");
        }, 1500);
    }
</script>
@endsection
