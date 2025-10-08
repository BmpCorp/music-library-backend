<?php

namespace App\Services;

use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Http;

class OpenRouterService
{
    private const API_URL = 'https://openrouter.ai/api/v1/';

    /** @var int in seconds */
    private const API_TIMEOUT = 300;

    /**
     * @see https://openrouter.ai/docs/api-reference/chat-completion
     * @see https://openrouter.ai/docs/api-reference/overview#completions-request-format
     */
    public function request(string $prompt, bool $asJson = false): ?string
    {
        if (!$prompt) {
            return null;
        }

        $apiKey = config('services.openrouter.key');
        $modelName = config('services.openrouter.model');

        if (!$apiKey || !$modelName) {
            logger()->warning('OpenRouter model and/or API key are not defined');
            return null;
        }

        $params = [
            'model' => $modelName,
            'prompt' => $prompt,
            'reasoning' => [
                'exclude' => true,
            ],
        ];
        if ($asJson) {
            $params['response_format'] = [
                'type' => 'json_object',
            ];
        }

        try {
            $response = Http::withToken($apiKey)
                ->asJson()
                ->timeout(self::API_TIMEOUT)
                ->post(self::API_URL . 'chat/completions', $params);
        } catch (ConnectionException $exception) {
            logger()->warning("Error on executing OpenRouter request: {$exception->getMessage()}");
            return null;
        }

        if ($response->failed()) {
            logger()->warning("OpenRouter request failed: {$response->getStatusCode()} {$response->getReasonPhrase()}");
            return null;
        }

        return $response['choices'][0]['text'] ?? null;
    }
}
