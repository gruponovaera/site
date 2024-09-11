<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lista de Presenças</title>
    <!-- Link para o arquivo CSS externo -->
    <link rel="stylesheet" href="../estilo.css"> 
</head>
<body>
    <header>
        <!-- Adicionando a imagem antes do título -->
        <img src="../img/logo-na.png" alt="Logo NA">
        <h1>Grupo Nova Era OnLine de Narcóticos Anônimos</h1>
    </header>

    <main>
        <div class="form-container">
            <!-- Formulário para seleção da data e horários da reunião -->
            <h2>Consultar Participantes</h2>
            <form method="post" class="consulta-form">
                <label for="data_reuniao">Data da Reunião:</label>
                <input type="date" id="data_reuniao" name="data_reuniao" required>

                <label for="hora_inicio">Hora de Início:</label>
                <input type="time" id="hora_inicio" name="hora_inicio" required>

                <label for="hora_fim">Hora Final:</label>
                <input type="time" id="hora_fim" name="hora_fim" required>

                <button type="submit">Consultar</button>
            </form>
        </div>

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
            $sql = "SELECT email, user_name FROM novaera_participantes WHERE join_time BETWEEN ? AND ? ORDER BY join_time ASC";
            $stmt = $mysqli->prepare($sql);

            if ($stmt) {
                // Vincula os parâmetros e executa a consulta
                $stmt->bind_param("ss", $dataHoraInicio, $dataHoraFim);
                $stmt->execute();

                // Obtém o resultado da consulta
                $result = $stmt->get_result();

                // Cria uma lista de participantes começando com o PS
                $participantes = ["1- PS"];

                // Inicializa arrays para controle de duplicatas por e-mail e por nome
                $emailsUsados = [];
                $nomesUsados = [];

                // Itera sobre os resultados e adiciona os participantes na lista, evitando duplicatas
                $contador = 2;
                while ($row = $result->fetch_assoc()) {
                    $email = $row['email'];
                    $userName = $row['user_name'];

                    // Primeiro filtro: se o e-mail não for nulo e ainda não estiver na lista, adiciona o participante
                    if (!empty($email) && !in_array($email, $emailsUsados)) {
                        $participantes[] = $contador . "- " . $userName;
                        $emailsUsados[] = $email;  // Marca o e-mail como usado
                        $contador++;
                    }
                    // Segundo filtro: se o e-mail for nulo, verifica se o nome ainda não foi adicionado
                    elseif (empty($email) && !in_array($userName, $nomesUsados)) {
                        $participantes[] = $contador . "- " . $userName;
                        $nomesUsados[] = $userName;  // Marca o nome como usado
                        $contador++;
                    }
                    // Se o e-mail for duplicado ou o nome for duplicado (sem e-mail), ignora a entrada
                }

                // Exibe a lista de participantes
                echo "<div class='lista-participantes'>";
                echo "<h2>Lista de Presenças</h2>";
                echo "<ul>";
                foreach ($participantes as $participante) {
                    echo "<li>$participante</li>";
                }
                echo "</ul>";
                echo "</div>";

                // Fecha o statement
                $stmt->close();
            } else {
                echo "Erro ao preparar a consulta: " . $mysqli->error;
            }

            // Fecha a conexão com o banco de dados
            $mysqli->close();
        }
        ?>
    </main>

    <footer>
        <p>© 2021-2024 Grupo Nova Era OnLine de Narcóticos Anônimos</p>
    </footer>
</body>
</html>