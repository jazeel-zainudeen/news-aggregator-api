<?php

namespace App\Services\NewsAggregators;

use App\Services\NewsAggregators\Adapters\NewsApiAdapter;
use App\Services\NewsAggregators\Adapters\NewYorkTimesAdapter;
use App\Services\NewsAggregators\Adapters\TheGuardianAdapter;
use App\Services\NewsAggregators\Contracts\NewsAggregatorInterface;
use App\Services\NewsAggregators\Enum\NewsAggregatorTypeEnum;
use App\Services\NewsAggregators\Exception\NewsAggregatorException;

class NewsAggregatorFactory
{
    /**
     * Creates and returns an instance of a news aggregator adapter based on the provided type.
     *
     * @param  NewsAggregatorTypeEnum  $type  The type of news aggregator to create.
     * @return NewsAggregatorInterface The corresponding news aggregator adapter instance.
     *
     * @throws NewsAggregatorException If an invalid aggregator type is provided.
     */
    public static function make(NewsAggregatorTypeEnum $type): NewsAggregatorInterface
    {
        return match ($type) {
            NewsAggregatorTypeEnum::NEWS_API => new NewsApiAdapter,
            NewsAggregatorTypeEnum::THE_GUARDIAN => new TheGuardianAdapter,
            NewsAggregatorTypeEnum::NEW_YORK_TIMES => new NewYorkTimesAdapter,
            default => throw NewsAggregatorException::invalidAggregatorType()
        };
    }
}
