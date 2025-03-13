<?php

namespace App\Services\NewsAggregators;

use App\Services\NewsAggregators\Exception\NewsAggregatorException;
use App\Services\NewsAggregators\Enum\NewsAggregatorTypeEnum;

class NewsAggregatorService
{
    /**
     * Fetch news articles from the specified news aggregator.
     *
     * @param string $type The type of news aggregator.
     * @param array<int|string, mixed> $attributes The attributes required to fetch news articles.
     * @return array<int, mixed> The fetched news articles.
     *
     * @throws NewsAggregatorException If the aggregator type is invalid.
     */
    public function fetchNewsArticles(string $type, array $attributes = []): void
    {
        $newsAggregatorType = NewsAggregatorTypeEnum::tryFrom($type);
        
        if ($newsAggregatorType === null) {
            throw NewsAggregatorException::invalidAggregatorType();
        }

        $taskHandler = NewsAggregatorFactory::make($newsAggregatorType);
        $taskHandler->fetch($attributes);
    }
}
