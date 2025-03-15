<?php

use App\Models\NewsArticle;
use App\Models\Category;
use App\Models\Author;
use App\Models\Source;
use App\Services\NewsAggregators\Enum\NewsAggregatorTypeEnum;
use App\Services\NewsAggregators\Repositories\NewsArticleRepository;

test('it creates a news article from NEWS_API data', function () {
    $repository = new NewsArticleRepository();

    $attributes = [
        'source' => ['name' => 'Example News'],
        'title' => 'Test News Title',
        'description' => 'Test Description',
        'publishedAt' => now()->toISOString(),
        'urlToImage' => 'https://example.com/image.jpg',
        'content' => 'Test Content',
    ];

    $repository->create($attributes, NewsAggregatorTypeEnum::NEWS_API);

    $this->assertDatabaseHas('news_articles', [
        'title' => 'Test News Title',
        'description' => 'Test Description',
        'api_source' => NewsAggregatorTypeEnum::NEWS_API->value,
    ]);
});

test('it creates a news article from The Guardian data', function () {
    $repository = new NewsArticleRepository();

    $attributes = [
        'sectionName' => 'Technology',
        'fields' => [
            'byline' => 'John Doe',
            'trailText' => 'Sample Guardian Description',
            'thumbnail' => 'https://example.com/guardian.jpg',
            'bodyText' => 'Guardian full content here.',
        ],
        'webTitle' => 'Guardian News Title',
        'webPublicationDate' => now()->toISOString(),
    ];

    $repository->create($attributes, NewsAggregatorTypeEnum::THE_GUARDIAN);

    $this->assertDatabaseHas('news_articles', [
        'title' => 'Guardian News Title',
        'description' => 'Sample Guardian Description',
        'api_source' => NewsAggregatorTypeEnum::THE_GUARDIAN->value,
    ]);
});

test('it creates a news article from The New York Times data', function () {
    $repository = new NewsArticleRepository();

    $attributes = [
        'section' => 'World',
        'subsection' => 'Politics',
        'byline' => 'Jane Smith',
        'published_date' => now()->toISOString(),
        'multimedia' => [['url' => 'https://example.com/nyt.jpg']],
        'abstract' => 'NYT abstract content.',
    ];

    $repository->create($attributes, NewsAggregatorTypeEnum::NEW_YORK_TIMES);

    $this->assertDatabaseHas('news_articles', [
        'content' => 'NYT abstract content.',
        'api_source' => NewsAggregatorTypeEnum::NEW_YORK_TIMES->value,
    ]);
});