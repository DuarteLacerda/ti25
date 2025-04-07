<?php
session_start();
if (!isset($_SESSION['username'])) { // Verifica se o utilizador está logado
    header("refresh:5;url=auth/login.php");
    die("Acesso Restrito");
}

function formatNumber($num) // Formata o número para duas casas decimais e remove zeros à direita
{
    return rtrim(rtrim(number_format($num, 2, '.', ''), '0'), '.');
}

$nome = $_GET['nome']; // Obter o nome passado na URL

$logFilePath = "api/{$nome}/log.txt"; // Caminho do ficheiro de log


if (!$nome || !file_exists($logFilePath)) { // Verificar se o ficheiro existe
    echo "Sensor não encontrado!\nSe não for redirecionado automaticamente, clique <a href='dashboard.php'>aqui</a>.";
    http_response_code(404); // Not Found
    header("refresh:5;url=dashboard.php");
}

// Ler o ficheiro e dividir em linhas
$logData = file($logFilePath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
$historico = [];


foreach ($logData as $linha) { // Processar cada linha do ficheiro
    $dados = explode(";", $linha); // Separar valores por ";"
    if (count($dados) == 2) {
        $historico[] = [
            "hora" => trim($dados[0]), // A hora deve ser uma string
            "valor" => trim($dados[1]) // O valor deve ser uma string
        ];
    }
}


usort($historico, function ($a, $b) { // Ordenar por ordem decrescente (do mais recente para o mais antigo)
    return strtotime($b["hora"]) - strtotime($a["hora"]);
});

// Manter apenas os primeiros 50 logs
$historico = array_slice($historico, 0, 50);
?>

<!doctype html>
<html lang="pt">

<head>
    <title>Histórico - <?php echo ucfirst($nome); ?></title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link rel="icon" href="assets/imagens/favicon.png" type="image/x-icon">
    <link rel="stylesheet" href="assets/style.css">
    <link rel="stylesheet" href="assets/dashboard.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
    <div class="container">
        <?php include("add-ons/nav.php"); ?>
        <h2 class="mt-5 mb-4">Histórico de <?php echo ucfirst($nome); ?></h2>
        <div class="row mt-4">
            <table class="table table-bordered table-striped text-center">
                <thead class="table-dark">
                    <tr>
                        <th>Valor</th>
                        <th>Data de Atualização</th>
                        <th>Estado Alertas</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($historico as $dado): ?>
                        <tr>
                            <td><?php
                                switch ($nome) {
                                    case "temperatura":
                                        echo formatNumber($dado["valor"]) . "°C";
                                        break;
                                    case "humidade":
                                        echo formatNumber($dado["valor"]) . "%";
                                        break;
                                    case "ultrasonico":
                                        echo formatNumber($dado["valor"]) . " cm";
                                        break;
                                    case "ventoinha":
                                        echo formatNumber($dado["valor"]) . " RPM";
                                        break;
                                    case "servo":
                                        echo formatNumber($dado["valor"]) . "º";
                                        break;
                                    case "led":
                                        echo formatNumber($dado["valor"]);
                                        break;
                                } ?>
                            </td>
                            <td><?php echo $dado["hora"]; ?></td>
                            <td>
                                <?php
                                switch ($nome) {
                                    case "temperatura":
                                        if ($dado["valor"] >= 40.00) {
                                            echo "<span class='badge bg-danger'>Crítico</span>";
                                        } elseif ($dado["valor"] > 20.00 && $dado["valor"] < 40.00) {
                                            echo "<span class='badge bg-warning'>Elevado</span>";
                                        } elseif ($dado["valor"] < 20.00 && $dado["valor"] > 0.00) {
                                            echo "<span class='badge bg-primary'>Normal</span>";
                                        } else {
                                            echo "<span class='badge bg-secondary'>Número negativo | Erro de sensor!!</span>";
                                        }
                                        break;
                                    case "humidade":
                                        if ($dado["valor"] >= 80.00) {
                                            echo "<span class='badge bg-danger'>Crítico</span>";
                                        } elseif ($dado["valor"] > 50.00 && $dado["valor"] < 80.00) {
                                            echo "<span class='badge bg-warning'>Elevado</span>";
                                        } elseif ($dado["valor"] < 50.00 && $dado["valor"] > 0.00) {
                                            echo "<span class='badge bg-primary'>Normal</span>";
                                        } else {
                                            echo "<span class='badge bg-secondary'>Número negativo | Erro de sensor!!</span>";
                                        }
                                        break;
                                    case "ultrasonico":
                                        if ($dado["valor"] >= 100.00) {
                                            echo "<span class='badge bg-danger'>Muito Longe</span>";
                                        } elseif ($dado["valor"] > 50.00 && $dado["valor"] < 100.00) {
                                            echo "<span class='badge bg-danger'>Longe</span>";
                                        } elseif ($dado["valor"] <= 50.00 && $dado["valor"] > 20.00) {
                                            echo "<span class='badge bg-warning'>+/- Longe</span>";
                                        } elseif ($dado["valor"] <= 20.00 && $dado["valor"] > 10.00) {
                                            echo "<span class='badge bg-warning'>Perto</span>";
                                        } elseif ($dado["valor"] <= 10.00 && $dado["valor"] > 0.00) {
                                            echo "<span class='badge bg-success'>Muito Perto</span>";
                                        } else {
                                            echo "<span class='badge bg-secondary'>Número negativo | Erro de sensor!!</span>";
                                        }
                                        break;
                                    case "ventoinha":
                                        if ($dado["valor"] >= 5.00) {
                                            echo "<span class='badge bg-success'>Ligado</span>";
                                        } elseif ($dado["valor"] > 0.00 && $dado["valor"] < 5.00) {
                                            echo "<span class='badge bg-danger'>Desligado</span>";
                                        } else {
                                            echo "<span class='badge bg-secondary'>Número negativo | Erro de sensor!!</span>";
                                        }
                                        break;
                                    case "servo":
                                        if ($dado["valor"] >= 80.00) {
                                            echo "<span class='badge bg-success'>Fechado</span>";
                                        } elseif ($dado["valor"] < 80.00) {
                                            echo "<span class='badge bg-danger'>Aberto</span>";
                                        } else {
                                            echo "<span class='badge bg-secondary'>Número negativo | Erro de sensor!!</span>";
                                        }
                                        break;
                                    case "led":
                                        if ($dado["valor"] == 1) {
                                            echo "<span class='badge bg-success'>Ligado</span>";
                                        } elseif ($dado["valor"] == 0) {
                                            echo "<span class='badge bg-danger'>Desligado</span>";
                                        } else {
                                            echo "<span class='badge bg-secondary'>Número negativo | Erro de sensor!!</span>";
                                        }
                                        break;
                                }
                                ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>

        </div>
        <div class="text-end mt-4 mb-4">
            <form action="dashboard.php" method="GET">
                <button class="btn btn-outline-dark">Voltar</button>
            </form>
        </div>
        <?php include("add-ons/footer.php"); ?>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.min.js"></script>
</body>

</html>