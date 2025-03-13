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
}