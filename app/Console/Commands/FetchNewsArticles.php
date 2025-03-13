<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\NewsAggregators\NewsAggregatorService;
use App\Services\NewsAggregators\Enum\NewsAggregatorTypeEnum;
use Exception;

class FetchNewsArticles extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'news:fetch';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fetch and update articles from third-party news sources';

    /**
     * News aggregator service instance.
     *
     * @var NewsAggregatorService
     */
    protected $newsAggregatorService;

    /**
     * Create a new command instance.
     */
    public function __construct(NewsAggregatorService $newsAggregatorService)
    {
        parent::__construct();
        $this->newsAggregatorService = $newsAggregatorService;
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Fetching news articles from all aggregators...');

        try {
            $aggregators = NewsAggregatorTypeEnum::cases();
            foreach ($aggregators as $aggregator) {
                $this->info("Fetching from: " . $aggregator->name);
                $this->newsAggregatorService->fetchNewsArticles($aggregator->value);
            }
            $this->info('News fetching completed successfully.');
        } catch (Exception $e) {
            $this->error('Error fetching articles: ' . $e->getMessage());
        }
    }
}
