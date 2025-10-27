<?php

namespace Database\Factories;

use App\Models\Article;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Article>
 */
class ArticleFactory extends Factory
{
    protected $model = Article::class;

    /**
     * @return array|mixed[]
     */
    public function definition(): array
    {
        return [
            'title' => $this->faker->sentence(),
            'author' => $this->faker->name(),
            'source' => $this->faker->word(),
            'category' => $this->faker->word(),
            'published_at' => $this->faker->dateTime(),
            'description' => $this->faker->paragraph(),
            'url' => $this->faker->url(),
        ];
    }
}
