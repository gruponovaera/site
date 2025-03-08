<?php
/**
 * Script de validação do webhook do Zoom
 * 
 * Este script é responsável por validar as solicitações de webhook do Zoom.
 * Quando o Zoom configura um novo webhook, ele envia uma solicitação de validação
 * que deve ser respondida com um token criptografado específico.
 */

// Define o cabeçalho Content-Type como JSON para todas as respostas
header('Content-Type: application/json');

// Caminho para o arquivo contendo tokens de segurança
// Corrigido o caminho que estava incorreto (faltava uma barra)
$tokenFilePath = dirname(__FILE__) . '/../config/webhook-token.php';

/**
 * Código de log comentado - pode ser descomentado para depuração
 */
/*
// Caminho para o arquivo de log
$logFilePath = dirname(__FILE__) . '/../config/webhook.log';

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

// Função temporária de log (não faz nada, apenas para evitar erros)
function logToFile($message) {
    // Função vazia para substituir as chamadas de log comentadas
    // Remova esta função se desativar os logs permanentemente
}

// Verifica se o arquivo de tokens existe
if (!file_exists($tokenFilePath)) {
    $errorMessage = 'Token file not found';
    logToFile($errorMessage);
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => $errorMessage]);
    exit;
}

// Inclui o arquivo de tokens
$tokens = include($tokenFilePath);

// Verifica se os tokens foram carregados corretamente
if (!isset($tokens['webhookSecret']) || !isset($tokens['verificationToken'])) {
    $errorMessage = 'Tokens are missing';
    logToFile($errorMessage);
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => $errorMessage]);
    exit;
}

// Define os tokens a partir do arquivo carregado
$webhookSecret = $tokens['webhookSecret'];
$verificationToken = $tokens['verificationToken'];

// Lê o corpo da solicitação JSON enviada pelo Zoom
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

// Verifica se o evento é a validação da URL do webhook
if (isset($data['event']) && $data['event'] === 'endpoint.url_validation') {
    // Captura o plainToken enviado pelo Zoom
    $plainToken = $data['payload']['plainToken'];

    // Cria o hash HMAC SHA-256 do plainToken usando o webhookSecret como chave
    $encryptedToken = hash_hmac('sha256', $plainToken, $webhookSecret);

    // Cria a resposta JSON no formato esperado pelo Zoom
    $response = [
        'plainToken' => $plainToken,
        'encryptedToken' => $encryptedToken
    ];

    // Retorna a resposta de sucesso com código HTTP 200
    logToFile("Response: " . json_encode($response));
    echo json_encode($response);
    http_response_code(200);
    exit;
} else {
    // Se não for um evento de validação, retorna erro
    $errorMessage = 'Invalid event: ' . (isset($data['event']) ? $data['event'] : 'none');
    logToFile($errorMessage);
    http_response_code(400);
    echo json_encode(['status' => 'error', 'message' => $errorMessage]);
}
?>
