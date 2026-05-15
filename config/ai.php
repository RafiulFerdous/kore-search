<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Default AI Provider
    |--------------------------------------------------------------------------
    |
    | Supported: 'ollama'
    |
    */

    'default' => env('AI_PROVIDER', 'ollama'),

    'providers' => [

        'ollama' => [
            'driver'  => \App\Services\AI\OllamaService::class,
            'base_url' => env('OLLAMA_BASE_URL', 'http://localhost:11434'),
            'model'    => env('OLLAMA_MODEL', 'llama3.2'),
        ],

    ],

];
