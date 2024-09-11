<?php
// Caminho para o arquivo de log
// $logFile = '../../config/webhook.log';

// Caminho para o arquivo de configuração tokens e conexão com o banco de dados
include '../../config/conexao.php';

// Conexão com o banco de dados
$mysqli = new mysqli($dbConfig['host'], $dbConfig['user'], $dbConfig['password'], $dbConfig['dbname'], $dbConfig['port']);

// Verifica se a conexão foi estabelecida
if ($mysqli->connect_error) {
    if (isset($logFile)) { // Verifica se o log está configurado
        $logMessage = date('Y-m-d H:i:s') . " - Erro de conexão: " . $mysqli->connect_error . PHP_EOL;
        file_put_contents($logFile, $logMessage, FILE_APPEND);
    }
    die("Erro de conexão com o banco de dados: " . $mysqli->connect_error);
}

// Lê o corpo da requisição
$input = file_get_contents('php://input');

// Salva o payload bruto no log, se configurado
if (isset($logFile)) { // Verifica se o log está configurado
    $logMessage = date('Y-m-d H:i:s') . " - Payload recebido: " . $input . PHP_EOL;
    file_put_contents($logFile, $logMessage, FILE_APPEND);
}

// Decodifica o JSON recebido
$data = json_decode($input, true);
if (json_last_error() === JSON_ERROR_NONE) {
    // Extração dos dados do JSON
    $email = $data['payload']['object']['participant']['email'] ?? null;
    $userName = $data['payload']['object']['participant']['user_name'] ?? '';
    $joinTime = $data['payload']['object']['participant']['join_time'] ?? '';
    $startTime = $data['payload']['object']['start_time'] ?? '';

    // Converte as datas para o formato correto
    $joinTimeFormatted = date('Y-m-d H:i:s', strtotime($joinTime));
    $startTimeFormatted = date('Y-m-d H:i:s', strtotime($startTime));

    // Inserção no banco de dados
    $sql = "INSERT INTO novaera_participantes (email, user_name, join_time, start_time) VALUES (?, ?, ?, ?)";
    $stmt = $mysqli->prepare($sql);

    if ($stmt) {
        $stmt->bind_param("ssss", $email, $userName, $joinTimeFormatted, $startTimeFormatted);

        if ($stmt->execute()) {
            if (isset($logFile)) { // Verifica se o log está configurado
                $logMessage = date('Y-m-d H:i:s') . " - Dados inseridos no banco de dados com sucesso." . PHP_EOL;
                file_put_contents($logFile, $logMessage, FILE_APPEND);
            }
        } else {
            if (isset($logFile)) { // Verifica se o log está configurado
                $logMessage = date('Y-m-d H:i:s') . " - Erro ao inserir no banco de dados: " . $stmt->error . PHP_EOL;
                file_put_contents($logFile, $logMessage, FILE_APPEND);
            }
        }
        $stmt->close();
    } else {
        if (isset($logFile)) { // Verifica se o log está configurado
            $logMessage = date('Y-m-d H:i:s') . " - Erro ao preparar a consulta: " . $mysqli->error . PHP_EOL;
            file_put_contents($logFile, $logMessage, FILE_APPEND);
        }
    }
} else {
    if (isset($logFile)) { // Verifica se o log está configurado
        $logMessage = date('Y-m-d H:i:s') . " - Erro ao decodificar o JSON: " . json_last_error_msg() . PHP_EOL;
        file_put_contents($logFile, $logMessage, FILE_APPEND);
    }
}

// Finaliza conexão com o banco de dados
$mysqli->close();

// Retorna confirmação do recebimento
echo "Payload recebido e registrado no log.";
?>