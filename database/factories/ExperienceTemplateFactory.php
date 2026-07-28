<?php

namespace Database\Factories;

use App\Models\ExperienceTemplate;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<ExperienceTemplate>
 */
class ExperienceTemplateFactory extends Factory
{
    protected $model = ExperienceTemplate::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $name = fake()->sentence(3);

        return [
            'name' => $name,
            'slug' => Str::slug($name).'-'.Str::random(5),
            'description' => fake()->paragraph(),
            'category' => fake()->randomElement(['general', 'physics', 'education', 'biology', 'chemistry']),
            'thumbnail_path' => null,
            'schema' => [
                'components' => [
                    [
                        'type' => 'text',
                        'label' => 'Heading',
                        'default' => [
                            'content' => 'Sample Text',
                            'tag' => 'h2',
                            'fontSize' => 'text-xl',
                            'color' => '#000000',
                            'align' => 'left',
                        ],
                    ],
                ],
            ],
            'default_config' => [
                'components' => [
                    [
                        'id' => '1',
                        'type' => 'text',
                        'label' => 'Heading',
                        'properties' => [
                            'content' => 'Sample Text',
                            'tag' => 'h2',
                            'fontSize' => 'text-xl',
                            'color' => '#000000',
                            'align' => 'left',
                        ],
                    ],
                ],
            ],
            'is_active' => true,
            'sort_order' => 0,
        ];
    }
}
