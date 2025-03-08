<?php
/**
 * Arquivo de configuração e funções para conexão com o banco de dados
 * 
 * Este arquivo contém as configurações de conexão com o banco de dados MySQL
 * e funções auxiliares para estabelecer conexões e executar consultas de forma segura.
 */

// Configurações de conexão com o banco de dados
$dbConfig = [
    'host' => 'localhost',      // Endereço do servidor de banco de dados
    'user' => 'novaera',        // Nome de usuário para acesso ao banco
    'password' => 'sua_senha',  // Senha de acesso ao banco (deve ser substituída pela senha real)
    'dbname' => 'novaera_participantes', // Nome do banco de dados
    'port' => 3306              // Porta padrão do MySQL
];

/**
 * Função para conectar ao banco de dados.
 * 
 * Esta função estabelece uma conexão com o banco de dados MySQL usando
 * as configurações definidas no array $dbConfig. Em caso de erro,
 * lança uma exceção com a mensagem de erro.
 *
 * @return mysqli Objeto de conexão ao banco de dados.
 * @throws Exception Se a conexão falhar.
 */
function conectar_banco_de_dados() {
    global $dbConfig;

    try {
        // Cria uma nova conexão com o banco de dados
        $mysqli = new mysqli(
            $dbConfig['host'], 
            $dbConfig['user'], 
            $dbConfig['password'], 
            $dbConfig['dbname'], 
            $dbConfig['port']
        );
        
        // Verifica se houve erro na conexão
        if ($mysqli->connect_error) {
            throw new Exception("Erro de conexão com o banco de dados: " . $mysqli->connect_error);
        }
        
        // Retorna o objeto de conexão
        return $mysqli;
    } catch (Exception $e) {
        // Registra o erro no log do sistema
        error_log($e->getMessage());
        // Repassa a exceção para ser tratada pelo código chamador
        throw $e;
    }
}

/**
 * Função para executar uma consulta SQL preparada de forma segura.
 * 
 * Esta função prepara e executa uma consulta SQL com parâmetros,
 * protegendo contra injeção de SQL. Ela gerencia todo o ciclo de vida
 * da conexão e do statement, incluindo abertura e fechamento.
 *
 * @param string $sql A consulta SQL com placeholders para os parâmetros.
 * @param array $params Array de parâmetros para substituir os placeholders na consulta.
 * @return mysqli_result Resultado da consulta.
 * @throws Exception Se ocorrer erro na preparação ou execução da consulta.
 */
function executar_consulta($sql, $params = []) {
    try {
        // Estabelece conexão com o banco de dados
        $mysqli = conectar_banco_de_dados();
        
        // Prepara a consulta SQL
        $stmt = $mysqli->prepare($sql);
        if ($stmt === false) {
            throw new Exception("Erro ao preparar a consulta: " . $mysqli->error);
        }

        // Se houver parâmetros, vincula-os ao statement
        if (!empty($params)) {
            // Assume que todos os parâmetros são strings (tipo 's')
            // Cria uma string com 's' repetido conforme o número de parâmetros
            $stmt->bind_param(str_repeat('s', count($params)), ...$params);
        }

        // Executa a consulta preparada
        if (!$stmt->execute()) {
            throw new Exception("Erro ao executar a consulta: " . $stmt->error);
        }

        // Obtém o resultado da consulta
        $result = $stmt->get_result();
        
        // Libera recursos
        $stmt->close();
        $mysqli->close();

        // Retorna o resultado da consulta
        return $result;
    } catch (Exception $e) {
        // Registra o erro no log do sistema
        error_log($e->getMessage());
        // Repassa a exceção para ser tratada pelo código chamador
        throw $e;
    }
}
?>