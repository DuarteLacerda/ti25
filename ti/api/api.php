<?php
session_start();
header('Content-Type: text/html; charset=utf-8'); // Definir o cabeçalho para a resposta como HTML

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    if (isset($_GET['get']) && $_GET['get'] == 1) {
        $sensores = ['temperatura', 'humidade', 'distancia', 'ventoinha', 'cancela', 'led'];

        foreach ($sensores as $nome) {
            $valorPath = "$nome/valor.txt";
            if (file_exists($valorPath)) {
                $valor = trim(file_get_contents($valorPath));
                $valor = trim(file_get_contents($valorPath));
                echo "$nome;$valor\n";
            }
        }
        exit;
    }


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
    $temImagem = isset($_FILES['imagem']);
    $temNome = isset($_POST['nome']);
    $temValor = isset($_POST['valor']);

    // === CASO 1: imagem + nome (ex: webcam)
    if ($temImagem && $temNome && !$temValor) {
        $nome = $_POST['nome'];
        $hora = date('Y/m/d H:i:s');
        $timestamp = date('Ymd_His');

        // Criar pastas
        if (!file_exists($nome)) mkdir($nome, 0777, true);
        $imgDir = "$nome/imagens/";
        if (!file_exists($imgDir)) mkdir($imgDir, 0777, true);

        // Criar nome do ficheiro de imagem
        $ext = pathinfo($_FILES["imagem"]["name"], PATHINFO_EXTENSION);
        $filename = "webcam_$timestamp.$ext";
        $target = $imgDir . $filename;

        // Guardar imagem
        if (!move_uploaded_file($_FILES["imagem"]["tmp_name"], $target)) {
            http_response_code(400);
            die("<p><strong>Erro:</strong> Falha ao guardar imagem.</p>");
        }

        // Atualizar ficheiros
        file_put_contents("$nome/nome.txt", $nome);
        file_put_contents("$nome/hora.txt", $hora);

        // Atualizar log
        $logPath = "$nome/log.txt";
        $log = "$hora;$filename\n";
        $logContent = "";

        if (file_exists($logPath)) {
            $lines = array_filter(explode("\n", trim(file_get_contents($logPath))));
            if (count($lines) >= 10) {
                array_shift($lines);
            }
            $logContent = implode("\n", $lines) . "\n";
        }

        $logContent .= $log;
        file_put_contents($logPath, $logContent);

        http_response_code(200);
        die("<p><strong>Sucesso:</strong> Imagem '$filename' do sensor '$nome' guardada.</p>");
    }

    // === CASO 2: nome + valor (sensores/atuadores), com ou sem imagem
    if ($temNome && $temValor) {
        $nome = $_POST['nome'];
        $valor = $_POST['valor'];
        $hora = date('Y/m/d H:i:s');

        if (empty($nome)) {
            http_response_code(400);
            die("<p><strong>Erro:</strong> 'nome' e são obrigatórios.</p>");
        }

        if (!file_exists($nome)) mkdir($nome, 0777, true);

        $valorPath = "$nome/valor.txt";
        $horaPath = "$nome/hora.txt";
        $nomePath = "$nome/nome.txt";
        $logPath = "$nome/log.txt";

        $criarLog = true;
        if (file_exists($valorPath)) {
            $valorAnterior = trim(file_get_contents($valorPath));
            if ($valorAnterior === $valor) {
                $criarLog = false;
            }
        }

        file_put_contents($valorPath, $valor);
        file_put_contents($horaPath, $hora);
        file_put_contents($nomePath, $nome);

        if ($criarLog) {
            $log = "$hora;$valor\n";
            $logContent = "";
            if (file_exists($logPath)) {
                $lines = array_filter(explode("\n", trim(file_get_contents($logPath))));
                if (count($lines) >= 25) {
                    array_shift($lines);
                }
                $logContent = implode("\n", $lines) . "\n";
            }
            $logContent .= $log;
            file_put_contents($logPath, $logContent);
        }

        // Guardar imagem (se enviada)
        if ($temImagem) {
            $imgDir = "$nome/imagens/";
            if (!file_exists($imgDir)) mkdir($imgDir, 0777, true);

            $timestamp = date('Ymd_His');
            $ext = pathinfo($_FILES["imagem"]["name"], PATHINFO_EXTENSION);
            $filename = "imagem_$timestamp.$ext";
            $target = $imgDir . $filename;

            move_uploaded_file($_FILES["imagem"]["tmp_name"], $target);
        }

        http_response_code(200);
        die("<p><strong>Sucesso:</strong> Dados de '$nome' atualizados.</p>");
    }

    // Caso inválido
    http_response_code(400);
    die("<p><strong>Erro:</strong> Dados insuficientes. Envie 'nome' e 'imagem', ou 'nome' e 'valor'.</p>");
}
