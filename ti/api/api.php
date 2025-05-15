<?php
session_start();
header('Content-Type: text/html; charset=utf-8'); // Definir o cabeçalho para a resposta como HTML

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    if (isset($_GET['nome'])) {
        $nome = $_GET['nome'];
        $pasta = "$nome/";

        if (!is_dir($pasta)) { // Verificar se a pasta do sensor existe
            http_response_code(404);
            die("<p><strong>Erro:</strong> Sensor '$nome' não encontrado.</p>");
        }

        $parametrosLidos = false;

        if (isset($_GET['valor'])) { // Lê o valor do sensor
            $valorPath = $pasta . 'valor.txt';
            if (file_exists($valorPath)) {
                echo file_get_contents($valorPath) . "\n";
            } else {
                die("Erro: 'valor.txt' não encontrado.\n");
            }
            $parametrosLidos = true;
        }

        if (isset($_GET['hora'])) { // Lê a hora do sensor
            $horaPath = $pasta . 'hora.txt';
            if (file_exists($horaPath)) {
                echo file_get_contents($horaPath) . "\n";
            } else {
                die("Erro: 'hora.txt' não encontrado.\n");
            }
            $parametrosLidos = true;
        }

        if (isset($_GET['nometxt'])) { // Lê o nome do sensor se o parâmetro for passado
            $nomePath = $pasta . 'nome.txt';
            if (file_exists($nomePath)) {
                echo file_get_contents($nomePath) . "\n";
            } else {
                die("Erro: 'nome.txt' não encontrado.\n");
            }
            $parametrosLidos = true;
        }

        if (!$parametrosLidos) { // Se não foi pedido nada específico, dá erro
            http_response_code(400);
            die("Erro: Nenhum parâmetro de leitura foi fornecido. Usa &valor, &hora ou &nometxt.\n");
        }
    } else {
        http_response_code(400); // Bad Request
        die("Erro: O parâmetro 'nome' é obrigatório.\n");
    }
} elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['valor']) && isset($_POST['nome'])) {
        $valor = $_POST['valor'];
        $hora = date('Y/m/d H:i:s');
        $nome = $_POST['nome'];

        if (empty($valor) || empty($nome)) {
            http_response_code(400);
            die("<p><strong>Erro:</strong> O valor e o nome não podem estar vazios.</p>");
        } else {
            if (!file_exists($nome)) {
                mkdir($nome, 0777, true);
            }

            $valorPath = "$nome/valor.txt";
            $horaPath = "$nome/hora.txt";
            $nomePath = "$nome/nome.txt";
            $logPath = "$nome/log.txt";

            $criarLog = true;

            // Verifica se já existe um valor anterior
            if (file_exists($valorPath)) {
                $valorAnterior = trim(file_get_contents($valorPath));
                // Só cria log se o valor for diferente
                if ($valorAnterior === $valor) {
                    $criarLog = false;
                }
            }

            // Atualiza os ficheiros
            file_put_contents($valorPath, $valor);
            file_put_contents($horaPath, $hora);
            file_put_contents($nomePath, $nome);

            if ($criarLog) {
                // Formatar como 'hora;valor\n'
                $log = "$hora;$valor\n";
                $logContent = "";

                if (file_exists($logPath)) {
                    $lines = array_filter(explode("\n", trim(file_get_contents($logPath))));
                    // Se houver 50 ou mais linhas, remove a primeira (mais antiga)
                    if (count($lines) >= 50) {
                        array_shift($lines);
                    }
                    $logContent = implode("\n", $lines) . "\n";
                }

                $logContent .= $log;
                file_put_contents($logPath, $logContent);
            }

            die("<p><strong>Sucesso:</strong> Dados do sensor '$nome' atualizados com sucesso.</p>");
        }
    } else {
        http_response_code(400);
        die("<p><strong>Erro:</strong> Dados incompletos. Certifique-se de enviar 'valor', 'nome' e 'hora'.</p>");
    }
}
