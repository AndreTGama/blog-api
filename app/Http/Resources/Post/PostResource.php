<?php

namespace App\Http\Resources\Post;

use App\Http\Resources\Category\CategoryResource;
use App\Http\Resources\PostMeta\PostMetaResource;
use App\Http\Resources\User\UserResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class PostResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'           => $this->id,
            'cover_image'  => $this->cover_image
                ? (filter_var($this->cover_image, FILTER_VALIDATE_URL)
                    ? $this->cover_image
                    : Storage::disk('public')->url($this->cover_image))
                : null,
            'allow_comments' => $this->allow_comments,
            'locale' => $this->locale,
            'translation_group_id' => $this->translation_group_id,
            'title'        => $this->title,
            'slug'         => $this->slug,
            'excerpt'      => $this->excerpt,
            'content'      => $this->content,
            'status'       => $this->status,
            'views'        => $this->views,
            'published_at' => $this->published_at,
            'author'       => new UserResource($this->whenLoaded('author')),
            'categories'   => CategoryResource::collection($this->whenLoaded('categories')),
            'translations' => $this->whenLoaded(
                'translations',
                fn () => $this->translations
                    ->where('status', 'published')
                    ->reject(fn ($translation) => $translation->is($this->resource))
                    ->map(fn ($translation) => [
                        'locale' => $translation->locale,
                        'slug' => $translation->slug,
                    ])
                    ->values(),
            ),
            'post_metas'   => PostMetaResource::collection($this->whenLoaded('postMetas')),
            'created_at'   => $this->created_at,
            'updated_at'   => $this->updated_at,
        ];
    }
}
