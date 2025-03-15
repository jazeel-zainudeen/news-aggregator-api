<?php

namespace App\Http\Resources\Article;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Str;

/**
 * @OA\Schema(
 *     schema="ArticleResource",
 *     type="object",
 *     title="Article Resource",
 *     description="Article resource representation",
 *
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="author", type="string", example="John Doe"),
 *     @OA\Property(property="title", type="string", example="Breaking News: Laravel 12 Released"),
 *     @OA\Property(property="description", type="string", example="A brief summary of the article."),
 *     @OA\Property(property="content", type="string", example="Full content of the article..."),
 *     @OA\Property(property="image", type="string", format="url", example="https://example.com/image.jpg"),
 *     @OA\Property(property="source", type="string", example="BBC News"),
 *     @OA\Property(property="category", type="string", example="World News"),
 *     @OA\Property(property="published_at", type="string", format="date-time", example="2025-03-15 12:34:56")
 * )
 */
class ArticleResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  Request  $request
     * @return array<string, mixed>
     */
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'author' => $this->whenLoaded('author', fn () => $this->author?->name),
            'title' => $this->title,
            'description' => $this->description,
            'content' => $this->content,
            'image' => $this->url_to_image,
            'source' => $this->whenLoaded('source', fn () => $this->source?->name),
            'category' => $this->whenLoaded('category', fn () => Str::headline($this->category?->name)),
            'published_at' => $this->published_at?->format('Y-m-d H:i:s'),
        ];
    }
}
