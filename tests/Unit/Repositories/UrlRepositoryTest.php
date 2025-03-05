<?php

namespace Tests\Unit\Repositories;

use Tests\TestCase;
use App\Models\Url;
use App\Repositories\UrlRepository\UrlRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;

class UrlRepositoryTest extends TestCase
{
    use RefreshDatabase;

    protected UrlRepository $urlRepository;

    protected function setUp() : void
    {
        parent::setUp();
        $this->urlRepository = new UrlRepository(new Url());
    }

    /** @test */
    public function it_can_store_a_new_url()
    {
        $data = [
            'short_code' => 'test123',
            'short_code_hash' => 'testhash123',
            'long_url' => 'https://example.com',
            'expires_at' => now()->addDays(7),
        ];

        $url = $this->urlRepository->store($data);

        $this->assertInstanceOf(Url::class, $url);
        $this->assertDatabaseHas('urls', [
            'short_code' => 'test123',
            'short_code_hash' => 'testhash123',
        ]);
    }

    /** @test */
    public function it_can_find_a_url_by_short_code_hash()
    {
        $shortCode = Str::random(8);
        $shortCodeHash = hash_hmac('sha256', $shortCode, env('APP_KEY'));
        $url = Url::factory()->create([
            'short_code' => $shortCode,
            'short_code_hash' => $shortCodeHash,
        ]);

        $foundUrl = $this->urlRepository->findByShortCodeHash($shortCodeHash);

        $this->assertNotNull($foundUrl);
        $this->assertEquals($url->id, $foundUrl->id);
    }

    /** @test */
    public function it_returns_null_if_short_code_hash_not_found()
    {
        $foundUrl = $this->urlRepository->findByShortCodeHash('nonexistenthash');

        $this->assertNull($foundUrl);
    }

    /** @test */
    public function it_checks_if_a_short_code_hash_exists()
    {
        Url::factory()->create(['short_code_hash' => 'testhash123']);

        $this->assertTrue($this->urlRepository->existsByShortCodeHash('testhash123'));
        $this->assertFalse($this->urlRepository->existsByShortCodeHash('wronghash'));
    }

    /** @test */
    public function it_increments_clicks_correctly()
    {
        $url = Url::factory()->create(['short_code_hash' => 'testhash123', 'clicks' => 0]);
        $this->urlRepository->incrementClicks('testhash123');

        $this->assertEquals(1, $url->fresh()->clicks);
    }
}
