<?php

namespace App\Http\Controllers;

use App\Http\Requests\ShortenUrlRequest;
use Illuminate\Http\Request;
use App\Services\UrlService\UrlServiceInterface;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class UrlController extends Controller
{
    protected UrlServiceInterface $urlService;

    public function __construct(UrlServiceInterface $urlService)
    {
        $this->urlService = $urlService;
    }

    /**
     * Display the URL shortening page.
     */
    public function index(): View
    {
        return view('shorten');
    }

    /**
     * Handle URL shortening request.
     */
    public function store(ShortenUrlRequest $request): RedirectResponse
    {
        $longUrl = $request->validated()['long_url'];
        if (!$this->urlService->isSafeUrl($longUrl)) {
            return redirect()->back()->withErrors(['long_url' => 'The URL is flagged as unsafe by Google Safe Browsing.']);
        }

        $url = $this->urlService->createShortUrl($longUrl);
        return redirect()->route('shortened', ['shortCode' => $url->short_code]);
    }

    /**
     * Display the shortened URL page.
     */
    public function show(string $shortCode): View
    {
        $url = $this->urlService->getLongUrl($shortCode);
        abort_unless($url, 404);

        return view('shortened', compact('url'));
    }

    /**
     * Redirect to the original URL and increment click count.
     */
    public function redirect(string $shortCode): RedirectResponse
    {
        $url = $this->urlService->getLongUrl($shortCode);
        if (!$url|| ($url->expires_at && now()->greaterThan($url->expires_at)))
        {
            return redirect('/')->with('error', 'This short URL has expired or invalid.');
        }

        $this->urlService->incrementClickCount($url);

        return redirect()->away($url->long_url);
    }
}
