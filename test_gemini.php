<?php

$apiKey = 'AIzaSyBA4rP4synri21QXajab8NUOWTI2E5rwRk';

// Test semua kombinasi: v1 vs v1beta, dan berbagai model
$tests = [
    // v1beta models
    ['v1beta', 'gemini-2.0-flash'],
    ['v1beta', 'gemini-2.0-flash-lite'],
    ['v1beta', 'gemini-2.5-flash-preview-04-17'],
    ['v1beta', 'gemini-1.5-flash-latest'],
    ['v1beta', 'gemini-1.5-flash-8b'],
    // v1 models
    ['v1', 'gemini-2.0-flash'],  
    ['v1', 'gemini-1.5-flash'],
    ['v1', 'gemini-1.5-flash-latest'],
];

foreach ($tests as [$version, $model]) {
    echo "Testing {$version}/{$model}... ";
    
    $url = "https://generativelanguage.googleapis.com/{$version}/models/{$model}:generateContent?key={$apiKey}";
    
    $data = json_encode([
        'contents' => [
            ['parts' => [['text' => 'Say hello']]]
        ]
    ]);
    
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
    curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
    curl_setopt($ch, CURLOPT_TIMEOUT, 15);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $error = curl_error($ch);
    curl_close($ch);
    
    if ($error) {
        echo "CURL ERROR: {$error}\n";
        continue;
    }
    
    if ($httpCode === 200) {
        $json = json_decode($response, true);
        $text = $json['candidates'][0]['content']['parts'][0]['text'] ?? 'No text';
        echo "✅ SUCCESS! -> " . substr(trim($text), 0, 60) . "\n";
    } else {
        $json = json_decode($response, true);
        $msg = $json['error']['message'] ?? 'Unknown error';
        echo "❌ [{$httpCode}]: " . substr($msg, 0, 80) . "\n";
    }
}
