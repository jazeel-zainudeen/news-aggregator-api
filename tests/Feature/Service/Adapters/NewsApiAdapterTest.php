<?php

use App\Services\NewsAggregators\Adapters\NewsApiAdapter;
use App\Services\NewsAggregators\Exception\NewsAggregatorException;
use App\Services\NewsAggregators\Repositories\NewsArticleRepository;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

beforeEach(function () {
    // Mock repository
    $this->newsArticleRepository = Mockery::mock(NewsArticleRepository::class);
    $this->newsApiAdapter = new NewsApiAdapter();
});

test('fetch method fetches and stores news articles successfully', function () {
    Config::set('services.news_api.key', 'test_api_key');
    Config::set('services.news_api.endpoint', 'https://newsapi.org/v2/');

    $categoriesCount = count(\App\Services\NewsAggregators\Enum\NewsApiCategoryEnum::cases());

    Http::fake([
        'https://newsapi.org/v2/top-headlines*' => Http::response([
            'status' => 'ok',
            'articles' => [
                ['title' => 'Sample News', 'description' => 'Sample Description'],
            ],
        ], 200),
    ]);

    DB::shouldReceive('transaction')->times($categoriesCount);

    $articles = $this->newsApiAdapter->fetch(['limit' => 10]);

    expect($articles)->toBeArray()->toHaveCount(1);
    expect($articles[0])->toHaveKeys(['title', 'description']);
});

test('fetch method throws exception when API call fails', function () {
    Config::set('services.news_api.key', 'test_api_key');
    Config::set('services.news_api.endpoint', 'https://newsapi.org/v2/');

    Http::fake([
        'https://newsapi.org/v2/top-headlines*' => Http::response(['status' => 'error'], 500),
    ]);

    $this->expectException(NewsAggregatorException::class);
    $this->expectExceptionMessage('Failed to fetch news articles from News API.');

    $this->newsApiAdapter->fetch(['limit' => 10]);
});

test('fetch method throws exception when API key or endpoint is missing', function () {
    Config::set('services.news_api.key', null);
    Config::set('services.news_api.endpoint', null);

    $this->expectException(NewsAggregatorException::class);
    $this->expectExceptionMessage('Missing configuration for News API.');

    $this->newsApiAdapter->fetch(['limit' => 10]);
});

test('fetch method logs error on failure', function () {
    Config::set('services.news_api.key', 'test_api_key');
    Config::set('services.news_api.endpoint', 'https://newsapi.org/v2/');

    Http::fake([
        'https://newsapi.org/v2/top-headlines*' => Http::response(['status' => 'error'], 500),
    ]);

    Log::shouldReceive('error')->once()->withArgs(function ($message, $context) {
        return str_contains($message, 'Failed to fetch news articles from NewsAPI');
    });

    try {
        $this->newsApiAdapter->fetch(['limit' => 10]);
    } catch (Exception $e) {
    }
});
