<?php

namespace App\Services\AI;

use App\Services\AI\Contracts\AIService;

class DemoAIService implements AIService
{
    public function suggestDescription(string $title, ?string $category = null): string
    {
        $category = $category ?: 'technology';

        return "Master {$title} with this comprehensive course. You will learn industry-best practices, " .
            "hands-on techniques, and real-world applications in the {$category} space. " .
            'Designed for practical learning with step-by-step guidance from experienced professionals.';
    }

    public function suggestTopics(string $title, ?string $category = null): array
    {
        return [
            "Introduction to {$title}",
            'Core Concepts and Fundamentals',
            'Hands-on Project Work',
            'Best Practices and Patterns',
            'Real-world Applications',
            'Advanced Techniques',
            'Performance Optimization',
            'Next Steps and Resources',
        ];
    }

    public function suggestDetails(string $title, ?string $category = null): array
    {
        $category = $category ?: 'technology';

        $topics = [
            "Getting Started with {$title}",
            'Core Architecture and Design',
            'Building Your First Project',
            'Intermediate Concepts',
            'Testing and Debugging',
            "Advanced {$category} Patterns",
            'Performance Tuning',
            'Deployment and Production',
        ];

        $levels = ['Beginner', 'Intermediate', 'Advanced'];
        $level  = $levels[array_rand($levels)];

        $durations = ['6 hours', '8 hours', '10 hours', '12 hours', '16 hours'];
        $duration  = $durations[array_rand($durations)];

        return [
            'description' => $this->suggestDescription($title, $category),
            'topics'      => $topics,
            'level'       => $level,
            'duration'    => $duration,
        ];
    }
}
