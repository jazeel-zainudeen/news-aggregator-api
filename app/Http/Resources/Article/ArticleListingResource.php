<?php

namespace App\Http\Resources\Article;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Str;

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
