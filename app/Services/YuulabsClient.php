<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class YuulabsClient
{
    protected $baseUrl;
    protected $apiKey;

    public function __construct()
    {
        $this->baseUrl = rtrim(config('services.yuulabs.base_url', 'https://api.yuulabs.web.id/api'), '/');
        $this->apiKey = config('services.yuulabs.api_key');
    }

    /**
     * ChatGPT Vision via Yuulabs Proxy
     */
    public function chatgptVision(string $filePath, string $prompt, string $model = 'gpt-4o')
    {
        try {
            $content = file_get_contents($filePath);
            $filename = basename($filePath);

            $request = Http::timeout(180);
            if ($this->apiKey && trim($this->apiKey) !== '') {
                $request = $request->withToken($this->apiKey);
            }

            $response = $request->attach('image', $content, $filename)
                ->post($this->baseUrl . '/ai/chatgpt', [
                    'message' => $prompt,
                    'model' => $model,
                    'stream' => false,
                ]);

            if ($response->successful()) {
                $res = $response->json();
                
                // DEBUG: Cetak seluruh respon untuk melihat strukturnya
                Log::info("Yuulabs RAW Response: " . json_encode($res));

                // Cek struktur result.text (seperti di playground)
                if (isset($res['result']['text'])) {
                    return $res['result']['text'];
                }
                
                // ChatGPT structure usually has 'result' or 'message'
                return $res['result'] ?? $res['message'] ?? $res['text'] ?? json_encode($res);
            }

            Log::error('Yuulabs ChatGPT Vision Error: ' . $response->status() . ' - ' . $response->body());
            throw new \Exception('Yuulabs ChatGPT Vision Error: ' . $response->status());

        } catch (\Exception $e) {
            Log::error('Yuulabs ChatGPT Vision Exception: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Gemini Vision via Yuulabs Proxy
     */
    public function geminiVision(string $filePath, string $prompt, ?string $systemInstruction = null)
    {
        try {
            $content = file_get_contents($filePath);
            $filename = basename($filePath);

            $request = Http::timeout(180); // Tambahkan timeout lebih lama
            if ($this->apiKey && trim($this->apiKey) !== '') {
                $request = $request->withToken($this->apiKey);
            }

            // Gabungkan system instruction ke dalam prompt utama jika ada
            // agar kompatibel dengan semua jenis proxy
            $finalPrompt = $prompt;
            if ($systemInstruction) {
                $finalPrompt = "INTRUKSI SISTEM:\n" . $systemInstruction . "\n\n" . "PERINTAH USER:\n" . $prompt;
            }

            $base64 = base64_encode($content);
            $mime = mime_content_type($filePath);

            $response = $request->post($this->baseUrl . '/ai/gemini', [
                'message' => $finalPrompt,
                'image' => "data:{$mime};base64,{$base64}",
                'stream' => false,
            ]);

            if ($response->successful()) {
                $res = $response->json();
                
                // Cek struktur result.text (seperti di playground)
                if (isset($res['result']['text'])) {
                    return $res['result']['text'];
                }
                
                // Fallback ke field lain
                return $res['result'] ?? $res['message'] ?? $res['text'] ?? json_encode($res);
            }

            Log::error('Yuulabs Gemini API Error: ' . $response->status() . ' - ' . $response->body());
            throw new \Exception('Yuulabs Gemini API Error: ' . $response->status() . ' - ' . $response->body());

        } catch (\Exception $e) {
            Log::error('Yuulabs Gemini Exception: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * General Chat
     */
    public function chat(string $prompt, string $endpoint = 'chatgpt', array $options = [])
    {
        try {
            $payload = array_merge([
                'message' => $prompt,
                'stream' => 'false',
            ], $options);

            $request = Http::timeout(60);

            if ($this->apiKey && trim($this->apiKey) !== '') {
                $request = $request->withToken($this->apiKey);
            }

            $response = $request->post($this->baseUrl . '/ai/' . $endpoint, $payload);

            if ($response->successful()) {
                $res = $response->json();
                
                // Cek struktur result.text
                if (isset($res['result']['text'])) {
                    return $res['result']['text'];
                }
                
                return $res['result'] ?? $res['message'] ?? $res['text'] ?? json_encode($res);
            }

            throw new \Exception('Yuulabs Chat Error: ' . $response->status());

        } catch (\Exception $e) {
            Log::error('Yuulabs Chat Exception: ' . $e->getMessage());
            throw $e;
        }
    }
}
