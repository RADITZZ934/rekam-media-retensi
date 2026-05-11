<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class LiteLLMClient
{
    protected $baseUrl;
    protected $apiKey;
    protected $model;

    public function __construct()
    {
        $this->baseUrl = env('LITELLM_BASE_URL', 'http://localhost:4000');
        $this->apiKey = env('LITELLM_API_KEY', 'sk-anything');
        $this->model = env('LITELLM_MODEL', 'gemini/gemini-1.5-flash');
    }

    /**
     * Send a prompt to LiteLLM Proxy via Chat Completions API (OpenAI-compatible).
     */
    public function chat(array $messages, array $options = [])
    {
        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->apiKey,
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
            ])->timeout(60)->post($this->baseUrl . '/v1/chat/completions', array_merge([
                'model' => $this->model,
                'messages' => $messages,
                'temperature' => 0,
            ], $options));

            if ($response->successful()) {
                return $response->json();
            }

            Log::error('LiteLLM Proxy Error: ' . $response->status() . ' - ' . $response->body());
            throw new \Exception('LiteLLM Proxy Error: ' . $response->status() . ' - ' . $response->body());

        } catch (\Exception $e) {
            Log::error('LiteLLM Proxy Exception: ' . $e->getMessage());
            throw $e;
        }
    }
}
