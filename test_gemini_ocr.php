<?php

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Services\GeminiDirectClient;
use Illuminate\Support\Facades\Log;

try {
    echo "Starting Gemini OCR Test...\n";
    $client = new GeminiDirectClient();
    
    // Create a dummy image
    $imgPath = storage_path('app/private/dummy_test_gemini.png');
    if (!file_exists($imgPath)) {
        if (!file_exists(dirname($imgPath))) {
            mkdir(dirname($imgPath), 0755, true);
        }
        // create a 100x100 white png with some text-like box
        $img = imagecreatetruecolor(200, 200);
        $white = imagecolorallocate($img, 255, 255, 255);
        imagefill($img, 0, 0, $white);
        
        $black = imagecolorallocate($img, 0, 0, 0);
        imagestring($img, 5, 20, 20, "Nama: Budi Santoso", $black);
        imagestring($img, 5, 20, 50, "No RM: 123456", $black);
        imagestring($img, 5, 20, 80, "Diagnosis: Vertigo", $black);
        
        imagepng($img, $imgPath);
        imagedestroy($img);
    }
    
    echo "Processing OCR with Gemini...\n";
    $mime = mime_content_type($imgPath);
    $data = base64_encode(file_get_contents($imgPath));
    
    $prompt = "Tolong ekstrak data rekam medis dari gambar ini menjadi JSON murni (nomor_rm, nama_pasien, diagnosis).";
    
    $response = $client->visionExtract($data, $mime, $prompt);
    
    echo "Gemini Response:\n";
    echo $response . "\n";
    
    echo "\nTest Finished.\n";

} catch (\Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
