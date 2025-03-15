<?php

namespace App\Models;

use App\Traits\Models\Filterable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

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
