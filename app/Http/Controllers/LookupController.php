<?php

namespace App\Http\Controllers;

use App\Http\Resources\AuthorResource;
use App\Http\Resources\CategoryResource;
use App\Http\Resources\SourceResource;
use App\Models\Author;
use App\Models\Category;
use App\Models\Source;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

/**
 * @OA\Tag(name="Lookup", description="Endpoints to list available categories, sources and authors")
 */
class LookupController extends Controller
{
    /**
     * List all categories with pagination.
     *
     * @OA\Get(
     *     path="/api/lookup/categories",
     *     tags={"Lookup"},
     *     summary="Get list of categories",
     *     security={{"sanctum":{}}},
     *
     *     @OA\Parameter(
     *         name="per_page",
     *         in="query",
     *         description="Number of items per page",
     *         required=false,
     *
     *         @OA\Schema(type="integer", default=10)
     *     ),
     *
     *     @OA\Response(
     *         response=200,
     *         description="Successful response",
     *
     *         @OA\JsonContent(type="array", @OA\Items(ref="#/components/schemas/CategoryResource"))
     *     )
     * )
     */
    public function listCategories(Request $request): AnonymousResourceCollection
    {
        $categories = Category::select('id', 'name')
            ->orderBy('name')
            ->cursorPaginate($request->integer('per_page', 10));

        return CategoryResource::collection($categories);
    }

    /**
     * List all sources with pagination.
     *
     * @OA\Get(
     *     path="/api/lookup/sources",
     *     tags={"Lookup"},
     *     summary="Get list of sources",
     *     security={{"sanctum":{}}},
     *
     *     @OA\Parameter(
     *         name="per_page",
     *         in="query",
     *         description="Number of items per page",
     *         required=false,
     *
     *         @OA\Schema(type="integer", default=10)
     *     ),
     *
     *     @OA\Response(
     *         response=200,
     *         description="Successful response",
     *
     *         @OA\JsonContent(type="array", @OA\Items(ref="#/components/schemas/SourceResource"))
     *     )
     * )
     */
    public function listSources(Request $request): AnonymousResourceCollection
    {
        $sources = Source::select('id', 'name')
            ->orderBy('name')
            ->cursorPaginate($request->integer('per_page', 10));

        return SourceResource::collection($sources);
    }

    /**
     * List all authors with pagination.
     *
     * @OA\Get(
     *     path="/api/lookup/authors",
     *     tags={"Lookup"},
     *     summary="Get list of authors",
     *     security={{"sanctum":{}}},
     *
     *     @OA\Parameter(
     *         name="per_page",
     *         in="query",
     *         description="Number of items per page",
     *         required=false,
     *
     *         @OA\Schema(type="integer", default=10)
     *     ),
     *
     *     @OA\Response(
     *         response=200,
     *         description="Successful response",
     *
     *         @OA\JsonContent(type="array", @OA\Items(ref="#/components/schemas/AuthorResource"))
     *     )
     * )
     */
    public function listAuthors(Request $request): AnonymousResourceCollection
    {
        $authors = Author::select('id', 'name')
            ->orderBy('name')
            ->cursorPaginate($request->integer('per_page', 10));

        return AuthorResource::collection($authors);
    }
}
