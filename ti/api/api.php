<?php
session_start();
// Definir o cabeçalho para a resposta como HTML
header('Content-Type: text/html; charset=utf-8');

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    if (isset($_GET['nome'])) {
        $nome = $_GET['nome'];
        $pasta = "$nome/";

        // Verificar se a pasta do sensor existe
        if (!is_dir($pasta)) {
            http_response_code(404);
            echo "<p><strong>Erro:</strong> Sensor '$nome' não encontrado.</p>";
            exit;
        }

        $parametrosLidos = false;

        // Valor
        if (isset($_GET['valor'])) {
            $valorPath = $pasta . 'valor.txt';
            if (file_exists($valorPath)) {
                echo file_get_contents($valorPath) . "\n";
            } else {
                echo "Erro: 'valor.txt' não encontrado.\n";
            }
            $parametrosLidos = true;
        }

        // Hora
        if (isset($_GET['hora'])) {
            $horaPath = $pasta . 'hora.txt';
            if (file_exists($horaPath)) {
                echo file_get_contents($horaPath) . "\n";
            } else {
                echo "Erro: 'hora.txt' não encontrado.\n";
            }
            $parametrosLidos = true;
        }

        // Nome (só imprime se vier um parâmetro tipo &nome=true, para não confundir com o nome do sensor)
        if (isset($_GET['nometxt'])) {
            $nomePath = $pasta . 'nome.txt';
            if (file_exists($nomePath)) {
                echo file_get_contents($nomePath) . "\n";
            } else {
                echo "Erro: 'nome.txt' não encontrado.\n";
            }
            $parametrosLidos = true;
        }

        // Se não foi pedido nada específico, dá erro
        if (!$parametrosLidos) {
            http_response_code(400);
            echo "Erro: Nenhum parâmetro de leitura foi fornecido. Usa &valor, &hora ou &nometxt.\n";
        }
    } else {
        http_response_code(400);
        echo "Erro: O parâmetro 'nome' é obrigatório.\n";
    }
} elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Verificar se os dados necessários estão presentes
    if (isset($_POST['valor']) && isset($_POST['nome'])) {
        $valor = $_POST['valor'];
        $hora = date('Y/m/d H:i:s');
        $nome = $_POST['nome'];

        // Verificar se o valor e o nome não estão vazios
        if (empty($valor) || empty($nome)) {
            http_response_code(400); // Bad Request
            echo "<p><strong>Erro:</strong> O valor e o nome não podem estar vazios.</p>";
        } else {
            // Gerar o log no formato 'Hora: YYYY/MM/DD H:M:S; Valor: x;'
            $log = $hora . ';' . $valor . "\n";

            // Criar o diretório se não existir
            if (!file_exists("$nome")) {
                mkdir("$nome", 0777, true);
            }


            // Escrever os dados nos arquivos correspondentes
            file_put_contents("$nome/valor.txt", $valor);
            file_put_contents("$nome/hora.txt", $hora);
            file_put_contents("$nome/nome.txt", $nome);
            file_put_contents("$nome/log.txt", $log, FILE_APPEND); // Adiciona ao log em vez de substituir

            echo "<p><strong>Sucesso:</strong> Dados do sensor '$nome' inseridos com sucesso.</p>";
        }
    } else {
        http_response_code(400); // Bad Request
        echo "<p><strong>Erro:</strong> Dados incompletos. Certifique-se de enviar 'valor', 'nome' e 'hora'.</p>";
    }
} else {
    http_response_code(403); // Forbidden
    echo "<p><strong>Erro:</strong>Acesso negado. Apenas os métodos GET e POST são aceitos.</p>";
}
