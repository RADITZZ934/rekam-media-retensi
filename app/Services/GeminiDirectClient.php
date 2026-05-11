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
        $this->primaryModel = env('GEMINI_MODEL', 'gemini-1.5-flash');
        $this->baseUrl = 'https://generativelanguage.googleapis.com/v1beta';
        $this->engine = env('AI_ENGINE', 'direct'); // 'direct' atau 'litellm'
        $this->litellmUrl = env('LITELLM_URL', 'http://localhost:4000/v1/chat/completions');

        $this->fallbackModels = [
            'gemini-1.5-flash',
            'gemini-1.5-flash-latest',
            'gemini-1.5-pro',
            'gemini-2.0-flash-exp',
        ];
    }

    /**
     * Ekstraksi Gambar (OCR Vision)
     */
    public function visionExtract(string $base64Image, string $mimeType, string $prompt, ?string $systemInstruction = null): string
    {
        if ($this->engine === 'litellm') {
            return $this->callLiteLlmVision($base64Image, $mimeType, $prompt, $systemInstruction);
        }

        // Logic Direct Google (seperti sebelumnya)
        $modelsToTry = array_unique([$this->primaryModel, ...$this->fallbackModels]);
        foreach ($modelsToTry as $model) {
            try {
                $url = "{$this->baseUrl}/models/{$model}:generateContent?key={$this->apiKey}";
                $body = [
                    'contents' => [['parts' => [['text' => $prompt], ['inlineData' => ['mimeType' => $mimeType, 'data' => $base64Image]]]]],
                    'generationConfig' => ['temperature' => 0.1, 'maxOutputTokens' => 4096]
                ];
                if ($systemInstruction)
                    $body['systemInstruction'] = ['parts' => [['text' => $systemInstruction]]];

                $response = Http::withHeaders(['Content-Type' => 'application/json'])->timeout(120)->post($url, $body);

                if ($response->successful()) {
                    $this->lastUsedModel = $model;
                    return $response->json()['candidates'][0]['content']['parts'][0]['text'] ?? '';
                }

                Log::warning("Direct Model {$model} failed with status {$response->status()}. Body: " . $response->body());
                if ($response->status() != 429)
                    continue; // Try next model

            } catch (\Exception $e) {
                Log::warning("Direct Model {$model} exception: " . $e->getMessage());
            }
        }
        throw new \Exception("Semua model Gemini Direct gagal. Pastikan API KEY valid dan model tersedia.");
    }

    /**
     * Chat Biasa (Teks)
     */
    public function chat(string $prompt, ?string $systemInstruction = null): string
    {
        if ($this->engine === 'litellm') {
            return $this->callLiteLlmChat($prompt, $systemInstruction);
        }

        // Logic Direct
        $modelsToTry = array_unique([$this->primaryModel, ...$this->fallbackModels]);
        foreach ($modelsToTry as $model) {
            try {
                $url = "{$this->baseUrl}/models/{$model}:generateContent?key={$this->apiKey}";
                $body = [
                    'contents' => [['parts' => [['text' => $prompt]]]],
                    'generationConfig' => ['temperature' => 0.1, 'maxOutputTokens' => 4096]
                ];
                if ($systemInstruction)
                    $body['systemInstruction'] = ['parts' => [['text' => $systemInstruction]]];

                $response = Http::withHeaders(['Content-Type' => 'application/json'])->timeout(60)->post($url, $body);
                if ($response->successful()) {
                    $this->lastUsedModel = $model;
                    return $response->json()['candidates'][0]['content']['parts'][0]['text'] ?? '';
                }
                Log::warning("Direct Chat Model {$model} failed: " . $response->status());
            } catch (\Exception $e) {
                Log::warning("Direct Chat Model {$model} exception: " . $e->getMessage());
            }
        }
        throw new \Exception("Chat Direct Gagal.");
    }

    /**
     * Chat Streaming (untuk SSE)
     */
    public function streamChat(string $prompt, ?string $systemInstruction = null)
    {
        $model = $this->primaryModel;
        $url = "{$this->baseUrl}/models/{$model}:streamGenerateContent?alt=sse&key={$this->apiKey}";

        $body = [
            'contents' => [['parts' => [['text' => $prompt]]]],
            'generationConfig' => ['temperature' => 0.7, 'maxOutputTokens' => 4096]
        ];

        if ($systemInstruction) {
            $body['systemInstruction'] = ['parts' => [['text' => $systemInstruction]]];
        }

        return Http::withOptions(['stream' => true]) // CRITICAL: This enables real-time stream proxying
            ->withHeaders(['Content-Type' => 'application/json'])
            ->timeout(120)
            ->post($url, $body);
    }

    /**
     * Internal: Panggil LiteLLM (Format OpenAI) untuk Vision
     */
    private function callLiteLlmVision($base64Image, $mimeType, $prompt, $systemInstruction)
    {
        Log::info("LiteLLM: Calling Vision Proxy...");

        $messages = [];
        if ($systemInstruction)
            $messages[] = ['role' => 'system', 'content' => $systemInstruction];

        $messages[] = [
            'role' => 'user',
            'content' => [
                ['type' => 'text', 'text' => $prompt],
                ['type' => 'image_url', 'image_url' => ['url' => "data:{$mimeType};base64,{$base64Image}"]]
            ]
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
