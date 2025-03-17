<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, GET, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');
require_once "../config.php";

$apiKey = getenv('OPENAI_API_KEY');

if (empty($apiKey)) {
    echo json_encode(['error' => 'API key not set']);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

$reqPayload = file_get_contents('php://input');
error_log("Request Payload: " . $reqPayload);

$reqData = json_decode($reqPayload, true);

if ($reqData === null) {
    error_log("Erro ao decodificar JSON: " . json_last_error_msg());
    http_response_code(400);
    echo json_encode(["error" => "Invalid JSON received"]);
    exit();
}

$model = 'gpt-3.5-turbo';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($reqData['message'])) {
    $userMessage = $reqData['message'];
    $apiUrl = 'https://api.openai.com/v1/chat/completions';

    $httpHeaders = [
        'Content-Type: application/json',
        'Authorization: Bearer ' . $apiKey
    ];

    $apiReqtData = json_encode([
        'model' => $model,
        'messages' => [
            ["role" => "user", "content" => $userMessage]
        ],
        'max_tokens' => 150
    ]);

    // Inicia a requisição cURL à OpenAI
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
    error_log("API Response: " . $apiRes);

    $apiResData = json_decode($apiRes, true);

    if (isset($apiResData['choices'][0]['message']['content'])) {
        $botReply = $apiResData['choices'][0]['message']['content'];
    } else {
        http_response_code(500);
        echo json_encode(['error' => 'Invalid response from OpenAI API']);
        exit();
    }

    $resPayload = [
        'reply' => $botReply,
        'raw_response' => $apiResData
    ];

    echo json_encode($resPayload);
} else {
    http_response_code(400);
    echo json_encode(['error' => 'Message not provided']);
}
?>
