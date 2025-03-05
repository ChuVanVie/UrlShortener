<?php

namespace Tests\Unit\Services;

use App\Models\Url;
use App\Repositories\UrlRepository\UrlRepositoryInterface;
use App\Services\UrlService\UrlService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Tests\TestCase;
use Mockery;

class UrlServiceTest extends TestCase
{
    use RefreshDatabase;

    protected UrlService $urlService;
    protected $urlRepositoryMock;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->urlRepositoryMock = Mockery::mock(UrlRepositoryInterface::class);

        $this->urlService = new UrlService($this->urlRepositoryMock);
    }

    public function test_createShortUrl_generates_unique_short_code()
    {
        $longUrl = 'https://example.com';
        
        $this->urlRepositoryMock
            ->shouldReceive('existsByShortCodeHash')
            ->andReturn(false);
        
        $this->urlRepositoryMock
            ->shouldReceive('store')
            ->once()
            ->andReturn(new Url([
                'short_code' => 'abcdef12',
                'short_code_hash' => hash_hmac('sha256', 'abcdef12', env('APP_KEY')),
                'long_url' => $longUrl,
                'expires_at' => now()->addDays(7),
            ]));

        $url = $this->urlService->createShortUrl($longUrl);

        $this->assertInstanceOf(Url::class, $url);
        $this->assertEquals(8, strlen($url->short_code));
    }

    public function test_getLongUrl_returns_correct_url()
    {
        $shortCode = 'abcdef12';
        $shortCodeHash = hash_hmac('sha256', $shortCode, env('APP_KEY'));
        $expectedUrl = new Url(['long_url' => 'https://example.com']);

        $this->urlRepositoryMock
            ->shouldReceive('findByShortCodeHash')
            ->with($shortCodeHash)
            ->once()
            ->andReturn($expectedUrl);

        $url = $this->urlService->getLongUrl($shortCode);

        $this->assertEquals('https://example.com', $url->long_url);
    }

    public function test_incrementClickCount_updates_clicks()
    {
        $url = new Url(['short_code_hash' => 'testhash123']);

        $this->urlRepositoryMock
            ->shouldReceive('incrementClicks')
            ->with($url->short_code_hash)
            ->once();

        $this->urlService->incrementClickCount($url);
    }

    public function test_isSafeUrl_returns_false_for_unsafe_url()
    {
        Http::fake([
            'https://safebrowsing.googleapis.com/v4/threatMatches:find*' => Http::response([
                'matches' => [
                    ['threatType' => 'MALWARE']
                ]
            ], 200),
        ]);

        $this->assertFalse($this->urlService->isSafeUrl('http://malicious-site.com'));
    }

    public function test_isSafeUrl_returns_true_for_safe_url()
    {
        Http::fake([
            'https://safebrowsing.googleapis.com/v4/threatMatches:find*' => Http::response([], 200),
        ]);

        $this->assertTrue($this->urlService->isSafeUrl('https://safe-site.com'));
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }
}
