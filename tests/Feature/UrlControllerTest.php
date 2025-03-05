<?php

namespace Tests\Feature;

use App\Http\Controllers\UrlController;
use App\Http\Requests\ShortenUrlRequest;
use App\Models\Url;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Services\UrlService\UrlServiceInterface;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Mockery;

class UrlControllerTest extends TestCase {
    protected UrlController $urlController;
    protected $urlServiceMock;

    protected function setUp(): void
    {
        parent::setUp();

        $this->urlServiceMock = Mockery::mock(UrlServiceInterface::class);

        $this->urlController = new UrlController($this->urlServiceMock);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    /** @test */
    public function it_displays_the_shorten_url_page()
    {
        $response = $this->urlController->index();
        
        $this->assertInstanceOf(View::class, $response);
        $this->assertEquals('shorten', $response->name());
    }

    /** @test */
    public function it_stores_a_short_url_successfully()
    {
        $longUrl = 'https://example.com';
        $shortCode = 'abc123';
        $mockUrl = new Url([
            'short_code' => $shortCode,
            'long_url' => $longUrl
        ]);

        $this->urlServiceMock
            ->shouldReceive('isSafeUrl')
            ->with($longUrl)
            ->once()
            ->andReturn(true);

        $this->urlServiceMock
            ->shouldReceive('createShortUrl')
            ->with($longUrl)
            ->once()
            ->andReturn($mockUrl);

        $requestMock = Mockery::mock(ShortenUrlRequest::class);
        $requestMock->shouldReceive('validated')->once()->andReturn(['long_url' => $longUrl]);

        $response = $this->urlController->store($requestMock);

        $this->assertInstanceOf(RedirectResponse::class, $response);
        $this->assertEquals(route('shortened', ['shortCode' => $shortCode]), $response->getTargetUrl());
    }

    /** @test */
    public function it_shows_error_if_url_is_unsafe()
    {
        $longUrl = 'https://unsafe-site.com';

        $this->urlServiceMock
            ->shouldReceive('isSafeUrl')
            ->with($longUrl)
            ->once()
            ->andReturn(false);

        $requestMock = Mockery::mock(ShortenUrlRequest::class);
        $requestMock->shouldReceive('validated')->once()->andReturn(['long_url' => $longUrl]);

        $response = $this->urlController->store($requestMock);

        $this->assertInstanceOf(RedirectResponse::class, $response);
        $this->assertArrayHasKey('errors', session()->all());
    }

    /** @test */
    public function it_redirects_to_original_url_and_increments_clicks()
    {
        $shortCode = 'abc123';
        $mockUrl = new Url([
            'short_code' => $shortCode,
            'long_url' => 'https://example.com'
        ]);

        $this->urlServiceMock
            ->shouldReceive('getLongUrl')
            ->with($shortCode)
            ->once()
            ->andReturn($mockUrl);

        $this->urlServiceMock
            ->shouldReceive('incrementClickCount')
            ->with($mockUrl)
            ->once();

        $response = $this->urlController->redirect($shortCode);

        $this->assertInstanceOf(RedirectResponse::class, $response);
        $this->assertEquals('https://example.com', $response->getTargetUrl());
    }

    /** @test */
    public function it_returns_404_if_url_not_found()
    {
        $shortCode = 'invalid123';

        $this->urlServiceMock
            ->shouldReceive('getLongUrl')
            ->with($shortCode)
            ->once()
            ->andReturn(null);

        $this->expectException(\Symfony\Component\HttpKernel\Exception\HttpException::class);

        $this->urlController->show($shortCode);
    }
}
