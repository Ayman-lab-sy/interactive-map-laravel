<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Model>
 */
class BlogPostFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        $saveFolder = "blog-posts/October2023";
        $folder="storage/app/public/".$saveFolder;
        $image = fake()->image($folder);
        $img = explode('/', $image);
        $i = explode('\\', $img[4]);
        $img = $img[3]."/".$i[0]."/".$i[1];

        return [
            'title' => fake()->sentence(),
            'desc' => fake()->paragraph(),
            'image' => $img,
            'content' => fake()->paragraphs(6, true)
        ];
    }
}
