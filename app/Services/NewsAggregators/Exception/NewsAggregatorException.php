<?php

namespace App\Services\NewsAggregators\Exception;

use App\Services\NewsAggregators\Enum\NewsAggregatorTypeEnum;
use Exception;

class NewsAggregatorException extends Exception
{
    /**
     * Throws a NewsAggregatorException with a message indicating an invalid aggregator type.
     *
     * @throws NewsAggregatorException
     */
    public static function invalidAggregatorType(): self
    {
        return new self(__('Invalid aggregator type'));
    }

    /**
     * Throws a NewsAggregatorException with a message indicating a failure to fetch news articles.
     *
     * @throws NewsAggregatorException
     */
    public static function failedToFetch(NewsAggregatorTypeEnum $aggregator): self
    {
        $aggregatorTitle = NewsAggregatorTypeEnum::getTitle($aggregator);

        return new self(__("Failed to fetch news articles from {$aggregatorTitle}."));
    }

    /**
     * Throws a NewsAggregatorException with a message indicating missing configuration for an aggregator.
     *
     * @throws NewsAggregatorException
     */
    public static function missingConfiguration(NewsAggregatorTypeEnum $aggregator): self
    {
        $aggregatorTitle = NewsAggregatorTypeEnum::getTitle($aggregator);

        return new self(__("Missing configuration for {$aggregatorTitle}."));
    }
}
