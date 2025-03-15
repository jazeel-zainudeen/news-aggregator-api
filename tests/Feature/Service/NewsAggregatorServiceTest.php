<?php

use App\Services\NewsAggregators\NewsAggregatorService;
use App\Services\NewsAggregators\Enum\NewsAggregatorTypeEnum;
use App\Services\NewsAggregators\Exception\NewsAggregatorException;
use App\Services\NewsAggregators\NewsAggregatorFactory;

beforeEach(function () {
    $this->newsAggregatorService = new NewsAggregatorService();
});

it('throws an exception for an invalid news aggregator type', function () {
    expect(fn () => $this->newsAggregatorService->fetchNewsArticles('invalid_type'))
        ->toThrow(NewsAggregatorException::class);
});

// it('fetches news articles for a valid aggregator type', function () {
//     $mockAggregator = Mockery::mock();
//     $mockAggregator->shouldReceive('fetch')->once()->andReturn([
//         ['title' => 'Sample News', 'content' => 'This is a test article.']
//     ]);

//     $newsAggregatorType = NewsAggregatorTypeEnum::tryFrom('valid_type');
    
//     Mockery::mock('alias:' . NewsAggregatorFactory::class)
//         ->shouldReceive('make')
//         ->with($newsAggregatorType)
//         ->andReturn($mockAggregator);

//     $articles = $this->newsAggregatorService->fetchNewsArticles('valid_type');
    
//     expect($articles)->toBeArray()
//         ->and($articles)->toHaveCount(1)
//         ->and($articles[0])->toHaveKeys(['title', 'content']);
// });