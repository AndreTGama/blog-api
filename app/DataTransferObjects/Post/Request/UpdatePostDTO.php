<?php

namespace App\DataTransferObjects\Post\Request;

use App\DataTransferObjects\PostMeta\Response\IndexPostMetaDTO;
use Illuminate\Http\UploadedFile;

class UpdatePostDTO
{
    public function __construct(
        public UploadedFile|string|null $coverImage,
        public ?string $title,
        public ?string $excerpt,
        public string|array|null $content,
        public ?string $status,
        public ?array $postMetas,
        public ?array $categoryIds,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            coverImage: $data['cover_image'] ?? null,
            title: $data['title'] ?? null,
            excerpt: $data['excerpt'] ?? null,
            content: $data['content'] ?? null,
            status: $data['status'] ?? null,
            postMetas: array_key_exists('post_metas', $data)
                ? array_map(fn ($meta) => IndexPostMetaDTO::fromArray($meta), $data['post_metas'])
                : null,
            categoryIds: $data['category_ids'] ?? null,
        );
    }
}
