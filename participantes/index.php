<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lista de Presenças</title>
    <!-- Link para o arquivo CSS externo que contém os estilos da página -->
    <link rel="stylesheet" href="../estilo.css">
</head>
<body>
    <header>
        <!-- Cabeçalho da página com logo e título do grupo -->
        <img src="../img/logo-na.png" alt="Logo NA">
        <h1>Grupo Nova Era OnLine de Narcóticos Anônimos</h1>
    </header>

    <main>
        <div class="form-container">
            <!-- Formulário para consulta de participantes por data e horário específicos -->
            <h2>Consultar Participantes</h2>
            <form method="post" class="consulta-form">
                <!-- Campo para selecionar a data da reunião -->
                <label for="data_reuniao">Data da Reunião:</label>
                <input type="date" id="data_reuniao" name="data_reuniao" required>

                <!-- Campo para definir a hora de início da consulta -->
                <label for="hora_inicio">Hora de Início:</label>
                <input type="time" id="hora_inicio" name="hora_inicio" required>

                <!-- Campo para definir a hora final da consulta -->
                <label for="hora_fim">Hora Final:</label>
                <input type="time" id="hora_fim" name="hora_fim" required>

                <!-- Botão para enviar o formulário e realizar a consulta -->
                <button type="submit">Consultar</button>
            </form>
        </div>

        <?php
        // Verifica se o formulário foi submetido através do método POST
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Inclui o arquivo de conexão com o banco de dados que contém as configurações e funções de conexão
            include '../config/conexao.php';

            // Estabelece uma nova conexão com o banco de dados usando as configurações definidas
            $mysqli = new mysqli($dbConfig['host'], $dbConfig['user'], $dbConfig['password'], $dbConfig['dbname'], $dbConfig['port']);

            // Verifica se houve erro na conexão com o banco de dados
            if ($mysqli->connect_error) {
                die("Erro de conexão com o banco de dados: " . $mysqli->connect_error);
            }

            // Captura os dados enviados pelo formulário
            $dataReuniao = $_POST['data_reuniao'];
            $horaInicio = $_POST['hora_inicio'];
            $horaFim = $_POST['hora_fim'];

            // Concatena a data com as horas para criar os timestamps completos para a consulta
            $dataHoraInicio = $dataReuniao . ' ' . $horaInicio;
            $dataHoraFim = $dataReuniao . ' ' . $horaFim;

            // Prepara a consulta SQL para buscar os participantes que entraram na reunião dentro do intervalo especificado
            // Ordena os resultados pela hora de entrada (join_time) em ordem crescente
            $sql = "SELECT email, user_name FROM novaera_participantes WHERE join_time BETWEEN ? AND ? ORDER BY join_time ASC";
            $stmt = $mysqli->prepare($sql);

            if ($stmt) {
                // Vincula os parâmetros da consulta (os dois timestamps) e executa a consulta
                $stmt->bind_param("ss", $dataHoraInicio, $dataHoraFim);
                $stmt->execute();

                // Obtém o resultado da consulta
                $result = $stmt->get_result();

                // Inicia a lista de participantes com o PS (Partilhador de Serviço) como primeiro item
                $participantes = ["1- PS"];

                // Inicializa arrays para controle de duplicatas por e-mail e por nome
                $emailsUsados = [];
                $nomesUsados = [];

                // Itera sobre os resultados da consulta para processar cada participante
                $contador = 2; // Contador para numeração sequencial dos participantes, começando em 2 (após o PS)
                while ($row = $result->fetch_assoc()) {
                    $email = $row['email'];
                    $userName = $row['user_name'];

                    // Primeiro filtro: se o e-mail não for nulo e ainda não estiver na lista, adiciona o participante
                    // Isso evita duplicatas de pessoas que entraram mais de uma vez com o mesmo e-mail
                    if (!empty($email) && !in_array($email, $emailsUsados)) {
                        $participantes[] = $contador . "- " . $userName;
                        $emailsUsados[] = $email;  // Marca o e-mail como usado
                        $contador++;
                    }
                    // Segundo filtro: se o e-mail for nulo, verifica se o nome ainda não foi adicionado
                    // Isso trata casos onde o participante não forneceu e-mail, usando apenas o nome como identificador
                    elseif (empty($email) && !in_array($userName, $nomesUsados)) {
                        $participantes[] = $contador . "- " . $userName;
                        $nomesUsados[] = $userName;  // Marca o nome como usado
                        $contador++;
                    }
                    // Se o e-mail for duplicado ou o nome for duplicado (sem e-mail), ignora a entrada
                }

                // Exibe a lista de participantes dentro de um container estilizado
                echo "<div class='lista-participantes'>";
                echo "<h2>Lista de Presenças</h2>";
                echo "<ul>";
                foreach ($participantes as $participante) {
                    echo "<li>$participante</li>";
                }
                echo "</ul>";
                echo "</div>";

                // Fecha o statement para liberar recursos
                $stmt->close();
            } else {
                // Exibe mensagem de erro caso a preparação da consulta falhe
                echo "Erro ao preparar a consulta: " . $mysqli->error;
            }

            // Fecha a conexão com o banco de dados para liberar recursos
            $mysqli->close();
        }
        ?>
    </main>

    <footer>
        <!-- Rodapé da página com informações de copyright -->
        <p>© 2021-2024 Grupo Nova Era OnLine de Narcóticos Anônimos</p>
    </footer>
</body>
</html>