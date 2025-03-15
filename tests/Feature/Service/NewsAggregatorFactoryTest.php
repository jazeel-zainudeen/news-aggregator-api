<?php

use App\Services\NewsAggregators\NewsAggregatorFactory;
use App\Services\NewsAggregators\Adapters\NewsApiAdapter;
use App\Services\NewsAggregators\Adapters\TheGuardianAdapter;
use App\Services\NewsAggregators\Adapters\NewYorkTimesAdapter;
use App\Services\NewsAggregators\Contracts\NewsAggregatorInterface;
use App\Services\NewsAggregators\Enum\NewsAggregatorTypeEnum;
use App\Services\NewsAggregators\Exception\NewsAggregatorException;

it('returns the correct aggregator adapter for each valid type', function () {
    $mapping = [
        NewsAggregatorTypeEnum::NEWS_API->value => NewsApiAdapter::class,
        NewsAggregatorTypeEnum::THE_GUARDIAN->value => TheGuardianAdapter::class,
        NewsAggregatorTypeEnum::NEW_YORK_TIMES->value => NewYorkTimesAdapter::class,
    ];

    foreach ($mapping as $type => $expectedClass) {
        $adapter = NewsAggregatorFactory::make(NewsAggregatorTypeEnum::tryFrom($type));
        
        expect($adapter)
            ->toBeInstanceOf(NewsAggregatorInterface::class)
            ->toBeInstanceOf($expectedClass);
    }
});

it('throws an exception for an invalid aggregator type', function () {
    expect(fn() => NewsAggregatorFactory::make(NewsAggregatorTypeEnum::tryFrom('invalid_type')))
        ->toThrow(NewsAggregatorException::class, __('Invalid aggregator type'));
});
