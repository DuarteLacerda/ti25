<?php
session_start();
if (!isset($_SESSION['loggedin'])) { // Verifica se o utilizador está logado
    header("refresh:5;url=auth/login.php");
    die("Acesso Restrito");
}

function loadSensorData($nome)
{
    $base = "api/$nome/";
    return [
        "valor" => file_get_contents($base . "valor.txt"),
        "hora" => file_get_contents($base . "hora.txt"),
        "nome" => file_get_contents($base . "nome.txt"),
        "log" => file_get_contents($base . "log.txt")
    ];
}

$temperatura = loadSensorData("temperatura");
$humidade = loadSensorData("humidade");
$distancia = loadSensorData("distancia");
$ventoinha = loadSensorData("ventoinha");
$angulo = loadSensorData("angulo");
$led = loadSensorData("led");

function formatNumber($number) // Formata o número para duas casas decimais e remove zeros à direita
{ // Formata o número para duas casas decimais e remove zeros à direita
    return rtrim(rtrim(number_format($number, 2, '.', ''), '0'), '.');
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Dashboard</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="assets/imagens/favicon.png" type="image/x-icon">
    <link rel="stylesheet" href="assets/style.css">
    <link rel="stylesheet" href="assets/dashboard.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
</head>

<body>
    <div id="content">
        <div class="container-fluid">
            <?php include("add-ons/nav.php"); ?>
            <div class="row mt-4">
                <div id="title-header" class="col-md-5 ms-3">
                    <h1>Servidor IoT</h1>
                    <h6 class="user-logo" id="user-name"><img src="assets/imagens/user.png" alt="Fotografia do utilizador" width="25"> <?php echo $_SESSION['username'] ?></h6>
                </div>
                <div id="logo-header" class="col-md-6">
                    <img class="estg" src="assets/imagens/ProduceShop.png" alt="Politécnico de Leiria">
                </div>
            </div>
            <div class="row mt-2 mb-2">
                <div class="col-sm-4 mb-2">
                    <div class="card bg-c-green order-card">
                        <div class="card-block">
                            <h6 class="m-b-20"><strong><?php echo $temperatura["nome"]; ?></strong></h6>
                            <h3 class="text-right"><i class="fa-solid fa-temperature-half pulse"></i><span> <?php echo $temperatura["valor"]; ?>ºC</span></h3>
                            <p class="m-b-0"><strong>Last update: <?php echo $temperatura["hora"]; ?><span class="f-right"><a href="history.php?nome=temperatura&nometxt">Histórico</a></span></strong></p>
                        </div>
                    </div>
                </div>
                <div class="col-sm-4 mb-2">
                    <div class="card bg-c-blue order-card">
                        <div class="card-block">
                            <h6 class="m-b-20"><strong><?php echo $humidade["nome"]; ?></strong></h6>
                            <h3 class="text-right"><i class="fa-solid fa-droplet pulse"></i><span> <?php echo $humidade["valor"]; ?>%</span></h3>
                            <p class="m-b-0"><strong>Last update: <?php echo $humidade["hora"]; ?><span class="f-right"><a href="history.php?nome=humidade&nometxt">Histórico</a></span></strong></p>
                        </div>
                    </div>
                </div>
                <div class="col-sm-4 mb-2">
                    <div class="card bg-c-yellow order-card">
                        <div class="card-block">
                            <h6 class="m-b-20"><strong><?php echo $distancia["nome"]; ?></strong></h6>
                            <h3 class="text-right"><i class="fa-solid fa-people-arrows pulse"></i><span> <?php echo $distancia["valor"]; ?> cm</span></h3>
                            <p class="m-b-0"><strong>Last update: <?php echo $distancia["hora"]; ?><span class="f-right"><a href="history.php?nome=distancia&nometxt">Histórico</a></span></strong></p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row mt-2 mb-2">
                <div class="col-sm-4 mb-2">
                    <div class="card bg-c-orange order-card">
                        <div class="card-block">
                            <h6 class="m-b-20"><strong><?php echo $ventoinha["nome"]; ?></strong></h6>
                            <h3 class="text-right"><i class="fa-solid fa-fan spin"></i><span> <?php echo formatNumber($ventoinha["valor"]); ?> RPM</span></h3>
                            <p class="m-b-0"><strong>Last update: <?php echo $ventoinha["hora"]; ?><span class="f-right"><a href="history.php?nome=ventoinha&nometxt">Histórico</a></span></strong></p>
                        </div>
                    </div>
                </div>
                <div class="col-sm-4 mb-2">
                    <div class="card bg-c-pink order-card">
                        <div class="card-block">
                            <h6 class="m-b-20"><strong><?php echo $angulo["nome"]; ?></strong></h6>
                            <h3 class="text-right"><i class="fa-solid fa-door-open pulse"></i><span> <?php echo $angulo["valor"]; ?>º</span></h3>
                            <p class="m-b-0"><strong>Last update: <?php echo $angulo["hora"]; ?><span class="f-right"><a href="history.php?nome=angulo&nometxt">Histórico</a></span></strong></p>
                        </div>
                    </div>
                </div>
                <div class="col-sm-4 mb-2">
                    <div class="card bg-c-purple order-card">
                        <div class="card-block">
                            <h6 class="m-b-20"><strong><?php echo $led["nome"]; ?></strong></h6>
                            <h3 class="text-right"><i class="fa-solid fa-sun spin_reverse"></i><span>
                                    <?php
                                    if ($led["valor"] == 1) {
                                        echo "Ligado";
                                    } else {
                                        echo "Desligado";
                                    }
                                    ?>
                                </span></h3>
                            <p class="m-b-0"><strong>Last update: <?php echo $led["hora"]; ?><span class="f-right"><a href="history.php?nome=led&nometxt">Histórico</a></span></strong></p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row mt-4">
                <h2>Tabela de Sensores</h2>
                <div class="table-responsive-sm">
                    <table class="table table-hover text-center align-middle">
                        <thead class="table-dark">
                            <tr>
                                <th>Sensor</th>
                                <th>Valor</th>
                                <th>Data</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- Temperatura -->
                            <tr>
                                <td>
                                    <a href="history.php?nome=temperatura&nometxt" class="text-decoration-none fw-bold">
                                        🌡️ <?php echo $temperatura["nome"]; ?>
                                    </a>
                                </td>
                                <td><?php echo $temperatura['valor']; ?>º</td>
                                <td><?php echo $temperatura["hora"]; ?></td>
                                <td>
                                    <?php
                                    switch (true) {
                                        case ($temperatura["valor"] >= 40.00):
                                            echo "<span class='badge bg-warning text-dark'>Alto</span>";
                                            break;
                                        case ($temperatura["valor"] > 15.00 && $temperatura["valor"] < 40.00):
                                            echo "<span class='badge bg-primary'>Normal</span>";
                                            break;
                                        case ($temperatura["valor"] <= 15.00):
                                            echo "<span class='badge bg-success'>fria</span>";
                                            break;
                                        default:
                                            echo "<span class='badge bg-secondary'>Erro</span>";
                                            break;
                                    }
                                    ?>
                                </td>
                            </tr>
                            <!-- Humidade -->
                            <tr>
                                <td>
                                    <a href="history.php?nome=humidade&nometxt" class="text-decoration-none fw-bold">
                                        💧 <?php echo $humidade["nome"]; ?>
                                    </a>
                                </td>
                                <td><?php echo $humidade["valor"]; ?>%</td>
                                <td><?php echo $humidade["hora"]; ?></td>
                                <td>
                                    <?php
                                    if ($humidade["valor"] >= 70.00) {
                                        echo "<span class='badge bg-warning text-danger'>Alta</span>";
                                    } elseif ($humidade["valor"] >= 30.00 && $humidade["valor"] < 70.00) {
                                        echo "<span class='badge bg-success'>Normal</span>";
                                    } else {
                                        echo "<span class='badge bg-primary'>Baixa</span>";
                                    }
                                    ?>
                                </td>
                            </tr>
                            <!-- Ultrassónico -->
                            <tr>
                                <td>
                                    <a href="history.php?nome=ultrasonico&nometxt" class="text-decoration-none fw-bold">
                                        📏 <?php echo $distancia["nome"]; ?>
                                    </a>
                                </td>
                                <td><?php echo $distancia["valor"]; ?> cm</td>
                                <td><?php echo $distancia["hora"]; ?></td>
                                <td>
                                    <?php
                                    switch (true) {
                                        case ($distancia["valor"] >= 100.00):
                                            echo "<span class='badge bg-danger'>Muito Longe</span>";
                                            break;
                                        case ($distancia["valor"] > 50.00 && $distancia["valor"] < 100.00):
                                            echo "<span class='badge bg-danger'>Longe</span>";
                                            break;
                                        case ($distancia["valor"] <= 50.00 && $distancia["valor"] > 20.00):
                                            echo "<span class='badge bg-warning text-dark'>+/- Longe</span>";
                                            break;
                                        case ($distancia["valor"] <= 20.00 && $distancia["valor"] > 10.00):
                                            echo "<span class='badge bg-warning text-dark'>Perto</span>";
                                            break;
                                        case ($distancia["valor"] <= 10.00 && $distancia["valor"] > 0.00):
                                            echo "<span class='badge bg-success'>Muito Perto</span>";
                                            break;
                                        default:
                                            echo "<span class='badge bg-secondary'>Erro</span>";
                                            break;
                                    }
                                    ?>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="row mt-4">
                <h2>Tabela de Atuadores</h2>
                <div class="table-responsive-sm">
                    <table class="table table-hover text-center align-middle">
                        <thead class="table-dark">
                            <tr>
                                <th>Atuador</th>
                                <th>Valor</th>
                                <th>Data</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- Ventoinha -->
                            <tr>
                                <td>
                                    <a href="history.php?nome=ventoinha&nometxt" class="text-decoration-none fw-bold">
                                        🌬️ <?php echo $ventoinha["nome"]; ?>
                                    </a>
                                </td>
                                <td><?php echo formatNumber($ventoinha['valor']); ?> RPM</td>
                                <td><?php echo $ventoinha["hora"]; ?></td>
                                <td>
                                    <?php
                                    if ($ventoinha["valor"] > 0) {
                                        echo "<span class='badge bg-success'>Ligada</span>";
                                    } else {
                                        echo "<span class='badge bg-danger'>Desligada</span>";
                                    }
                                    ?>
                                </td>
                            </tr>
                            <!-- Servo -->
                            <tr>
                                <td>
                                    <a href="history.php?nome=servo&nometxt" class="text-decoration-none fw-bold">
                                        ⚙️ <?php echo $angulo["nome"]; ?>
                                    </a>
                                </td>
                                <td><?php echo formatNumber($angulo['valor']); ?>º</td>
                                <td><?php echo $angulo["hora"]; ?></td>
                                <td>
                                    <?php
                                    if ($angulo["valor"] >= 80.00) {
                                        echo "<span class='badge bg-success'>Fechado</span>";
                                    } elseif ($angulo["valor"] < 80.00) {
                                        echo "<span class='badge bg-danger'>Aberto</span>";
                                    } else {
                                        echo "<span class='badge bg-secondary'>Erro</span>";
                                    }
                                    ?>
                                </td>
                            </tr>
                            <!-- Led -->
                            <tr>
                                <td>
                                    <a href="history.php?nome=led&nometxt" class="text-decoration-none fw-bold">
                                        💡 <?php echo $led["nome"]; ?>
                                    </a>
                                </td>
                                <td>
                                    <?php
                                    if ($led["valor"] == 1) {
                                        echo "Ligado";
                                    } else {
                                        echo "Desligado";
                                    }
                                    ?>
                                </td>
                                <td><?php echo $led["hora"]; ?></td>
                                <td>
                                    <?php
                                    if ($led["valor"] == 1) {
                                        echo "<span class='badge bg-success'>Ligado</span>";
                                    } else {
                                        echo "<span class='badge bg-danger'>Desligado</span>";
                                    }
                                    ?>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
            <?php include("add-ons/footer.php"); ?>
        </div>
    </div>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/js/all.min.js" defer></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" defer></script>
    <script src="assets/script.js" defer></script>
</body>

</html>