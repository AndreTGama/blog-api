<?php

namespace Database\Factories;

use App\Models\Post;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Post>
 */
class PostFactory extends Factory
{

    protected $model = Post::class;
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'cover_image' => $this->faker->imageUrl(640, 480, 'posts', true),
            'locale' => 'pt',
            'translation_group_id' => (string) Str::uuid(),
            'title' => $this->faker->sentence(),
            'slug' => $this->faker->slug(),
            'excerpt' => $this->faker->paragraph(),
            'content' => $this->faker->text(500),
            'status' => $this->faker->randomElement(['draft', 'published']),
            'views' => $this->faker->numberBetween(0, 1000),
            'published_at' => $this->faker->dateTimeBetween('-1 year', 'now'),
            'author_id' => User::factory()
        ];
    }
}
