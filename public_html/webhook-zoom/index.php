<?php
// Defina o cabeçalho Content-Type como JSON
header('Content-Type: application/json');

/*
// Caminho para o arquivo de log
$logFilePath = dirname(__FILE__) . '../../config/webhook.log';

// Função para gravar logs
function logToFile($message) {
    global $logFilePath;
    $timestamp = date('Y-m-d H:i:s');
    $logMessage = "[$timestamp] $message\n";
    file_put_contents($logFilePath, $logMessage, FILE_APPEND);
}
*/

// Inclua o arquivo de conexão com o banco de dados
include dirname(__FILE__) . '../../config/conexao.php';

// Função para conectar ao banco de dados
function getDBConnection() {
    global $dbConfig;
    $dsn = "mysql:host={$dbConfig['host']};dbname={$dbConfig['dbname']};port={$dbConfig['port']}";
    try {
        $pdo = new PDO($dsn, $dbConfig['user'], $dbConfig['password']);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        return $pdo;
    } catch (PDOException $e) {
        logToFile("Database connection failed: " . $e->getMessage());
        http_response_code(500);
        echo json_encode(['status' => 'error', 'message' => 'Database connection failed']);
        exit;
    }
}

// Lê o corpo da solicitação JSON
$body = file_get_contents('php://input');

// Registra o corpo recebido para depuração
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

// Verifica se a estrutura do JSON contém as informações necessárias
if (isset($data[0]['body']['payload']['object']['participant'])) {
    $participant = $data[0]['body']['payload']['object']['participant'];

    // Extrai informações do participante
    $userId = $participant['user_id'];
    $userName = $participant['user_name'];
    $joinTime = $participant['join_time'];
    $startTime = $data[0]['body']['payload']['object']['start_time'];

    // Conecta ao banco de dados
    $pdo = getDBConnection();

    // Prepara a consulta SQL
    $stmt = $pdo->prepare('INSERT INTO novaera_participantes (user_id, user_name, join_time, start_time) VALUES (:user_id, :user_name, :join_time, :start_time)');
    $stmt->bindParam(':user_id', $userId);
    $stmt->bindParam(':user_name', $userName);
    $stmt->bindParam(':join_time', $joinTime);
    $stmt->bindParam(':start_time', $startTime);

    // Executa a consulta
    if ($stmt->execute()) {
        logToFile("Participant saved: User ID = $userId, User Name = $userName, Join Time = $joinTime, Start Time = $startTime");
        echo json_encode(['status' => 'success']);
        http_response_code(200);
    } else {
        logToFile("Failed to save participant: User ID = $userId, User Name = $userName, Join Time = $joinTime, Start Time = $startTime");
        echo json_encode(['status' => 'error', 'message' => 'Failed to save participant']);
        http_response_code(500);
    }
    exit;
} else {
    $errorMessage = 'Invalid data structure';
    logToFile($errorMessage);
    http_response_code(400);
    echo json_encode(['status' => 'error', 'message' => $errorMessage]);
}
?>
