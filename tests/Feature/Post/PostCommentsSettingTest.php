<?php

namespace Tests\Feature\Post;

use App\Models\Category;
use App\Models\Post;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PostCommentsSettingTest extends TestCase
{
    use RefreshDatabase;

    public function test_new_posts_do_not_allow_comments_by_default(): void
    {
        $author = $this->createAuthor();
        $category = Category::factory()->create();

        $response = $this->actingAs($author, 'sanctum')->postJson(route('posts.store'), [
            'cover_image' => 'https://example.test/cover.png',
            'title' => 'Post sem comentários',
            'excerpt' => 'Resumo do post.',
            'content' => 'Conteúdo do post.',
            'status' => 'draft',
            'category_ids' => [$category->id],
        ]);

        $response
            ->assertCreated()
            ->assertJsonPath('data.allow_comments', false);

        $this->assertFalse(Post::query()->firstOrFail()->allow_comments);
    }

    public function test_comments_can_be_enabled_when_creating_a_post(): void
    {
        $author = $this->createAuthor();
        $category = Category::factory()->create();

        $response = $this->actingAs($author, 'sanctum')->postJson(route('posts.store'), [
            'cover_image' => 'https://example.test/cover.png',
            'title' => 'Post com comentários',
            'excerpt' => 'Resumo do post.',
            'content' => 'Conteúdo do post.',
            'status' => 'draft',
            'category_ids' => [$category->id],
            'allow_comments' => true,
        ]);

        $response
            ->assertCreated()
            ->assertJsonPath('data.allow_comments', true);

        $this->assertTrue(Post::query()->firstOrFail()->allow_comments);
    }

    public function test_comments_setting_can_be_enabled_and_disabled_when_updating_a_post(): void
    {
        $author = $this->createAuthor();
        $post = Post::factory()->create([
            'author_id' => $author->id,
            'allow_comments' => false,
        ]);

        $this->actingAs($author, 'sanctum')
            ->putJson(route('posts.update', $post->id), ['allow_comments' => true])
            ->assertOk()
            ->assertJsonPath('data.allow_comments', true);

        $this->assertTrue($post->fresh()->allow_comments);

        $this->actingAs($author, 'sanctum')
            ->putJson(route('posts.update', $post->id), ['excerpt' => 'Resumo atualizado.'])
            ->assertOk()
            ->assertJsonPath('data.allow_comments', true);

        $this->actingAs($author, 'sanctum')
            ->putJson(route('posts.update', $post->id), ['allow_comments' => false])
            ->assertOk()
            ->assertJsonPath('data.allow_comments', false);

        $this->assertFalse($post->fresh()->allow_comments);
    }

    private function createAuthor(): User
    {
        $role = Role::factory()->create(['name' => 'editor']);

        return User::factory()->create(['role_id' => $role->id]);
    }
}
