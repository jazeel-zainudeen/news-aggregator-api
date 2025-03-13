<?php

namespace App\Services\NewsAggregators\Enum;

enum NewsApiCategoryEnum: string
{
    /**
     * Represents the business category.
     */
    case BUSINESS = 'business';

    /**
     * Represents the entertainment category.
     */
    case ENTERTAINMENT = 'entertainment';

    /**
     * Represents the general category.
     */
    case GENERAL = 'general';

    /**
     * Represents the health category.
     */
    case HEALTH = 'health';

    /**
     * Represents the science category.
     */
    case SCIENCE = 'science';

    /**
     * Represents the sports category.
     */
    case SPORTS = 'sports';

    /**
     * Represents the technology category.
     */
    case TECHNOLOGY = 'technology';
}