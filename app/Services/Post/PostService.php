<?php

namespace App\Services\Post;

use App\DataTransferObjects\Common\IndexDTO;
use App\DataTransferObjects\Post\Request\StorePostDTO;
use App\DataTransferObjects\Post\Request\UpdatePostDTO;
use App\Models\Post;
use App\Supports\SlugGenerator;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\UploadedFile;
use RuntimeException;

class PostService
{
    private array $loads = ['author', 'categories', 'postMetas'];

    public function __construct(
        private SlugGenerator $slugGenerator
    ) {}

    /**
     * Retrieve a paginated list of posts based on the provided filters and pagination parameters.
     *
     * @param IndexDTO $dto The data transfer object containing filters and pagination parameters.
     * @return LengthAwarePaginator A paginated collection of posts matching the criteria.
     */
    public function index(IndexDTO $dto): LengthAwarePaginator
    {
        $query = Post::query();

        if (!empty($dto->filters['search'])) {
            $searchTerm = $dto->filters['search'];
            $query->where(function ($q) use ($searchTerm) {
                $q->where('title', 'like', "%{$searchTerm}%")
                    ->orWhere('slug', 'like', "%{$searchTerm}%")
                    ->orWhere('excerpt', 'like', "%{$searchTerm}%")
                    ->orWhere('content', 'like', "%{$searchTerm}%")
                    ->orWhereHas('author', function ($q) use ($searchTerm) {
                        $q->where('name', 'like', "%{$searchTerm}%")
                            ->orWhere('email', 'like', "%{$searchTerm}%");
                    });
            });
        }

        $query->orderBy($dto->orderBy, $dto->orderDirection);
        $query->with($this->loads);
        return $query->paginate($dto->limit, ['*'], 'page', $dto->page);
    }

    /**
     * Store a new post in the database based on the provided data transfer object.
     *
     * @param StorePostDTO $dto The data transfer object containing the details of the post to be created.
     * @return Post The newly created post instance.
     */
    public function store(StorePostDTO $dto): Post
    {
        $post = new Post();
        $post->cover_image = $dto->coverImage instanceof UploadedFile
            ? $this->storeCoverImage($dto->coverImage)
            : $dto->coverImage;
        $post->title = $dto->title;
        $post->slug = $this->slugGenerator->generateUnique($dto->title, 'posts');
        $post->excerpt = $dto->excerpt;
        $post->content = $dto->content;
        $post->status = $dto->status;
        $post->author_id = auth()->id();

        $post->save();

        if ($dto->postMetas) {
            foreach ($dto->postMetas as $meta) {
                $post->postMetas()->create([
                    'key' => $meta->key,
                    'value' => $meta->value,
                ]);
            }
        }

        if (!empty($dto->categoryIds)) {
            $post->categories()->sync($dto->categoryIds);
        }

        return $post;
    }

    /**
     * Retrieve the details of a specific post.
     *
     * @param Post $post The post model instance to be retrieved.
     * @return Post The post instance with its related data loaded.
     */
    public function show(Post $post): Post
    {
        return $post->load($this->loads);
    }

    /**
     * Update an existing post in the database based on the provided data transfer object.
     *
     * @param Post $post The post model instance to be updated.
     * @param UpdatePostDTO $dto The data transfer object containing the updated details of the post.
     * @return Post The updated post instance.
     */
    public function update(Post $post, UpdatePostDTO $dto): Post
    {
        $post->cover_image = $dto->coverImage instanceof UploadedFile
            ? $this->storeCoverImage($dto->coverImage)
            : ($dto->coverImage ?? $post->cover_image);
        if ($dto->title && $dto->title !== $post->title) {
            $post->slug = $this->slugGenerator->generateUnique($dto->title, 'posts');
        }
        $post->title = $dto->title ?? $post->title;
        $post->excerpt = $dto->excerpt ?? $post->excerpt;
        $post->content = $dto->content ?? $post->content;
        $post->status = $dto->status ?? $post->status;

        $post->save();

        if ($dto->postMetas) {
            foreach ($dto->postMetas as $meta) {
                $existingMeta = $post->postMetas()->where('key', $meta->key)->first();
                if ($existingMeta) {
                    $existingMeta->update(['value' => $meta->value]);
                } else {
                    $post->postMetas()->create([
                        'key' => $meta->key,
                        'value' => $meta->value,
                    ]);
                }
            }
        }

        if ($dto->categoryIds !== null) {
            $post->categories()->sync($dto->categoryIds);
        }

        return $post;
    }

    /**
     * Delete a post from the database.
     *
     * @param Post $post The post model instance to be deleted.
     * @return void
     */
    public function delete(Post $post): void
    {
        $post->delete();
    }

    /**
     * Restore a soft-deleted post.
     *
     * @param Post $post The post model instance to be restored.
     * @return void
     */
    public function restore(Post $post): void
    {
        $post->restore();
    }

    private function storeCoverImage(UploadedFile $image): string
    {
        $path = $image->store('posts/thumbnails', 'public');

        if (!is_string($path)) {
            throw new RuntimeException('The post cover image could not be stored.');
        }

        return $path;
    }
}
