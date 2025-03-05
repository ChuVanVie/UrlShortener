<?php
namespace App\Services\UrlService;

use App\Services\UrlService\UrlServiceInterface;
use App\Repositories\UrlRepository\UrlRepositoryInterface;
use Illuminate\Support\Str;
use App\Models\Url;
use Illuminate\Support\Facades\Http;

class UrlService implements UrlServiceInterface
{
    protected UrlRepositoryInterface $urlRepository;

    public function __construct(UrlRepositoryInterface $urlRepository)
    {
        $this->urlRepository = $urlRepository;
    }

    public function getLongUrl(string $shortCode): ?Url
    {
        $secretKey = config('app.key');
        $shortCodeHash = hash_hmac('sha256', $shortCode, $secretKey);
        return $this->urlRepository->findByShortCodeHash($shortCodeHash);
    }

    public function createShortUrl(string $longUrl): Url
    {
        do {
            $shortCode = Str::random(8);
            $shortCodeHash = hash_hmac('sha256', $shortCode, config('app.key'));
        } while ($this->urlRepository->existsByShortCodeHash($shortCodeHash));

        return $this->urlRepository->store([
            'short_code' => $shortCode,
            'short_code_hash' => $shortCodeHash,
            'long_url' => $longUrl,
            'expires_at' => now()->addDays(7),
        ]);
    }

    public function incrementClickCount(Url $url)
    {
        return $this->urlRepository->incrementClicks($url->short_code_hash);
    }

    public function isSafeUrl(string $url): bool
    {
        $apiKey = env('GOOGLE_SAFE_BROWSING_API_KEY');
        $endpoint = "https://safebrowsing.googleapis.com/v4/threatMatches:find?key={$apiKey}";

        $data = [
            "client" => ["clientId" => "your-app", "clientVersion" => "1.0"],
            "threatInfo" => [
                "threatTypes" => ["MALWARE", "SOCIAL_ENGINEERING"],
                "platformTypes" => ["ANY_PLATFORM"],
                "threatEntryTypes" => ["URL"],
                "threatEntries" => [["url" => $url]],
            ],
        ];

        $response = Http::post($endpoint, $data);
        return empty($response->json()['matches']);
    }
}
