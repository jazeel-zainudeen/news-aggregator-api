<?php

use App\Services\NewsAggregators\Adapters\TheGuardianAdapter;
use App\Services\NewsAggregators\Enum\NewsAggregatorTypeEnum;
use App\Services\NewsAggregators\Exception\NewsAggregatorException;
use App\Services\NewsAggregators\Repositories\NewsArticleRepository;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

beforeEach(function () {
    $this->newsArticleRepository = Mockery::mock(NewsArticleRepository::class);
    $this->guardianAdapter = new TheGuardianAdapter();
});

test('fetch method fetches and stores news articles successfully', function () {
    Config::set('services.the_guardian.key', 'test_api_key');
    Config::set('services.the_guardian.endpoint', 'https://content.guardianapis.com/');

    Http::fake([
        'https://content.guardianapis.com/search*' => Http::response([
            'response' => [
                'status' => 'ok',
                'results' => [
                    ['webTitle' => 'Guardian News', 'trailText' => 'Guardian Description'],
                ],
            ],
        ], 200),
    ]);

    $this->newsArticleRepository
        ->shouldReceive('create')
        ->with(Mockery::any(), NewsAggregatorTypeEnum::THE_GUARDIAN);

    $articles = $this->guardianAdapter->fetch([]);

    expect($articles)->toBeArray()->toHaveCount(1);
    expect($articles[0])->toHaveKeys(['webTitle', 'trailText']);
});

test('fetch method throws exception when API call fails', function () {
    Config::set('services.the_guardian.key', 'test_api_key');
    Config::set('services.the_guardian.endpoint', 'https://content.guardianapis.com/');

    Http::fake([
        'https://content.guardianapis.com/search*' => Http::response(['response' => ['status' => 'error']], 500),
    ]);

    $this->expectException(NewsAggregatorException::class);
    $this->expectExceptionMessage('Failed to fetch news articles from The Guardian');

    $this->guardianAdapter->fetch([]);
});

test('fetch method throws exception when API key or endpoint is missing', function () {
    Config::set('services.the_guardian.key', null);
    Config::set('services.the_guardian.endpoint', null);

    $this->expectException(NewsAggregatorException::class);
    $this->expectExceptionMessage('Missing configuration for The Guardian');

    $this->guardianAdapter->fetch([]);
});

test('fetch method logs error on failure', function () {
    Config::set('services.the_guardian.key', 'test_api_key');
    Config::set('services.the_guardian.endpoint', 'https://content.guardianapis.com/');

    Http::fake([
        'https://content.guardianapis.com/search*' => Http::response(['response' => ['status' => 'error']], 500),
    ]);

    Log::shouldReceive('error')->once()->withArgs(function ($message, $context) {
        return str_contains($message, 'Failed to fetch news articles from The Guardian');
    });

    try {
        $this->guardianAdapter->fetch([]);
    } catch (Exception $e) {
    }
});
