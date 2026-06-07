<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * Client untuk memanggil AI (Gemini Direct atau LiteLLM Proxy).
 * Mendukung format native Google Gemini dan format OpenAI (untuk LiteLLM).
 */
class GeminiDirectClient
{
    protected string $apiKey;
    protected string $primaryModel;
    protected string $baseUrl;
    protected string $engine;
    protected string $litellmUrl;
    protected array $fallbackModels;

    public ?string $lastUsedModel = null;

    public function __construct()
    {
        $this->apiKey = env('GEMINI_API_KEY', '');
        $this->primaryModel = env('GEMINI_MODEL', 'gemini-2.5-flash');
        $this->baseUrl = 'https://generativelanguage.googleapis.com/v1beta';
        $this->engine = env('AI_ENGINE', 'direct'); // 'direct' atau 'litellm'
        $this->litellmUrl = env('LITELLM_URL', 'http://localhost:4000/v1/chat/completions');

        $this->fallbackModels = [
            'gemini-2.5-flash',
            'gemini-2.0-flash',
            'gemini-3.5-flash',
            'gemini-2.5-pro',
            'gemini-3-flash-preview',
            'gemini-1.5-flash',
        ];
    }

    /**
     * Get list of API keys from env config.
     */
    protected function getApiKeyList(): array
    {
        $keysStr = env('GEMINI_API_KEYS', '');
        if (!empty($keysStr)) {
            $keys = array_filter(array_map('trim', explode(',', $keysStr)));
            if (!empty($keys)) {
                return array_values($keys);
            }
        }
        
        $singleKey = env('GEMINI_API_KEY', '');
        return !empty($singleKey) ? [$singleKey] : [];
    }

    /**
     * Get next API key in a round-robin rotation.
     */
    protected function getNextApiKey(): string
    {
        $keys = $this->getApiKeyList();
        if (empty($keys)) {
            throw new \Exception("No Gemini API keys configured. Set GEMINI_API_KEY or GEMINI_API_KEYS in .env");
        }
        
        // Use cache to track the index across requests
        $index = \Illuminate\Support\Facades\Cache::get('gemini_api_key_index', 0);
        $key = $keys[$index % count($keys)];
        
        // Advance index
        \Illuminate\Support\Facades\Cache::put('gemini_api_key_index', ($index + 1) % count($keys), 3600);
        
        return $key;
    }

    /**
     * Ekstraksi Gambar (OCR Vision) - Mendukung single base64 string atau array dari base64 images
     */
    public function visionExtract(string|array $images, string $mimeType, string $prompt, ?string $systemInstruction = null): string
    {
        if ($this->engine === 'litellm') {
            return $this->callLiteLlmVision($images, $mimeType, $prompt, $systemInstruction);
        }

        $keys = $this->getApiKeyList();
        if (empty($keys)) {
            throw new \Exception("No Gemini API keys configured.");
        }

        // Map inputs to standard format: list of ['mimeType' => ..., 'data' => ...]
        $imgList = [];
        if (is_string($images)) {
            $imgList[] = [
                'mimeType' => $mimeType,
                'data' => $images
            ];
        } else {
            $imgList = $images;
        }

        $modelsToTry = array_unique([$this->primaryModel, ...$this->fallbackModels]);
        $attempts = count($keys);

        for ($i = 0; $i < $attempts; $i++) {
            $apiKey = $this->getNextApiKey();

            foreach ($modelsToTry as $model) {
                try {
                    $url = "{$this->baseUrl}/models/{$model}:generateContent?key={$apiKey}";
                    
                    // Build parts array containing the text prompt and all images
                    $parts = [['text' => $prompt]];
                    foreach ($imgList as $img) {
                        $parts[] = [
                            'inlineData' => [
                                'mimeType' => $img['mimeType'],
                                'data' => $img['data']
                            ]
                        ];
                    }

                    $body = [
                        'contents' => [['parts' => $parts]],
                        'generationConfig' => ['temperature' => 0.1, 'maxOutputTokens' => 4096]
                    ];
                    if ($systemInstruction) {
                        $body['systemInstruction'] = ['parts' => [['text' => $systemInstruction]]];
                    }

                    Log::info("Gemini visionExtract: Trying key index " . (\Illuminate\Support\Facades\Cache::get('gemini_api_key_index', 0)) . " with model {$model} (" . count($imgList) . " images)");
                    $response = Http::withHeaders(['Content-Type' => 'application/json'])->timeout(120)->post($url, $body);

                    if ($response->successful()) {
                        $this->lastUsedModel = $model;
                        return $response->json()['candidates'][0]['content']['parts'][0]['text'] ?? '';
                    }

                    Log::warning("Direct Model {$model} failed with status {$response->status()}. Body: " . $response->body());
                    if ($response->status() == 429 || $response->status() == 400 || $response->status() == 403) {
                        // Rate limit or auth error with this key, break to try the next key
                        break;
                    }
                } catch (\Exception $e) {
                    Log::warning("Direct Model {$model} exception: " . $e->getMessage());
                }
            }
        }
        
        throw new \Exception("Semua API Key Gemini Direct gagal. Pastikan API KEY valid dan model tersedia.");
    }

    /**
     * Chat Biasa (Teks)
     */
    public function chat(string $prompt, ?string $systemInstruction = null): string
    {
        if ($this->engine === 'litellm') {
            return $this->callLiteLlmChat($prompt, $systemInstruction);
        }

        $keys = $this->getApiKeyList();
        if (empty($keys)) {
            throw new \Exception("No Gemini API keys configured.");
        }

        $modelsToTry = array_unique([$this->primaryModel, ...$this->fallbackModels]);
        $attempts = count($keys);

        for ($i = 0; $i < $attempts; $i++) {
            $apiKey = $this->getNextApiKey();

            foreach ($modelsToTry as $model) {
                try {
                    $url = "{$this->baseUrl}/models/{$model}:generateContent?key={$apiKey}";
                    $body = [
                        'contents' => [['parts' => [['text' => $prompt]]]],
                        'generationConfig' => ['temperature' => 0.1, 'maxOutputTokens' => 4096]
                    ];
                    if ($systemInstruction) {
                        $body['systemInstruction'] = ['parts' => [['text' => $systemInstruction]]];
                    }

                    Log::info("Gemini chat: Trying key index with model {$model}");
                    $response = Http::withHeaders(['Content-Type' => 'application/json'])->timeout(60)->post($url, $body);
                    if ($response->successful()) {
                        $this->lastUsedModel = $model;
                        return $response->json()['candidates'][0]['content']['parts'][0]['text'] ?? '';
                    }
                    
                    Log::warning("Direct Chat Model {$model} failed: " . $response->status());
                    if ($response->status() == 429 || $response->status() == 400 || $response->status() == 403) {
                        break;
                    }
                } catch (\Exception $e) {
                    Log::warning("Direct Chat Model {$model} exception: " . $e->getMessage());
                }
            }
        }
        
        throw new \Exception("Chat Direct Gagal menggunakan semua API Key.");
    }

    /**
     * Chat Streaming (untuk SSE)
     */
    public function streamChat(string $prompt, ?string $systemInstruction = null)
    {
        $keys = $this->getApiKeyList();
        if (empty($keys)) {
            throw new \Exception("No Gemini API keys configured.");
        }

        $attempts = count($keys);
        $model = $this->primaryModel;

        for ($i = 0; $i < $attempts; $i++) {
            $apiKey = $this->getNextApiKey();
            $url = "{$this->baseUrl}/models/{$model}:streamGenerateContent?alt=sse&key={$apiKey}";

            $body = [
                'contents' => [['parts' => [['text' => $prompt]]]],
                'generationConfig' => ['temperature' => 0.7, 'maxOutputTokens' => 4096]
            ];

            if ($systemInstruction) {
                $body['systemInstruction'] = ['parts' => [['text' => $systemInstruction]]];
            }

            try {
                $response = Http::withOptions(['stream' => true])
                    ->withHeaders(['Content-Type' => 'application/json'])
                    ->timeout(120)
                    ->post($url, $body);

                if ($response->successful()) {
                    return $response;
                }

                Log::warning("Gemini streamChat key failed: " . $response->status());
            } catch (\Exception $e) {
                Log::warning("Gemini streamChat exception: " . $e->getMessage());
            }
        }

        throw new \Exception("Semua API Key Gemini gagal untuk Stream Chat.");
    }

    /**
     * Internal: Panggil LiteLLM (Format OpenAI) untuk Vision
     */
    private function callLiteLlmVision($images, $mimeType, $prompt, $systemInstruction)
    {
        Log::info("LiteLLM: Calling Vision Proxy...");

        $imgList = [];
        if (is_string($images)) {
            $imgList[] = [
                'mimeType' => $mimeType,
                'data' => $images
            ];
        } else {
            $imgList = $images;
        }

        $contentParts = [['type' => 'text', 'text' => $prompt]];
        foreach ($imgList as $img) {
            $contentParts[] = [
                'type' => 'image_url',
                'image_url' => [
                    'url' => "data:{$img['mimeType']};base64,{$img['data']}"
                ]
            ];
        }

        $messages = [];
        if ($systemInstruction)
            $messages[] = ['role' => 'system', 'content' => $systemInstruction];

        $messages[] = [
            'role' => 'user',
            'content' => $contentParts
        ];

        return $this->postToLiteLlm($messages);
    }

    /**
     * Internal: Panggil LiteLLM (Format OpenAI) untuk Chat
     */
    private function callLiteLlmChat($prompt, $systemInstruction)
    {
        Log::info("LiteLLM: Calling Chat Proxy...");

        $messages = [];
        if ($systemInstruction)
            $messages[] = ['role' => 'system', 'content' => $systemInstruction];
        $messages[] = ['role' => 'user', 'content' => $prompt];

        return $this->postToLiteLlm($messages);
    }

    private function postToLiteLlm($messages)
    {
        try {
            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
                'Authorization' => 'Bearer ' . $this->apiKey,
            ])->timeout(120)->post($this->litellmUrl, [
                        'model' => $this->primaryModel,
                        'messages' => $messages,
                        'temperature' => 0.1
                    ]);

            if (!$response->successful()) {
                throw new \Exception("LiteLLM Error [{$response->status()}]: " . $response->body());
            }

            return $response->json()['choices'][0]['message']['content'] ?? '';
        } catch (\Exception $e) {
            Log::error("LiteLLM Request Failed: " . $e->getMessage());
            throw $e;
        }
    }
}
