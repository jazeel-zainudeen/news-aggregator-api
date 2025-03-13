<?php

namespace App\Services\NewsAggregators\Exception;

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
    public static function failedToFetch(string $aggregator): self
    {
        return new self(__("Failed to fetch news articles from {$aggregator}."));
    }

    public static function missingConfiguration(string $aggregator): self
    {
        return new self(__("Missing configuration for {$aggregator}."));
    }
}