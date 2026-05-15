<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Default AI Provider
    |--------------------------------------------------------------------------
    |
    | Supported: 'demo', 'ollama'
    |
    | 'demo'    - generates content from templates, no external API needed
    | 'ollama'  - local AI via Ollama (requires ollama serve + model pulled)
    |
    */

    'default' => env('AI_PROVIDER', 'demo'),

    'providers' => [

        'demo' => [
            'driver' => \App\Services\AI\DemoAIService::class,
        ],

        'ollama' => [
            'driver'   => \App\Services\AI\OllamaService::class,
            'base_url' => env('OLLAMA_BASE_URL', 'http://localhost:11434'),
            'model'    => env('OLLAMA_MODEL', 'llama3.2'),
        ],

    ],

];
