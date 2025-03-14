<?php

namespace App\Services\NewsAggregators\Adapters;

use App\Services\NewsAggregators\Contracts\NewsAggregatorInterface;
use App\Services\NewsAggregators\Enum\NewsAggregatorTypeEnum;
use App\Services\NewsAggregators\Exception\NewsAggregatorException;
use App\Services\NewsAggregators\Repositories\NewsArticleRepository;
use Exception;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class TheGuardianAdapter implements NewsAggregatorInterface
{
    /**
     * NewsArticleRepository instance.
     */
    protected NewsArticleRepository $newsArticleRepository;

    /**
     * TheGuardianAdapter constructor.
     *
     * @param  NewsArticleRepository  $newsArticleRepository
     */
    public function __construct()
    {
        $this->newsArticleRepository = new NewsArticleRepository;
    }

    /**
     * @inheritDoc
     */
    public function fetch(array $data): array
    {
        try {
            $apiKey = config('services.the_guardian.key');
            $apiEndpoint = config('services.the_guardian.endpoint');

            if (empty($apiKey) || empty($apiEndpoint)) {
                throw NewsAggregatorException::missingConfiguration(NewsAggregatorTypeEnum::THE_GUARDIAN);
            }

            $totalLimit = $data['limit'] ?? 100;

            $response = Http::get($apiEndpoint . 'search', [
                'api-key' => $apiKey,
                'type' => 'article',
                'page-size' => $totalLimit,
                'show-fields' => 'thumbnail,bodyText,trailText,byline',
            ]);

            if ($response->failed() && $response->json('response.status') !== 'ok') {
                throw NewsAggregatorException::failedToFetch(NewsAggregatorTypeEnum::THE_GUARDIAN);
            }

            $articles = $response->json('response.results', []);

            foreach ($articles as $article) {
                $this->newsArticleRepository->create($article, NewsAggregatorTypeEnum::THE_GUARDIAN);
            }

            return $articles;
        } catch (Exception $exception) {
            Log::error('Failed to fetch news articles from The Guardian', [
                'exception' => $exception,
                'data' => $data,
            ]);

            throw $exception;
        }
    }
}
