<?php
// Defina o cabeçalho Content-Type como JSON
header('Content-Type: application/json');

// Caminho para o arquivo contendo tokens
$tokenFilePath = dirname(__FILE__) . '/../../config/webhook-token.php';

/*
// Caminho para o arquivo de log
$logFilePath = dirname(__FILE__) . '/../../config/webhook.log';

// Função para gravar logs
function logToFile($message) {
    global $logFilePath;
    $timestamp = date('Y-m-d H:i:s');
    $logMessage = "[$timestamp] $message\n";
    // Verifique se o PHP consegue gravar no arquivo de log
    if (file_put_contents($logFilePath, $logMessage, FILE_APPEND) === false) {
        // Se não conseguir gravar, envie uma resposta para ajudar no diagnóstico
        echo json_encode(['status' => 'error', 'message' => 'Unable to write to log file']);
        exit;
    }
}
*/

// Verifique se o arquivo de tokens existe
if (!file_exists($tokenFilePath)) {
    $errorMessage = 'Token file not found';
    logToFile($errorMessage);
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => $errorMessage]);
    exit;
}

// Inclua o arquivo de tokens
$tokens = include($tokenFilePath);

// Verifique se os tokens foram carregados corretamente
if (!isset($tokens['webhookSecret']) || !isset($tokens['verificationToken'])) {
    $errorMessage = 'Tokens are missing';
    logToFile($errorMessage);
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => $errorMessage]);
    exit;
}

// Defina os tokens
$webhookSecret = $tokens['webhookSecret'];
$verificationToken = $tokens['verificationToken'];

// Lê o corpo da solicitação JSON
$body = file_get_contents('php://input');
logToFile("Received body: $body");

// Decodifica o JSON para um array PHP
$data = json_decode($body, true);

// Verifica se a decodificação foi bem-sucedida
if (json_last_error() !== JSON_ERROR_NONE) {
    $errorMessage = 'Invalid JSON: ' . json_last_error_msg();
    logToFile($errorMessage);
    http_response_code(400);
    echo json_encode(['status' => 'error', 'message' => $errorMessage]);
    exit;
}

// Verifica se o evento é a validação da URL
if (isset($data['event']) && $data['event'] === 'endpoint.url_validation') {
    // Captura o plainToken
    $plainToken = $data['payload']['plainToken'];

    // Cria o hash HMAC SHA-256 do plainToken
    $encryptedToken = hash_hmac('sha256', $plainToken, $webhookSecret);

    // Cria a resposta JSON
    $response = [
        'plainToken' => $plainToken,
        'encryptedToken' => $encryptedToken
    ];

    // Retorna a resposta de sucesso
    logToFile("Response: " . json_encode($response));
    echo json_encode($response);
    http_response_code(200);
    exit;
} else {
    $errorMessage = 'Invalid event: ' . (isset($data['event']) ? $data['event'] : 'none');
    logToFile($errorMessage);
    http_response_code(400);
    echo json_encode(['status' => 'error', 'message' => $errorMessage]);
}
?>
