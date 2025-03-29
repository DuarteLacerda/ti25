<?php
session_start();
// Definir o cabeçalho para a resposta como HTML
header('Content-Type: text/html; charset=utf-8');

// Verificar o método da requisição
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    // Verificar se existe o parâmetro "nome" no GET
    if (isset($_GET['nome'])) {
        $nome = $_GET['nome'];
        $filePath = "$nome/valor.txt";

        // Verificar se o ficheiro existe
        if (file_exists($filePath)) {
            // Ler e imprimir o conteúdo do ficheiro
            echo file_get_contents($filePath);
        } else {
            http_response_code(404); // Not Found
            echo "<p><strong>Erro:</strong> Sensor '$nome' não encontrado.</p>";
        }
    } else {
        http_response_code(400); // Bad Request
        echo "<p><strong>Erro:</strong> Faltam parâmetros no GET. O parâmetro 'nome' é obrigatório.</p>";
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
