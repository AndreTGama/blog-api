<?php

namespace App\Http\Controllers\Post;

use App\DataTransferObjects\Post\Request\StorePostDTO;
use App\DataTransferObjects\Post\Request\UpdatePostDTO;
use App\Http\Controllers\Controller;
use App\Http\Requests\Post\IndexPostRequest;
use App\Http\Requests\Post\StorePostRequest;
use App\Http\Requests\Post\UpdatePostRequest;
use App\Http\Resources\Common\PaginatedResource;
use App\Http\Resources\Post\PostResource;
use App\Models\Post;
use App\Responses\ApiResponse;
use App\Services\Post\PostService;

class PostController extends Controller
{

    /**
     * PostController constructor.
     *
     * @param PostService $postService The service responsible for handling post-related business logic.
     */
    public function __construct(private PostService $postService) {}


    /**
     * Retrieve a paginated list of posts.
     *
     * @param IndexPostRequest $request The request object containing filters and pagination parameters.
     * @return \Illuminate\Http\JsonResponse A JSON response containing the paginated list of posts.
     */
    public function index(IndexPostRequest $request)
    {
        $data = $this->postService->index(dto: $request->toDTO());
        return ApiResponse::success(
            message: 'Posts retrieved successfully.',
            data: new PaginatedResource($data, PostResource::class),
            status: 200
        );
    }

    /**
     * Store a newly created post in the database.
     *
     * @param StorePostRequest $request The request object containing the details of the post to be created.
     * @return \Illuminate\Http\JsonResponse A JSON response indicating the success of the operation and containing the created post data.
     */
    public function store(StorePostRequest $request)
    {
        $this->authorize('create', Post::class);
        $data = $this->postService->store(dto: StorePostDTO::fromArray($request->validated()));
        return ApiResponse::success(
            message: 'Post created successfully.',
            data: new PostResource($data),
            status: 201
        );
    }

    /**
     * Display the specified post.
     *
     * @param Post $postResource The post model instance to be displayed.
     * @return \Illuminate\Http\JsonResponse A JSON response containing the details of the specified post.
     */
    public function show(Post $postResource)
    {
        $data = $this->postService->show(post: $postResource);
        return ApiResponse::success(
            message: 'Post retrieved successfully.',
            data: new PostResource($data),
            status: 200
        );
    }

    /**
     * Update the specified post in the database.
     *
     * @param UpdatePostRequest $request The request object containing the updated details of the post.
     * @param Post $postResource The post model instance to be updated.
     * @return \Illuminate\Http\JsonResponse A JSON response indicating the success of the operation and containing the updated post data.
     */
    public function update(UpdatePostRequest $request, Post $postResource)
    {
        $this->authorize('update', $postResource);
        $data = $this->postService->update(post: $postResource, dto: UpdatePostDTO::fromArray($request->validated()));
        return ApiResponse::success(
            message: 'Post updated successfully.',
            data: new PostResource($data),
            status: 200
        );
    }

    /**
     * Remove the specified post from the database.
     *
     * @param Post $postResource The post model instance to be deleted.
     * @return \Illuminate\Http\JsonResponse A JSON response indicating the success of the operation.
     */
    public function delete(Post $postResource)
    {
        $this->authorize('delete', $postResource);
        $this->postService->delete(post: $postResource);
        return ApiResponse::success(
            message: 'Post deleted successfully.',
            data: null,
            status: 200
        );
    }

    /**
     * Restore a soft-deleted post.
     *
     * @param Post $postResource The post model instance to be restored.
     * @return \Illuminate\Http\JsonResponse A JSON response indicating the success of the operation.
     */
    public function restore(Post $postResource)
    {
        $this->authorize('restore', $postResource);
        $this->postService->restore(post: $postResource);
        return ApiResponse::success(
            message: 'Post restored successfully.',
            data: null,
            status: 200
        );
    }
}
