<?php
// Configurações de conexão com o banco de dados
$dbConfig = [
    'host' => 'localhost',
    'user' => 'novaera',
    'password' => 'sua_senha',  // coloque a senha
    'dbname' => 'novaera_participantes',
    'port' => 3306
];

/**
 * Função para conectar ao banco de dados.
 *
 * @return mysqli Conexão ao banco de dados.
 * @throws Exception Se a conexão falhar.
 */
function conectar_banco_de_dados() {
    global $dbConfig;

    try {
        $mysqli = new mysqli($dbConfig['host'], $dbConfig['user'], $dbConfig['password'], $dbConfig['dbname'], $dbConfig['port']);
        if ($mysqli->connect_error) {
            throw new Exception("Erro de conexão com o banco de dados: " . $mysqli->connect_error);
        }
        return $mysqli;
    } catch (Exception $e) {
        error_log($e->getMessage());
        throw $e;
    }
}

/**
 * Função para executar uma consulta ao banco de dados.
 *
 * @param string $sql A consulta SQL.
 * @param array $params Parâmetros para a consulta SQL.
 * @return mixed Resultados da consulta.
 * @throws Exception Se a execução da consulta falhar.
 */
function executar_consulta($sql, $params = []) {
    try {
        $mysqli = conectar_banco_de_dados();
        $stmt = $mysqli->prepare($sql);
        if ($stmt === false) {
            throw new Exception("Erro ao preparar a consulta: " . $mysqli->error);
        }

        if (!empty($params)) {
            // Supondo que todos os parâmetros são strings
            $stmt->bind_param(str_repeat('s', count($params)), ...$params);
        }

        if (!$stmt->execute()) {
            throw new Exception("Erro ao executar a consulta: " . $stmt->error);
        }

        $result = $stmt->get_result();
        $stmt->close();
        $mysqli->close();

        return $result;
    } catch (Exception $e) {
        error_log($e->getMessage());
        throw $e;
    }
}
?>