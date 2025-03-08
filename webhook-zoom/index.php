<?php
/**
 * Webhook para receber e processar eventos do Zoom
 * 
 * Este script recebe notificações de eventos do Zoom via webhook,
 * extrai informações sobre os participantes e as armazena no banco de dados.
 * É usado para registrar a presença de participantes nas reuniões online.
 */

// Inclui o arquivo de configuração e funções para conexão com o banco de dados
include '../config/conexao.php';

try {
    // Lê o corpo da requisição HTTP recebida do Zoom
    $input = file_get_contents('php://input');

    // Decodifica o JSON recebido para um array associativo PHP
    $data = json_decode($input, true);
    
    // Verifica se o JSON foi decodificado com sucesso
    if (json_last_error() === JSON_ERROR_NONE) {
        // Extrai os dados do participante do JSON recebido
        // Usa o operador de coalescência nula (??) para definir valores padrão caso os campos não existam
        $email = $data['payload']['object']['participant']['email'] ?? null;
        $userName = $data['payload']['object']['participant']['user_name'] ?? '';
        $joinTime = $data['payload']['object']['participant']['join_time'] ?? '';
        $startTime = $data['payload']['object']['start_time'] ?? '';

        // Converte as datas recebidas para o formato padrão do MySQL (YYYY-MM-DD HH:MM:SS)
        $joinTimeFormatted = date('Y-m-d H:i:s', strtotime($joinTime));
        $startTimeFormatted = date('Y-m-d H:i:s', strtotime($startTime));

        // Prepara a consulta SQL para inserir os dados do participante no banco de dados
        $sql = "INSERT INTO novaera_participantes (email, user_name, join_time, start_time) VALUES (?, ?, ?, ?)";
        $params = [$email, $userName, $joinTimeFormatted, $startTimeFormatted];

        // Executa a consulta usando a função definida em conexao.php
        executar_consulta($sql, $params);

        // Retorna uma mensagem de sucesso
        echo "Dados inseridos no banco de dados com sucesso.";
    } else {
        // Lança uma exceção se houver erro na decodificação do JSON
        throw new Exception("Erro ao decodificar o JSON: " . json_last_error_msg());
    }
} catch (Exception $e) {
    // Registra o erro no log do sistema
    error_log("Erro ao processar o webhook: " . $e->getMessage());
    
    // Retorna código de erro HTTP 500 (Internal Server Error)
    http_response_code(500);
    
    // Retorna uma mensagem de erro genérica
    echo "Erro ao processar o webhook.";
}
?>