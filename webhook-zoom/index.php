<?php
// Caminho para o arquivo de configuração tokens e conexão com o banco de dados
include '../config/conexao.php';

try {
    // Lê o corpo da requisição
    $input = file_get_contents('php://input');

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
        $params = [$email, $userName, $joinTimeFormatted, $startTimeFormatted];

        executar_consulta($sql, $params);

        // Logar sucesso
        echo "Dados inseridos no banco de dados com sucesso.";
    } else {
        throw new Exception("Erro ao decodificar o JSON: " . json_last_error_msg());
    }
} catch (Exception $e) {
    // Logar erro
    error_log("Erro ao processar o webhook: " . $e->getMessage());
    http_response_code(500); // Retornar código de erro 500
    echo "Erro ao processar o webhook.";
}
?>