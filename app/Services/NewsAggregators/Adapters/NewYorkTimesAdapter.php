<?php

namespace App\Services\NewsAggregators\Adapters;

use App\Services\NewsAggregators\Contracts\NewsAggregatorInterface;
use App\Services\NewsAggregators\Enum\NewsAggregatorTypeEnum;
use App\Services\NewsAggregators\Exception\NewsAggregatorException;
use App\Services\NewsAggregators\Repositories\NewsArticleRepository;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class NewYorkTimesAdapter implements NewsAggregatorInterface
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
     * {@inheritDoc}
     */
    public function fetch(array $data): array
    {
        try {
            $apiKey = config('services.the_new_york_times.key');
            $apiEndpoint = config('services.the_new_york_times.endpoint');

            if (empty($apiKey) || empty($apiEndpoint)) {
                throw NewsAggregatorException::missingConfiguration(NewsAggregatorTypeEnum::NEW_YORK_TIMES);
            }

            $response = Http::get($apiEndpoint . 'topstories/v2/home.json', [
                'api-key' => $apiKey,
            ]);

            if ($response->failed() || $response->json('status') !== 'OK') {
                throw NewsAggregatorException::failedToFetch(NewsAggregatorTypeEnum::NEW_YORK_TIMES);
            }

            $articles = $response->json('results', []);

            if (! empty($articles)) {
                DB::transaction(function () use ($articles) {
                    foreach ($articles as $article) {
                        $this->newsArticleRepository->create($article, NewsAggregatorTypeEnum::NEW_YORK_TIMES);
                    }
                });
            }

            return $articles;
        } catch (Exception $exception) {
            Log::error('Failed to fetch news articles from NewsAPI', [
                'exception' => $exception,
                'data' => $data,
            ]);

            throw $exception;
        }
    }
}
