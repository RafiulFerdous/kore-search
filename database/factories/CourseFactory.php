<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class CourseFactory extends Factory
{
    private static array $usedSlugs = [];

    public function definition(): array
    {
        $prefixes = ['Complete', 'Mastering', 'Introduction to', 'Advanced', 'Hands-On', 'The Ultimate', 'Modern', 'Practical'];
        $categories = ['Frontend', 'Backend', 'Database', 'DevOps', 'Mobile'];
        $levels = ['beginner', 'intermediate', 'advanced'];
        $level = fake()->randomElement($levels);
        $category = fake()->randomElement($categories);

        $noun = match ($category) {
            'Frontend' => fake()->randomElement(['React.js', 'Vue.js', 'Angular', 'Svelte', 'Next.js', 'TypeScript', 'CSS', 'HTML5', 'Tailwind CSS']),
            'Backend' => fake()->randomElement(['Laravel', 'Node.js', 'Python', 'Django', 'Go', 'Rust', 'PHP', 'Express.js', 'NestJS']),
            'Database' => fake()->randomElement(['MySQL', 'PostgreSQL', 'MongoDB', 'Redis', 'SQLite', 'Elasticsearch']),
            'DevOps' => fake()->randomElement(['Docker', 'Kubernetes', 'AWS', 'CI/CD', 'Terraform', 'Ansible', 'Linux']),
            'Mobile' => fake()->randomElement(['Flutter', 'React Native', 'SwiftUI', 'Kotlin', 'Android', 'iOS']),
        };

        $base = fake()->randomElement($prefixes) . ' ' . $noun;
        $slug = Str::slug($base);
        $i = 2;
        while (in_array($slug, self::$usedSlugs, true)) {
            $slug = Str::slug($base) . '-' . $i++;
        }
        self::$usedSlugs[] = $slug;

        $durations = [
            'beginner' => ['4 hours', '6 hours', '8 hours', '10 hours', '12 hours'],
            'intermediate' => ['8 hours', '10 hours', '12 hours', '14 hours', '16 hours'],
            'advanced' => ['10 hours', '14 hours', '18 hours', '20 hours', '24 hours'],
        ];

        return [
            'title'          => $base . ($i > 2 ? ' ' . ($i - 1) : ''),
            'slug'           => $slug,
            'description'    => fake()->paragraph(3),
            'thumbnail'      => 'https://placehold.co/800x450',
            'category'       => $category,
            'level'          => $level,
            'price'          => fake()->boolean(30) ? 0 : fake()->randomElement([499, 799, 999, 1200, 1500, 1999, 2499, 2999]),
            'is_published'   => true,
            'enrolled_count' => fake()->numberBetween(10, 500),
            'rating'         => fake()->randomFloat(1, 3.5, 5.0),
            'duration'       => fake()->randomElement($durations[$level]),
            'topics'         => [fake()->sentence(3), fake()->sentence(3), fake()->sentence(3), fake()->sentence(3), fake()->sentence(3)],
        ];
    }
}
