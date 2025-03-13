<?php

namespace App\Services\NewsAggregators\Enum;

enum NewsAggregatorTypeEnum: string
{
    /**
     * Represents the News API aggregator.
     */
    case NEWS_API = 'news_api';

    /**
     * Represents The Guardian news aggregator.
     */
    case THE_GUARDIAN = 'the_guardian';

    /**
     * Represents The New York Times news aggregator.
     */
    case NEW_YORK_TIMES = 'new_york_times';

    /**
     * Get the title of the aggregator.
     *
     * @param  NewsAggregatorTypeEnum  $aggregatorType  The aggregator type.
     * @return string The title of the aggregator.
     */
    public static function getTitle(NewsAggregatorTypeEnum $aggregatorType): string
    {
        return match ($aggregatorType) {
            self::NEWS_API => 'News API',
            self::THE_GUARDIAN => 'The Guardian',
            self::NEW_YORK_TIMES => 'The New York Times',
        };
    }
}
