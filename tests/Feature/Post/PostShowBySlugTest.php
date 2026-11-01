<?php

namespace Tests\Feature\Post;

use App\Models\Post;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

class PostShowBySlugTest extends TestCase
{
    use RefreshDatabase;

    public function test_post_can_be_retrieved_by_slug(): void
    {
        $post = Post::factory()->create([
            'slug' => 'post-de-teste',
            'views' => 10,
        ]);

        $this->getJson(route('posts.show', 'post-de-teste'))
            ->assertOk()
            ->assertJsonPath('data.id', $post->id)
            ->assertJsonPath('data.slug', 'post-de-teste')
            ->assertJsonPath('data.views', 11);

        $this->getJson(route('posts.show', 'post-de-teste'))
            ->assertOk()
            ->assertJsonPath('data.views', 11);

        $this->assertSame(11, $post->fresh()->views);
    }

    public function test_post_can_also_be_retrieved_by_id_for_existing_dashboard_links(): void
    {
        $post = Post::factory()->create();

        $this->getJson(route('posts.show', $post->id))
            ->assertOk()
            ->assertJsonPath('data.id', $post->id);
    }

    public function test_post_response_lists_published_translations(): void
    {
        $translationGroupId = (string) Str::uuid();
        $post = Post::factory()->create([
            'locale' => 'pt',
            'slug' => 'artigo-em-portugues',
            'translation_group_id' => $translationGroupId,
            'status' => 'published',
        ]);
        Post::factory()->create([
            'locale' => 'en',
            'slug' => 'article-in-english',
            'translation_group_id' => $translationGroupId,
            'status' => 'published',
        ]);
        Post::factory()->create([
            'locale' => 'en',
            'slug' => 'unpublished-translation',
            'translation_group_id' => (string) Str::uuid(),
            'status' => 'draft',
        ]);

        $this->getJson(route('posts.show', $post->slug))
            ->assertOk()
            ->assertJsonPath('data.locale', 'pt')
            ->assertJsonPath('data.translation_group_id', $translationGroupId)
            ->assertJsonPath('data.translations.0.locale', 'en')
            ->assertJsonPath('data.translations.0.slug', 'article-in-english')
            ->assertJsonCount(1, 'data.translations');
    }

    public function test_post_index_can_be_filtered_by_locale(): void
    {
        Post::factory()->create(['locale' => 'pt']);
        $englishPost = Post::factory()->create(['locale' => 'en']);

        $this->getJson(route('posts.index', ['filters' => ['locale' => 'en']]))
            ->assertOk()
            ->assertJsonPath('data.items.0.id', $englishPost->id)
            ->assertJsonPath('data.items.0.locale', 'en')
            ->assertJsonCount(1, 'data.items');
    }
}
