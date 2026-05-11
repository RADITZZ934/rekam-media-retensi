<?php

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Services\YuulabsClient;
use Illuminate\Support\Facades\Log;

try {
    echo "Starting Yuulabs OCR Test...\n";
    $client = new YuulabsClient();

    // Use an existing image
    $imgPath = base_path('dokumen_rs.jpg');
    if (!file_exists($imgPath)) {
        echo "Error: dokumen_rs.jpg not found in root.\n";
        exit(1);
    }

    $prompt = "Kamu adalah sistem OCR ahli. Ekstrak data dari gambar ini menjadi JSON: {nomor_rm, nama_pasien, diagnosis, nama_kasus}.";

    echo "Sending request to Yuulabs API...\n";
    $response = $client->visionExtract($imgPath, $prompt);

    echo "\n--- YUULABS RAW RESPONSE ---\n";
    echo $response . "\n";

    $parsed = null;

    // Try to find markdown JSON block first
    if (preg_match('/```json\s*(\{.*?\})\s*```/s', $response, $matches)) {
        $parsed = json_decode($matches[1], true);
        if ($parsed)
            echo "Found valid JSON in markdown block.\n";
    }

    // If not found or invalid, try to find any JSON-like structure at the end of the response
    if (!$parsed && preg_match_all('/\{(?:[^{}]|(?R))*\}/s', $response, $matches)) {
        $matches = $matches[0];
        for ($i = count($matches) - 1; $i >= 0; $i--) {
            $data = json_decode($matches[$i], true);
            if ($data) {
                $parsed = $data;
                echo "Successfully extracted JSON from match #$i.\n";
                break;
            }
        }
    }

    if ($parsed) {
        echo "\n--- PARSED DATA ---\n";
        print_r($parsed);
    } else {
        echo "\nFailed to find valid JSON structure in response.\n";
    }

    echo "\nTest Finished.\n";

} catch (\Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
