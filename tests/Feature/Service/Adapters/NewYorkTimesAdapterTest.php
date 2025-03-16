<?php

use App\Services\NewsAggregators\Adapters\NewYorkTimesAdapter;
use App\Services\NewsAggregators\Enum\NewsAggregatorTypeEnum;
use App\Services\NewsAggregators\Exception\NewsAggregatorException;
use App\Services\NewsAggregators\Repositories\NewsArticleRepository;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

beforeEach(function () {
    $this->newsArticleRepository = Mockery::mock(NewsArticleRepository::class);
    $this->nytAdapter = new NewYorkTimesAdapter();
});

test('fetch method fetches and stores news articles successfully', function () {
    Config::set('services.the_new_york_times.key', 'test_api_key');
    Config::set('services.the_new_york_times.endpoint', 'https://api.nytimes.com/svc/');

    Http::fake([
        'https://api.nytimes.com/svc/topstories/v2/home.json*' => Http::response([
            'status' => 'OK',
            'results' => [
                ['title' => 'NYT News', 'abstract' => 'NYT Description'],
            ],
        ], 200),
    ]);

    DB::shouldReceive('transaction')->once();

    $articles = $this->nytAdapter->fetch([]);

    expect($articles)->toBeArray()->toHaveCount(1);
    expect($articles[0])->toHaveKeys(['title', 'abstract']);
});

test('fetch method throws exception when API call fails', function () {
    Config::set('services.the_new_york_times.key', 'test_api_key');
    Config::set('services.the_new_york_times.endpoint', 'https://api.nytimes.com/svc/');

    Http::fake([
        'https://api.nytimes.com/svc/topstories/v2/home.json*' => Http::response(['status' => 'error'], 500),
    ]);

    $this->expectException(NewsAggregatorException::class);
    $this->expectExceptionMessage('Failed to fetch news articles from The New York Times');

    $this->nytAdapter->fetch([]);
});

test('fetch method throws exception when API key or endpoint is missing', function () {
    Config::set('services.the_new_york_times.key', null);
    Config::set('services.the_new_york_times.endpoint', null);

    $this->expectException(NewsAggregatorException::class);
    $this->expectExceptionMessage('Missing configuration for The New York Times');

    $this->nytAdapter->fetch([]);
});


test('fetch method logs error on failure', function () {
    Config::set('services.the_new_york_times.key', 'test_api_key');
    Config::set('services.the_new_york_times.endpoint', 'https://api.nytimes.com/svc/');

    Http::fake([
        'https://api.nytimes.com/svc/topstories/v2/home.json*' => Http::response(['status' => 'error'], 500),
    ]);

    Log::shouldReceive('error')->once()->withArgs(function ($message, $context) {
        return str_contains($message, 'Failed to fetch news articles from NewsAPI');
    });

    try {
        $this->nytAdapter->fetch([]);
    } catch (Exception $e) {
    }
});
