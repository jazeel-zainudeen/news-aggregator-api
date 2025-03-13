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
}