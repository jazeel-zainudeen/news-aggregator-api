<?php

namespace App\Http\Controllers;

use App\Http\Filters\NewsArticleFilter;
use App\Http\Requests\Article\ArticleListingRequest;
use App\Http\Resources\Article\ArticleListingResource;
use App\Models\NewsArticle;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

/**
 * @OA\Tag(
 *     name="Article Management",
 *     description="API Endpoints for managing articles"
 * )
 */
class ArticleController extends Controller
{
    /**
     * News article listing with filters.
     *
     * @OA\Get(
     *     path="/api/articles",
     *     summary="Get a list of articles with filtering options",
     *     tags={"Article Management"},
     *     security={{"sanctum":{}}},
     *
     *     @OA\Parameter(
     *         name="per_page",
     *         in="query",
     *         description="Number of articles per page",
     *         required=false,
     *
     *         @OA\Schema(type="integer", default=20)
     *     ),
     *
     *     @OA\Parameter(
     *         name="search",
     *         in="query",
     *         description="Search articles by keyword",
     *         required=false,
     *
     *         @OA\Schema(type="string")
     *     ),
     *
     *     @OA\Parameter(
     *         name="date",
     *         in="query",
     *         description="Filter articles by date (YYYY-MM-DD)",
     *         required=false,
     *
     *         @OA\Schema(type="string", format="date")
     *     ),
     *
     *     @OA\Parameter(
     *         name="category",
     *         in="query",
     *         description="Filter articles by category(id)",
     *         required=false,
     *
     *         @OA\Schema(type="integer")
     *     ),
     *
     *     @OA\Parameter(
     *         name="source",
     *         in="query",
     *         description="Filter articles by source(id)",
     *         required=false,
     *
     *         @OA\Schema(type="integer")
     *     ),
     *
     *     @OA\Response(
     *         response=200,
     *         description="List of filtered articles",
     *
     *         @OA\JsonContent(
     *             type="array",
     *
     *             @OA\Items(ref="#/components/schemas/NewsArticle")
     *         )
     *     )
     * )
     */
    public function index(ArticleListingRequest $request, NewsArticleFilter $filter): AnonymousResourceCollection
    {
        $articles = NewsArticle::select('id', 'source_id', 'category_id', 'author_id', 'title', 'description', 'published_at')
            ->with([
                'source:id,name',
                'category:id,name',
                'author:id,name',
            ])
            ->latest('news_articles.published_at')
            ->filter($filter)
            ->cursorPaginate($request->get('per_page', 20));

        return ArticleListingResource::collection($articles);
    }
}
