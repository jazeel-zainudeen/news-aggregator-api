<?php

namespace App\Http\Controllers;

use App\Http\Filters\NewsArticleFilter;
use App\Http\Requests\Article\ArticleListingRequest;
use App\Http\Resources\Article\ArticleListingResource;
use App\Http\Resources\Article\ArticleResource;
use App\Models\NewsArticle;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Auth;

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

    /**
     * @OA\Get(
     *     path="/api/articles/{article}",
     *     summary="Get a specific article",
     *     tags={"Article Management"},
     *     security={{"sanctum":{}}},
     *
     *     @OA\Parameter(
     *         name="article",
     *         in="path",
     *         required=true,
     *         description="ID of the article",
     *
     *         @OA\Schema(type="integer")
     *     ),
     *
     *     @OA\Response(
     *         response=200,
     *         description="Successful response",
     *
     *         @OA\JsonContent(ref="#/components/schemas/ArticleResource")
     *     ),
     *
     *     @OA\Response(
     *         response=404,
     *         description="Article not found"
     *     )
     * )
     */
    public function show(NewsArticle $article): ArticleResource
    {
        $article->load(['source:id,name', 'category:id,name', 'author:id,name']);

        return new ArticleResource($article);
    }

    /**
     * Personalized news article listing
     *
     * @OA\Get(
     *     path="/api/articles/personalized",
     *     summary="Get a list of personalized articles",
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
     *     @OA\Response(
     *         response=200,
     *         description="List of articles",
     *
     *         @OA\JsonContent(
     *             type="array",
     *
     *             @OA\Items(ref="#/components/schemas/NewsArticle")
     *         )
     *     )
     * )
     */
    public function personalizedFeed(Request $request): AnonymousResourceCollection
    {
        $user = Auth::user();
        $userPreferences = $user->preferences()->get()->groupBy('preferable_type');

        $authorIds = $userPreferences->has('author') 
            ? $userPreferences->get('author')->pluck('preferable_id')->filter()->toArray() 
            : [];

        $categoryIds = $userPreferences->has('category') 
            ? $userPreferences->get('category')->pluck('preferable_id')->filter()->toArray() 
            : [];

        $sourceIds = $userPreferences->has('source') 
            ? $userPreferences->get('source')->pluck('preferable_id')->filter()->toArray() 
            : [];

        $articles = NewsArticle::select('id', 'source_id', 'category_id', 'author_id', 'title', 'description', 'published_at')
            ->with([
                'source:id,name',
                'category:id,name',
                'author:id,name',
            ])
            ->where(function ($query) use ($authorIds, $categoryIds, $sourceIds) {
                if (!empty($authorIds)) {
                    $query->orWhereIn('author_id', $authorIds);
                }
                if (!empty($categoryIds)) {
                    $query->orWhereIn('category_id', $categoryIds);
                }
                if (!empty($sourceIds)) {
                    $query->orWhereIn('source_id', $sourceIds);
                }
            })
            ->latest('news_articles.published_at')
            ->cursorPaginate($request->get('per_page', 20));

        return ArticleListingResource::collection($articles);
    }
}
