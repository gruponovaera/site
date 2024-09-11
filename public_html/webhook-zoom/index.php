<?php
// Caminho para o arquivo de log
$logFile = '../../config/webhook.log';

// Caminho para o arquivo de configuração tokens e conexão com o banco de dados
include '../../config/conexao.php'; // Ajuste o caminho conforme necessário

// Conexão com o banco de dados
$mysqli = new mysqli($dbConfig['host'], $dbConfig['user'], $dbConfig['password'], $dbConfig['dbname'], $dbConfig['port']);

// Verifica se a conexão foi estabelecida
if ($mysqli->connect_error) {
    $logMessage = date('Y-m-d H:i:s') . " - Erro de conexão: " . $mysqli->connect_error . PHP_EOL;
    file_put_contents($logFile, $logMessage, FILE_APPEND);
    die("Erro de conexão com o banco de dados: " . $mysqli->connect_error);
}

// Lê o corpo da requisição
$input = file_get_contents('php://input');

// Salva o payload bruto no log
$logMessage = date('Y-m-d H:i:s') . " - Payload recebido: " . $input . PHP_EOL;
file_put_contents($logFile, $logMessage, FILE_APPEND);

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

    // Verifica se o e-mail existe para usar como filtro
    $filter = $email ? "email = ?" : "user_name = ?";
    $filterValue = $email ?: $userName;

    // Verifica se o participante já foi inserido para evitar duplicidade
    $checkSql = "SELECT COUNT(*) FROM novaera_participantes WHERE $filter";
    $checkStmt = $mysqli->prepare($checkSql);

    if ($checkStmt) {
        $checkStmt->bind_param("s", $filterValue);
        $checkStmt->execute();
        $checkStmt->bind_result($count);
        $checkStmt->fetch();
        $checkStmt->close();

        if ($count == 0) { // Se não houver registros, insere
            // Inserção no banco de dados
            $sql = "INSERT INTO novaera_participantes (email, user_name, join_time, start_time) VALUES (?, ?, ?, ?)";
            $stmt = $mysqli->prepare($sql);

            if ($stmt) {
                $stmt->bind_param("ssss", $email, $userName, $joinTimeFormatted, $startTimeFormatted);

                if ($stmt->execute()) {
                    $logMessage = date('Y-m-d H:i:s') . " - Dados inseridos no banco de dados com sucesso." . PHP_EOL;
                } else {
                    $logMessage = date('Y-m-d H:i:s') . " - Erro ao inserir no banco de dados: " . $stmt->error . PHP_EOL;
                }
                $stmt->close();
            } else {
                $logMessage = date('Y-m-d H:i:s') . " - Erro ao preparar a consulta: " . $mysqli->error . PHP_EOL;
            }
        } else {
            $logMessage = date('Y-m-d H:i:s') . " - Participante já registrado, não inserido novamente." . PHP_EOL;
        }
    } else {
        $logMessage = date('Y-m-d H:i:s') . " - Erro ao preparar a consulta de verificação: " . $mysqli->error . PHP_EOL;
    }

    file_put_contents($logFile, $logMessage, FILE_APPEND);
} else {
    $logMessage = date('Y-m-d H:i:s') . " - Erro ao decodificar o JSON: " . json_last_error_msg() . PHP_EOL;
    file_put_contents($logFile, $logMessage, FILE_APPEND);
}

// Finaliza conexão com o banco de dados
$mysqli->close();

// Retorna confirmarção do recebimento
echo "Payload recebido e registrado no log.";
?>