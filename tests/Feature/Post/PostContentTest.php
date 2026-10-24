<?php

namespace Tests\Feature\Post;

use App\Models\Category;
use App\Models\Post;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PostContentTest extends TestCase
{
    use RefreshDatabase;

    public function test_post_content_can_mix_paragraphs_and_tabbed_code(): void
    {
        $role = Role::factory()->create(['name' => 'admin']);
        $author = User::factory()->create(['role_id' => $role->id]);
        $category = Category::factory()->create();
        $content = [
            [
                'type' => 'paragraph',
                'text' => 'O padrão Abstract Factory cria famílias de objetos relacionados.',
            ],
            [
                'type' => 'code_tabs',
                'tabs' => [
                    [
                        'label' => 'PHP',
                        'language' => 'php',
                        'code' => 'interface UIFactory { public function createButton(): Button; }',
                    ],
                    [
                        'label' => 'TypeScript',
                        'language' => 'typescript',
                        'code' => 'interface UIFactory { createButton(): Button; }',
                    ],
                ],
            ],
            [
                'type' => 'paragraph',
                'text' => 'O texto continua depois do exemplo.',
            ],
        ];

        $response = $this->actingAs($author, 'sanctum')->postJson(route('posts.store'), [
            'cover_image' => 'https://example.test/cover.png',
            'title' => 'Abstract Factory',
            'excerpt' => 'Um exemplo de padrão de projeto.',
            'content' => $content,
            'status' => 'draft',
            'category_ids' => [$category->id],
        ]);

        $response
            ->assertCreated()
            ->assertJsonPath('data.content', $content);

        $post = Post::query()->firstOrFail();

        $this->assertSame($content, $post->content);
        $this->assertStringContainsString('"format":"blocks-v1"', $post->getRawOriginal('content'));
    }

    public function test_existing_plain_text_content_is_still_returned_as_a_string(): void
    {
        $post = Post::factory()->create(['content' => 'Conteúdo legado em texto simples.']);

        $this->getJson(route('posts.show', $post->id))
            ->assertOk()
            ->assertJsonPath('data.content', 'Conteúdo legado em texto simples.');
    }

    public function test_post_content_can_be_updated_without_resending_other_fields(): void
    {
        $role = Role::factory()->create(['name' => 'editor']);
        $author = User::factory()->create(['role_id' => $role->id]);
        $post = Post::factory()->create([
            'author_id' => $author->id,
            'content' => 'Conteúdo antigo.',
        ]);
        $content = [
            [
                'type' => 'paragraph',
                'text' => 'Conteúdo atualizado.',
            ],
        ];

        $this->actingAs($author, 'sanctum')
            ->putJson(route('posts.update', $post->id), ['content' => $content])
            ->assertOk()
            ->assertJsonPath('data.content', $content);

        $this->assertSame($content, $post->fresh()->content);
    }

    public function test_tabbed_code_requires_a_language_for_each_tab(): void
    {
        $role = Role::factory()->create(['name' => 'admin']);
        $author = User::factory()->create(['role_id' => $role->id]);
        $category = Category::factory()->create();

        $response = $this->actingAs($author, 'sanctum')->postJson(route('posts.store'), [
            'cover_image' => 'https://example.test/cover.png',
            'title' => 'Abstract Factory',
            'excerpt' => 'Um exemplo de padrão de projeto.',
            'content' => [
                [
                    'type' => 'code_tabs',
                    'tabs' => [
                        ['label' => 'PHP', 'code' => 'echo "Olá";'],
                    ],
                ],
            ],
            'status' => 'draft',
            'category_ids' => [$category->id],
        ]);

        $response->assertStatus(422);
    }
}
