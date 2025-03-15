<?php

namespace App\Models;

use App\Traits\Models\Filterable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @OA\Schema(
 *     schema="NewsArticle",
 *     title="News Article Model",
 *     description="Represents a news article with various attributes",
 *
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="category_id", type="integer", example=2, description="Category ID of the article"),
 *     @OA\Property(property="source_id", type="integer", example=3, description="Source ID of the article"),
 *     @OA\Property(property="author_id", type="integer", example=5, description="Author ID of the article"),
 *     @OA\Property(property="title", type="string", example="Breaking News: AI Revolution"),
 *     @OA\Property(property="description", type="string", example="A brief description of the article"),
 *     @OA\Property(property="published_at", type="string", format="date-time", example="2025-03-15T10:00:00Z"),
 *     @OA\Property(property="url_to_image", type="string", format="url", example="https://example.com/image.jpg"),
 *     @OA\Property(property="content", type="string", example="Full content of the article"),
 *     @OA\Property(property="api_source", type="string", example="NewsAPI")
 * )
 */
class NewsArticle extends Model
{
    /** @use HasFactory<\Database\Factories\NewsArticleFactory> */
    use Filterable, HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'category_id',
        'source_id',
        'author_id',
        'title',
        'description',
        'published_at',
        'url_to_image',
        'content',
        'api_source',
    ];

    /**
     * Get the author of the article.
     */
    public function author(): BelongsTo
    {
        return $this->belongsTo(Author::class, 'author_id');
    }

    /**
     * Get the source of the article.
     */
    public function source(): BelongsTo
    {
        return $this->belongsTo(Source::class, 'source_id');
    }

    /**
     * Get the category of the article.
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'category_id');
    }

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'published_at' => 'datetime',
        ];
    }
}
