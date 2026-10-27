<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Post extends Model
{
    /**
     * Uses the HasFactory trait for model factory support,
     * SoftDeletes trait for soft deletion functionality,
     * and HasUuids trait for UUID primary key generation.
     */
    use HasFactory, SoftDeletes, HasUuids;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'cover_image',
        'allow_comments',
        'title',
        'slug',
        'excerpt',
        'content',
        'status',
        'views',
        'published_at',
        'author_id',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'allow_comments' => 'boolean',
        'published_at' => 'datetime',
    ];

    /**
     * Preserve legacy text while storing structured content blocks as a versioned JSON document.
     */
    public function setContentAttribute(string|array|null $value): void
    {
        if (is_array($value)) {
            $value = json_encode([
                'format' => 'blocks-v1',
                'blocks' => $value,
            ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_THROW_ON_ERROR);
        }

        $this->attributes['content'] = $value;
    }

    public function getContentAttribute(?string $value): string|array|null
    {
        if ($value === null) {
            return null;
        }

        $document = json_decode($value, true);

        if (
            json_last_error() === JSON_ERROR_NONE
            && is_array($document)
            && ($document['format'] ?? null) === 'blocks-v1'
            && is_array($document['blocks'] ?? null)
        ) {
            return $document['blocks'];
        }

        return $value;
    }

    /**
     * Get the author of the post.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function author()
    {
        return $this->belongsTo(User::class, 'author_id');
    }

    /**
     * Get all metadata associated with this post.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function postMetas()
    {
        return $this->hasMany(PostMeta::class);
    }

    /**
     * Get the categories associated with the post.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function categories()
    {
        return $this->belongsToMany(Category::class, 'post_categories', 'post_id', 'category_id');
    }
}
