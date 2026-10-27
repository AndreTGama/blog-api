<?php 

namespace App\DataTransferObjects\Post\Request;

use App\DataTransferObjects\PostMeta\Response\IndexPostMetaDTO;
use Illuminate\Http\UploadedFile;

class StorePostDTO
{
    /**
     * Data Transfer Object for storing a new post.
     *
     * @param UploadedFile|string|null $coverImage The uploaded cover image or its existing URL.
     * @param string $title The title of the post.
     * @param string $slug The slug for the post.
     * @param string $excerpt A short excerpt of the post.
     * @param string|array $content The post text or ordered content blocks.
     * @param string $status The status of the post (e.g., draft, published).
     * @param bool $allowComments Whether comments are allowed on the post.
     * @param IndexPostMetaDTO|null $postMeta The post meta data for the post (optional).
     * @param array $categoryIds An array of category IDs associated with the post.
     */
    public function __construct(
        public UploadedFile|string|null $coverImage,
        public string            $title,
        public string            $excerpt,
        public string|array      $content,
        public string            $status,
        public ?array            $postMetas,
        public array             $categoryIds,
        public bool              $allowComments = false
    ) {}

    /**
     * Creates a new instance of the class from an associative array.
     *
     * @param array $data Associative array containing 'title', 'slug', 'excerpt', 'content', and 'category_ids' keys.
     * @return self Returns an instance of the class populated with the provided data.
     */
    public static function fromArray(array $data): self
    {
        return new self(
            coverImage: $data['cover_image'] ?? null,
            title: $data['title'],
            excerpt: $data['excerpt'],
            content: $data['content'],
            status: $data['status'],
            postMetas: array_key_exists('post_meta', $data) ? array_map(fn($meta) => IndexPostMetaDTO::fromArray($meta), $data['post_metas']) : null,
            categoryIds: $data['category_ids'] ?? [],
            allowComments: $data['allow_comments'] ?? false
        );
    }

    /**
     * Converts the StorePostDTO object properties to an associative array.
     *
     * @return array{
     *     cover_image: UploadedFile|string|null,
     *     title: string,
     *     slug: string,
     *     excerpt: string,
     *     content: string,
     *     status: string,
     *     allow_comments: bool,
     *     post_metas: array|null,
     *     category_ids: array
     * }
     * Returns an array containing the post's title, slug, excerpt, content, and associated category IDs.
     */
    public function toArray(): array
    {
        return [
            'cover_image' => $this->coverImage,
            'title' => $this->title,
            'excerpt' => $this->excerpt,
            'content' => $this->content,
            'status' => $this->status,
            'allow_comments' => $this->allowComments,
            'post_metas' => $this->postMetas ? array_map(fn($meta) => $meta->toArray(), $this->postMetas) : null,
            'category_ids' => $this->categoryIds
        ];
    }
}