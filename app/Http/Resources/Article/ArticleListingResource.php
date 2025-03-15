<?php

namespace App\Http\Resources\Article;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Str;

/**
 * @OA\Schema(
 *     schema="ArticleListingResource",
 *     title="Article Listing Resource",
 *     description="Article details returned in a listing",
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="author", type="string", nullable=true, example="John Doe"),
 *     @OA\Property(property="title", type="string", example="Breaking News: Laravel 12 Released"),
 *     @OA\Property(property="description", type="string", example="Laravel 12 brings many improvements including..."),
 *     @OA\Property(property="source", type="string", nullable=true, example="TechCrunch"),
 *     @OA\Property(property="category", type="string", example="Technology"),
 *     @OA\Property(property="published_at", type="string", format="date-time", example="2025-03-15 14:30:00")
 * )
 */
class ArticleListingResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'author' => $this->author?->name,
            'title' => $this->title,
            'description' => $this->description,
            'source' => $this->source?->name,
            'category' => Str::headline($this->category?->name),
            'published_at' => $this->published_at->format('Y-m-d H:i:s'),
        ];
    }
}
