<?php

use App\Models\Simulation;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;

uses(RefreshDatabase::class);

/**
 * Helper: create a published simulation and set up its extract directory.
 * Returns ['simulation', 'extractPath'] for cleanup.
 */
function createSimulationWithExtractDir(array $overrides = []): array
{
    $user = User::factory()->create(['role' => 'creator']);

    $simulation = Simulation::create(array_merge([
        'user_id' => $user->id,
        'title' => 'Tailwind Test Simulation',
        'slug' => 'tailwind-test-'.Str::random(5),
        'description' => 'A test simulation',
        'category' => 'education',
        'status' => 'published',
        'is_published' => true,
        'published_at' => now(),
        'version' => '1.0.0',
        'zip_path' => 'simulations/0/test.zip',
    ], $overrides));

    // Derive extract path exactly as getExtractPath() does:
    // zip_path "simulations/0/test.zip" → dirname → "simulations/0" → + slug
    $extractDir = dirname($simulation->zip_path);
    $extractPath = Storage::disk('public')->path(rtrim($extractDir, '/\\').'/'.$simulation->slug);
    @mkdir($extractPath, 0755, true);

    return ['simulation' => $simulation, 'extractPath' => $extractPath];
}

// ─── Tailwind Proxy Route Tests ────────────────────────────────────────────────

it('returns tailwind proxy script with correct content type', function () {
    Http::fake([
        'cdn.tailwindcss.com' => Http::response('// tailwind script content', 200),
    ]);

    Cache::flush();

    $response = $this->get(route('simulations.tailwind-proxy'));

    $response->assertStatus(200);
    $response->assertHeader('Content-Type', 'application/javascript; charset=utf-8');
    $response->assertSee('tailwind script content');
});

it('caches tailwind proxy response for 24 hours', function () {
    Http::fake([
        'cdn.tailwindcss.com' => Http::response('// cached script', 200),
    ]);

    Cache::flush();

    // First request
    $this->get(route('simulations.tailwind-proxy'));

    // Second request should use cache
    Http::fake(); // Reset HTTP fake — shouldn't be called again
    $response = $this->get(route('simulations.tailwind-proxy'));

    $response->assertStatus(200);
    $response->assertSee('cached script');
});

it('returns fallback when tailwind CDN is unreachable', function () {
    Http::fake([
        'cdn.tailwindcss.com' => Http::response(null, 503),
    ]);

    Cache::flush();

    $response = $this->get(route('simulations.tailwind-proxy'));

    $response->assertStatus(200);
    $response->assertHeader('Content-Type', 'application/javascript; charset=utf-8');
    $response->assertSee('Tailwind CDN temporarily unavailable');
    $response->assertSee('fallback active');
});

it('sets CORS and nosniff headers on proxy response', function () {
    Http::fake([
        'cdn.tailwindcss.com' => Http::response('// script', 200),
    ]);

    Cache::flush();

    $response = $this->get(route('simulations.tailwind-proxy'));

    $response->assertStatus(200);
    $response->assertHeader('Access-Control-Allow-Origin', '*');
    $response->assertHeader('X-Content-Type-Options', 'nosniff');
});

// ─── CDN Rewrite in Serve Method Tests ─────────────────────────────────────────

it('rewrites tailwind CDN URL in HTML files to proxy route', function () {
    ['simulation' => $simulation, 'extractPath' => $extractPath] = createSimulationWithExtractDir();

    $htmlContent = '<!DOCTYPE html>
<html>
<head>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>body { margin: 0; }</style>
</head>
<body class="bg-white text-gray-900">
    <h1 class="text-2xl font-bold">Hello World</h1>
</body>
</html>';

    file_put_contents($extractPath.'/index.html', $htmlContent);

    $response = $this->get(route('simulations.serve', [
        'slug' => $simulation->slug,
        'path' => 'index.html',
    ]));

    $response->assertStatus(200);
    $response->assertHeader('Content-Type', 'text/html; charset=UTF-8');

    $proxyUrl = route('simulations.tailwind-proxy');
    $response->assertSee($proxyUrl);
    $response->assertDontSee('cdn.tailwindcss.com');

    // Cleanup
    @unlink($extractPath.'/index.html');
    @rmdir($extractPath);
});

it('rewrites http CDN URLs to proxy as well', function () {
    ['simulation' => $simulation, 'extractPath' => $extractPath] = createSimulationWithExtractDir([
        'slug' => 'http-tailwind-'.Str::random(5),
    ]);

    $htmlContent = '<html><head>
        <script src="http://cdn.tailwindcss.com"></script>
    </head><body></body></html>';

    file_put_contents($extractPath.'/index.html', $htmlContent);

    $response = $this->get(route('simulations.serve', [
        'slug' => $simulation->slug,
        'path' => 'index.html',
    ]));

    $response->assertStatus(200);
    $proxyUrl = route('simulations.tailwind-proxy');
    $response->assertSee($proxyUrl);
    $response->assertDontSee('cdn.tailwindcss.com');

    // Cleanup
    @unlink($extractPath.'/index.html');
    @rmdir($extractPath);
});

it('does not rewrite non-HTML files like CSS', function () {
    ['simulation' => $simulation, 'extractPath' => $extractPath] = createSimulationWithExtractDir([
        'slug' => 'css-only-'.Str::random(5),
    ]);

    $cssContent = '.bg-white { background-color: white; } .text-bold { font-weight: bold; }';
    file_put_contents($extractPath.'/styles.css', $cssContent);

    $response = $this->get(route('simulations.serve', [
        'slug' => $simulation->slug,
        'path' => 'styles.css',
    ]));

    $response->assertStatus(200);
    $response->assertHeader('Content-Type', 'text/css; charset=utf-8');
    $response->assertDontSee(route('simulations.tailwind-proxy'));

    // Cleanup
    @unlink($extractPath.'/styles.css');
    @rmdir($extractPath);
});

it('HTML files without CDN are served normally via response file', function () {
    ['simulation' => $simulation, 'extractPath' => $extractPath] = createSimulationWithExtractDir([
        'slug' => 'plain-html-'.Str::random(5),
    ]);

    $htmlContent = '<html><body><h1>Plain HTML</h1></body></html>';
    file_put_contents($extractPath.'/index.html', $htmlContent);

    $response = $this->get(route('simulations.serve', [
        'slug' => $simulation->slug,
        'path' => 'index.html',
    ]));

    $response->assertStatus(200);
    $response->assertHeader('Content-Type', 'text/html; charset=UTF-8');

    // Should NOT contain proxy URL since no CDN reference
    $response->assertDontSee(route('simulations.tailwind-proxy'));

    // Cleanup
    @unlink($extractPath.'/index.html');
    @rmdir($extractPath);
});

it('show page template contains sandbox iframe for CSS isolation', function () {
    $templatePath = resource_path('views/simulations/show.blade.php');
    $contents = file_get_contents($templatePath);

    // Verify the iframe has sandbox attribute for CSS isolation
    $this->assertStringContainsString('simulation-iframe', $contents);
    $this->assertStringContainsString('sandbox=', $contents);
    $this->assertStringContainsString('allow-scripts', $contents);
    $this->assertStringContainsString('allow-same-origin', $contents);
});

it('tailwind proxy route is registered and accessible', function () {
    $this->assertTrue(
        Route::has('simulations.tailwind-proxy'),
        'Route simulations.tailwind-proxy should be registered'
    );
});

it('tailwind proxy route URL contains .js extension', function () {
    $url = route('simulations.tailwind-proxy');
    $this->assertStringContainsString('tailwind-proxy.js', $url);
});
