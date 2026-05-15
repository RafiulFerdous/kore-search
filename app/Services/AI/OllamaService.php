<?php

namespace App\Services\AI;

use App\Services\AI\Contracts\AIService;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class OllamaService implements AIService
{
    protected string $baseUrl;

    protected string $model;

    public function __construct()
    {
        $this->baseUrl = config('ai.ollama.base_url', 'http://localhost:11434');
        $this->model   = config('ai.ollama.model', 'llama3.2');
    }

    public function suggestDescription(string $title, ?string $category = null): string
    {
        $prompt = "Write a concise 2-3 sentence course description for a course titled \"{$title}\"" .
            ($category ? " in the category \"{$category}\"." : '.') .
            ' The description should be informative and engaging, suitable for a course listing page.';

        return $this->ask($prompt);
    }

    public function suggestTopics(string $title, ?string $category = null): array
    {
        $prompt = "List 5-8 topic names for a course titled \"{$title}\"" .
            ($category ? " in the category \"{$category}\"." : '.') .
            ' Return only the topic names, one per line. Do not number them.';

        $response = $this->ask($prompt);

        return array_filter(array_map('trim', explode("\n", $response)));
    }

    public function suggestDetails(string $title, ?string $category = null): array
    {
        $prompt = "For a course titled \"{$title}\"" .
            ($category ? " in the category \"{$category}\"" : '') .
            ", suggest:\n" .
            "1. A short description (2-3 sentences)\n" .
            "2. 5-8 topic names (one per line, not numbered)\n" .
            "3. A difficulty level (Beginner, Intermediate, or Advanced)\n" .
            "4. A duration estimate (e.g. '8 hours', '12 hours')\n\n" .
            'Format your response as:' . "\n" .
            'DESCRIPTION:' . "\n" .
            '<description>' . "\n\n" .
            'TOPICS:' . "\n" .
            '<topics one per line>' . "\n\n" .
            'LEVEL:' . "\n" .
            '<level>' . "\n\n" .
            'DURATION:' . "\n" .
            '<duration>';

        $response = $this->ask($prompt);

        return $this->parseDetails($response);
    }

    protected function ask(string $prompt): string
    {
        try {
            $response = Http::timeout(60)->post("{$this->baseUrl}/api/generate", [
                'model'  => $this->model,
                'prompt' => $prompt,
                'stream' => false,
            ]);

            if ($response->failed()) {
                Log::warning('Ollama request failed', ['status' => $response->status(), 'body' => $response->body()]);
                return '';
            }

            $data = $response->json();

            return trim($data['response'] ?? '');
        } catch (\Exception $e) {
            Log::error('Ollama request error', ['message' => $e->getMessage()]);
            return '';
        }
    }

    protected function parseDetails(string $response): array
    {
        $result = [
            'description' => '',
            'topics'      => [],
            'level'       => '',
            'duration'    => '',
        ];

        if (preg_match('/DESCRIPTION:\s*\n(.+?)(?=\n\nTOPICS:|\z)/s', $response, $m)) {
            $result['description'] = trim($m[1]);
        }

        if (preg_match('/TOPICS:\s*\n(.+?)(?=\n\nLEVEL:|\z)/s', $response, $m)) {
            $topics = array_filter(array_map('trim', explode("\n", trim($m[1]))));
            $result['topics'] = array_values($topics);
        }

        if (preg_match('/LEVEL:\s*\n(.+?)(?=\n\nDURATION:|\z)/s', $response, $m)) {
            $result['level'] = trim($m[1]);
        }

        if (preg_match('/DURATION:\s*\n(.+)/s', $response, $m)) {
            $result['duration'] = trim($m[1]);
        }

        return $result;
    }
}
