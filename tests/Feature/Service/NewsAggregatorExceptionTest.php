<?php

use App\Services\NewsAggregators\Exception\NewsAggregatorException;
use App\Services\NewsAggregators\Enum\NewsAggregatorTypeEnum;

it('throws an exception for invalid aggregator type', function () {
    expect(fn () => throw NewsAggregatorException::invalidAggregatorType())
        ->toThrow(NewsAggregatorException::class, __('Invalid aggregator type'));
});

it('throws an exception when fetching fails for each aggregator type', function () {
    foreach (NewsAggregatorTypeEnum::cases() as $aggregator) {
        $expectedMessage = __("Failed to fetch news articles from " . NewsAggregatorTypeEnum::getTitle($aggregator) . ".");
        
        expect(fn () => throw NewsAggregatorException::failedToFetch($aggregator))
            ->toThrow(NewsAggregatorException::class, $expectedMessage);
    }
});

it('throws an exception for missing configuration for each aggregator type', function () {
    foreach (NewsAggregatorTypeEnum::cases() as $aggregator) {
        $expectedMessage = __("Missing configuration for " . NewsAggregatorTypeEnum::getTitle($aggregator) . ".");
        
        expect(fn () => throw NewsAggregatorException::missingConfiguration($aggregator))
            ->toThrow(NewsAggregatorException::class, $expectedMessage);
    }
});
