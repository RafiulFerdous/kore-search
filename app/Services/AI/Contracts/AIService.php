<?php

namespace App\Services\AI\Contracts;

interface AIService
{
    public function suggestDescription(string $title, ?string $category = null): string;

    public function suggestTopics(string $title, ?string $category = null): array;

    public function suggestDetails(string $title, ?string $category = null): array;
}
