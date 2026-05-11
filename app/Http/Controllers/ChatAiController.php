<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ChatAiController extends Controller
{
    /**
     * Proxy chat request to YuuLabs ChatAI endpoint.
     */
    public function chat(Request $request)
    {
        $request->validate([
            'message' => 'required|string|max:2000',
        ]);

        $message = $request->input('message');

        // Use Gemini for REAL streaming
        $gemini = new \App\Services\GeminiDirectClient();

        return response()->stream(function () use ($gemini, $message) {
            try {
                $response = $gemini->streamChat($message, "Anda adalah AI Assistant RSUK yang ahli dalam rekam medis dan retensi dokumen. Berikan jawaban yang profesional, akurat, dan membantu.");

                if (!$response->successful()) {
                    echo "data: " . json_encode(['error' => 'API Gagal: ' . $response->status()]) . "\n\n";
                    return;
                }

                $body = $response->toPsrResponse()->getBody();

                while (!$body->eof()) {
                    $chunk = $body->read(512); // Smaller chunks for better responsiveness
                    echo $chunk;

                    if (ob_get_level() > 0)
                        ob_flush();
                    flush();
                }
            } catch (\Exception $e) {
                echo "data: " . json_encode(['error' => 'Stream Error: ' . $e->getMessage()]) . "\n\n";
            }
        }, 200, [
            'Cache-Control' => 'no-cache',
            'Content-Type' => 'text/event-stream',
            'X-Accel-Buffering' => 'no',
        ]);
    }

    /**
     * Get available AI characters/models list.
     */
    public function characters()
    {
        // Hardcoded characters for now - can be fetched from API later
        $characters = [
            ['id' => 'unlimited', 'name' => 'AI Assistant', 'description' => 'Asisten pintar serba bisa', 'avatar' => '🤖'],
        ];

        return response()->json([
            'success' => true,
            'data' => $characters,
        ]);
    }
}
