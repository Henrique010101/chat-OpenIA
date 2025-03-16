<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, GET, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

require __DIR__ . '/../vendor/autoload.php';

use Dotenv\Dotenv;

$dotenv = Dotenv::createImmutable(__DIR__ . '/../');
$dotenv->load();

$apiKey = getenv('OPENAI_API_KEY');
if ($apiKey != '') {
    echo json_encode(['error' => 'API key not set']);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

$reqPayload = file_get_contents('php://input');
$reqtData = json_decode($reqPayload, true);
$model = 'gpt-3.5-turbo';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($reqtData['message'])) {
    $userMessage = $reqtData['message'];
    $apiUrl = 'https://api.openai.com/v1/completions';
    $httpHeaders = [
        'Content-Type: application/json',
        'Authorization: Bearer ' . $apiKey
    ];
    $apiReqtData = json_encode([
        'model' => $model,
        'prompt' => $userMessage,
        'max_tokens' => 150
    ]);

    $ch = curl_init($apiUrl);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $httpHeaders);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $apiReqtData);
    $apiRes = curl_exec($ch);

    if (curl_errno($ch)) {
        $curlError = curl_error($ch);
        curl_close($ch);
        http_response_code(500);
        echo json_encode(['error' => $curlError]);
        exit();
    }

    curl_close($ch);
    error_log($apiRes);
    $apiResData = json_decode($apiRes, true);

    if (isset($apiResData['choices'][0]['text'])) {
        $botReply = $apiResData['choices'][0]['text'];
    } else {
        http_response_code(500);
        echo json_encode(['error' => 'Invalid response from OpenAI API']);
        exit();
    }

    $resPayload = [
        'reply' => $botReply
    ];

    echo json_encode($resPayload);
} else {
    http_response_code(400);
    echo json_encode(['error' => 'Message not provided']);
}
?>