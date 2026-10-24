<?php

namespace Tests\Feature\Post;

use App\Models\Category;
use App\Models\Post;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class PostCoverImageTest extends TestCase
{
    use RefreshDatabase;

    public function test_post_cover_image_is_stored_and_returned_as_a_public_url(): void
    {
        Storage::fake('public');
        $author = $this->createAuthor();
        $category = Category::factory()->create();

        $response = $this->actingAs($author, 'sanctum')->post(route('posts.store'), [
            'cover_image' => UploadedFile::fake()->create('cover.png')->mimeType('image/png'),
            'title' => 'Post com capa',
            'excerpt' => 'Resumo do post.',
            'content' => 'Conteúdo do post.',
            'status' => 'draft',
            'category_ids' => [$category->id],
        ], ['Accept' => 'application/json']);

        $response->assertCreated();

        $post = Post::query()->firstOrFail();
        $this->assertStringStartsWith('posts/thumbnails/', $post->cover_image);
        Storage::disk('public')->assertExists($post->cover_image);
        $response->assertJsonPath('data.cover_image', Storage::disk('public')->url($post->cover_image));
    }

    public function test_existing_cover_image_urls_are_returned_unchanged(): void
    {
        $coverImageUrl = 'https://example.test/existing-cover.png';
        $post = Post::factory()->create(['cover_image' => $coverImageUrl]);

        $this->getJson(route('posts.show', $post->id))
            ->assertOk()
            ->assertJsonPath('data.cover_image', $coverImageUrl);
    }

    public function test_post_cover_image_can_be_replaced_with_an_upload(): void
    {
        Storage::fake('public');
        $author = $this->createAuthor();
        $post = Post::factory()->create([
            'author_id' => $author->id,
            'cover_image' => 'https://example.test/old-cover.png',
        ]);

        $response = $this->actingAs($author, 'sanctum')->put(route('posts.update', $post->id), [
            'cover_image' => UploadedFile::fake()->create('new-cover.webp')->mimeType('image/webp'),
        ], ['Accept' => 'application/json']);

        $response->assertOk();

        $post->refresh();
        $this->assertStringStartsWith('posts/thumbnails/', $post->cover_image);
        Storage::disk('public')->assertExists($post->cover_image);
        $response->assertJsonPath('data.cover_image', Storage::disk('public')->url($post->cover_image));
    }

    public function test_post_cover_image_cannot_exceed_two_megabytes(): void
    {
        Storage::fake('public');
        $author = $this->createAuthor();
        $category = Category::factory()->create();

        $response = $this->actingAs($author, 'sanctum')
            ->post(route('posts.store'), [
                'cover_image' => UploadedFile::fake()->create('too-large.png')->size(2049)->mimeType('image/png'),
                'title' => 'Post com capa grande',
                'excerpt' => 'Resumo do post.',
                'content' => 'Conteúdo do post.',
                'status' => 'draft',
                'category_ids' => [$category->id],
            ], ['Accept' => 'application/json']);

        $response
            ->assertUnprocessable()
            ->assertJsonPath('data.errors.cover_image.0', 'The cover image field must not be greater than 2048 kilobytes.');
    }

    private function createAuthor(): User
    {
        $role = Role::factory()->create(['name' => 'editor']);

        return User::factory()->create(['role_id' => $role->id]);
    }
}
