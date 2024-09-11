<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Lista de Presenças</title>
</head>
<body>
    <h1>Lista de Presenças</h1>

    <!-- Formulário para seleção da data e horários da reunião -->
    <form method="post">
        <label for="data_reuniao">Data da Reunião:</label>
        <input type="date" id="data_reuniao" name="data_reuniao" required>

        <label for="hora_inicio">Hora de Início:</label>
        <input type="time" id="hora_inicio" name="hora_inicio" required>

        <label for="hora_fim">Hora Final:</label>
        <input type="time" id="hora_fim" name="hora_fim" required>

        <button type="submit">Consultar</button>
    </form>

    <?php
    // Verifica se o formulário foi submetido
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // Inclui o arquivo de conexão com o banco de dados
        include '../../config/conexao.php';

        // Conexão com o banco de dados
        $mysqli = new mysqli($dbConfig['host'], $dbConfig['user'], $dbConfig['password'], $dbConfig['dbname'], $dbConfig['port']);

        // Verifica se a conexão foi estabelecida
        if ($mysqli->connect_error) {
            die("Erro de conexão com o banco de dados: " . $mysqli->connect_error);
        }

        // Captura os dados do formulário
        $dataReuniao = $_POST['data_reuniao'];
        $horaInicio = $_POST['hora_inicio'];
        $horaFim = $_POST['hora_fim'];

        // Concatena a data com as horas de início e fim para formatar os campos de consulta
        $dataHoraInicio = $dataReuniao . ' ' . $horaInicio;
        $dataHoraFim = $dataReuniao . ' ' . $horaFim;

        // Consulta SQL para buscar os participantes dentro do intervalo de tempo especificado
        $sql = "SELECT user_name FROM novaera_participantes WHERE join_time BETWEEN ? AND ? ORDER BY join_time ASC";
        $stmt = $mysqli->prepare($sql);

        if ($stmt) {
            // Vincula os parâmetros e executa a consulta
            $stmt->bind_param("ss", $dataHoraInicio, $dataHoraFim);
            $stmt->execute();

            // Obtém o resultado da consulta
            $result = $stmt->get_result();

            // Cria uma lista de participantes começando com o PS
            $participantes = ["1- PS"];

            // Itera sobre os resultados e adiciona os participantes na lista
            $contador = 2;
            while ($row = $result->fetch_assoc()) {
                $participantes[] = $contador . "- " . $row['user_name'];
                $contador++;
            }

            // Exibe a lista de participantes
            echo "<h2>Lista de Presença:</h2>";
            echo "<ul>";
            foreach ($participantes as $participante) {
                echo "<li>$participante</li>";
            }
            echo "</ul>";

            // Fecha o statement
            $stmt->close();
        } else {
            echo "Erro ao preparar a consulta: " . $mysqli->error;
        }

        // Fecha a conexão com o banco de dados
        $mysqli->close();
    }
    ?>
</body>
</html>