<?php

use App\Console\Commands\FetchNewsArticles;
use App\Services\NewsAggregators\Enum\NewsAggregatorTypeEnum;
use App\Services\NewsAggregators\NewsAggregatorService;

test('it fails when an invalid limit is provided', function () {
    $this->artisan('news:fetch', ['--limit' => -1])
        ->expectsOutput('Invalid limit provided. It must be a positive number.')
        ->assertExitCode(0);
});


test('it fetches news articles successfully', function () {
    $mockService = Mockery::mock(NewsAggregatorService::class);
    $this->app->instance(NewsAggregatorService::class, $mockService);

    $mockService->shouldReceive('fetchNewsArticles')
        ->andReturn([['title' => 'Sample News']]);

    $this->artisan('news:fetch', ['--limit' => 10])
        ->expectsOutputToContain('Starting news fetching process')
        ->expectsOutputToContain('✔ Successfully fetched')
        ->expectsOutput('✅ News fetching process completed successfully.')
        ->assertExitCode(0);
});


test('it handles exceptions when fetching news', function () {
    $mockService = Mockery::mock(NewsAggregatorService::class);
    $this->app->instance(NewsAggregatorService::class, $mockService);

    $mockService->shouldReceive('fetchNewsArticles')
        ->andThrow(new Exception('Service error'));

    $this->artisan('news:fetch', ['--limit' => 10])
        ->expectsOutputToContain('❌ Failed to fetch')
        ->assertExitCode(0);
});

test('it uses the default limit when no limit is provided', function () {
    $mockService = Mockery::mock(NewsAggregatorService::class);
    $this->app->instance(NewsAggregatorService::class, $mockService);

    $mockService->shouldReceive('fetchNewsArticles')
        ->withArgs(fn($aggregator, $options) => $options['limit'] === 100)
        ->andReturn([['title' => 'Default Limit News']]);

    $this->artisan('news:fetch')
        ->expectsOutputToContain('Starting news fetching process with a limit of 100')
        ->expectsOutputToContain('✔ Successfully fetched')
        ->assertExitCode(0);
});