<?php
session_start();
if (!isset($_SESSION['username'])) { // Verifica se o utilizador está logado
    header("refresh:5;url=auth/login.php");
    die("Acesso Restrito");
}

$temperatura = array( // Array associativa para armazenar os dados da temperatura
    "valor" => file_get_contents("api/temperatura/valor.txt"),
    "hora" => file_get_contents("api/temperatura/hora.txt"),
    "nome" => file_get_contents("api/temperatura/nome.txt"),
    "log" => file_get_contents("api/temperatura/log.txt")
);

$servo = array( // Array associativa para armazenar os dados da servo
    "valor" => file_get_contents("api/servo/valor.txt"),
    "hora" => file_get_contents("api/servo/hora.txt"),
    "nome" => file_get_contents("api/servo/nome.txt"),
    "log" => file_get_contents("api/servo/log.txt")
);

$us = array( // Array associativa para armazenar os dados da ultrasonico
    "valor" => file_get_contents("api/ultrasonico/valor.txt"),
    "hora" => file_get_contents("api/ultrasonico/hora.txt"),
    "nome" => file_get_contents("api/ultrasonico/nome.txt"),
    "log" => file_get_contents("api/ultrasonico/log.txt")
);

function formatNumber($number)
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
    <div class="container">
        <?php include("add-ons/nav.php"); ?>
        <div class="row mt-4">
            <div id="title-header" class="col-md-6">
                <h1>Servidor IoT</h1>
                <h6 class="user-logo" id="user-name"><img src="assets/imagens/user.png" alt="Fotografia do utilizador" width="25"> <?php echo $_SESSION['username'] ?></h6>
            </div>
            <div id="logo-header" class="col-md-6">
                <a href="https://www.ipleiria.pt/estg" target="_blank"><img class="estg" src="assets/imagens/estg.png"
                        alt="Politécnico de Leiria"></a>
            </div>
        </div>
        <div class="row mt-2 mb-2">
            <div class="col-sm-4 mb-2">
                <div class="card bg-c-green order-card">
                    <div class="card-block">
                        <h6 class="m-b-20"><strong><?php echo $temperatura["nome"]; ?></strong></h6>
                        <h3 class="text-right"><i class="fa-solid fa-temperature-half pulse"></i><span> <?php echo $temperatura["valor"]; ?>ºC</span></h3>
                        <p class="m-b-0"><strong>Last updatde: <?php echo $temperatura["hora"]; ?><span class="f-right"><a href="history.php?nome=temperatura&nometxt">Histórico</a></strong></span></p>
                    </div>
                </div>
            </div>
            <div class="col-sm-4 mb-2">
                <div class="card bg-c-blue order-card">
                    <div class="card-block">
                        <h6 class="m-b-20"><strong>Humidade</strong></h6>
                        <h3 class="text-right"><i class="fa-solid fa-droplet pulse"></i><span> 53%</span></h3>
                        <p class="m-b-0"><strong>Last updatde: 2025/04/07 15:47:33<span class="f-right"><a href="history.php?nome=humidade&nometxt">Histórico</a></strong></span></p>
                    </div>
                </div>
            </div>
            <div class="col-sm-4 mb-2">
                <div class="card bg-c-yellow order-card">
                    <div class="card-block">
                        <h6 class="m-b-20"><strong><?php echo $us["nome"]; ?></strong></h6>
                        <h3 class="text-right"><i class="fa-solid fa-people-arrows pulse"></i><span> <?php echo $us["valor"]; ?>º</span></h3>
                        <p class="m-b-0"><strong>Last updatde: <?php echo $us["hora"]; ?><span class="f-right"><a href="history.php?nome=ultrasonico&nometxt">Histórico</a></strong></span></p>
                    </div>
                </div>
            </div>
        </div>
        <div class="row mt-2 mb-2">
            <div class="col-sm-4 mb-2">
                <div class="card bg-c-orange order-card">
                    <div class="card-block">
                        <h6 class="m-b-20"><strong>Ventoinha</strong></h6>
                        <h3 class="text-right"><i class="fa-solid fa-fan spin"></i><span> 59 RPM</span></h3>
                        <p class="m-b-0"><strong>Last updatde: 2025/04/07 15:47:33<span class="f-right"><a href="history.php?nome=ventoinha&nometxt">Histórico</a></strong></span></p>
                    </div>
                </div>
            </div>
            <div class="col-sm-4 mb-2">
                <div class="card bg-c-pink order-card">
                    <div class="card-block">
                        <h6 class="m-b-20"><strong><?php echo $servo["nome"]; ?></strong></h6>
                        <h3 class="text-right"><i class="fa-solid fa-door-open pulse"></i><span> <?php echo $servo["valor"]; ?>º</span></h3>
                        <p class="m-b-0"><strong>Last updatde: 2025/04/07 15:47:33<span class="f-right"><a href="history.php?nome=servo&nometxt">Histórico</a></strong></span></p>
                    </div>
                </div>
            </div>
            <div class="col-sm-4 mb-2">
                <div class="card bg-c-purple order-card">
                    <div class="card-block">
                        <h6 class="m-b-20"><strong>Led</strong></h6>
                        <h3 class="text-right"><i class="fa-solid fa-sun spin_reverse"></i></i><span> Ligado</span></h3>
                        <p class="m-b-0"><strong>Last updatde: 2025/04/07 15:47:33<span class="f-right"><a href="history.php?nome=led&nometxt">Histórico</a></strong></span></p>
                    </div>
                </div>
            </div>
        </div>
        <div class="row mt-4">
            <h2>Tabela de Sensores</h2>
            <div class="table-responsive-sm">
                <table class="table table-bordered table-striped text-center">
                    <thead class="table-dark">
                        <tr>
                            <th>Tipo de Dispositivo IoT</th>
                            <th>Valor</th>
                            <th>Data de Atualização</th>
                            <th>Estado Alertas</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td><a href="history.php?nome=temperatura&nometxt"><?php echo $temperatura["nome"]; ?></a></td>
                            <td><?php echo formatNumber($temperatura['valor']); ?>º</td>
                            <td><?php echo $temperatura["hora"]; ?></td>
                            <?php
                            switch (true) {
                                case ($temperatura["valor"] >= 40.00):
                                    echo "<td><span class='badge bg-danger'>Crítico</span></td>";
                                    break;
                                case ($temperatura["valor"] > 20.00 && $temperatura["valor"] < 40.00):
                                    echo "<td><span class='badge bg-warning'>Elevado</span></td>";
                                    break;
                                case ($temperatura["valor"] <= 20.00):
                                    echo "<td><span class='badge bg-success'>Normal</span></td>";
                                    break;
                                default:
                                    echo "<td class='badge bg-secondary'>Número negativo | Erro de sensor!!</td>";
                                    break;
                            }
                            ?>
                        </tr>
                        <tr>
                            <td><a href="history.php?nome=servo&nometxt"><?php echo $servo["nome"]; ?></a></td>
                            <td><?php echo formatNumber($servo['valor']); ?>º</td>
                            <td><?php echo $servo["hora"]; ?></td>
                            <?php
                            switch (true) {
                                case "servo":
                                    if ($servo["valor"] >= 80.00) {
                                        echo "<td><span class='badge bg-success'>Fechado</span></td>";
                                    } elseif ($servo["valor"] < 80.00) {
                                        echo "<td><span class='badge bg-danger'>Aberto</span></td>";
                                    }
                                    break;
                                default:
                                    echo "<td class='badge bg-secondary'>Número negativo | Erro de sensor!!</td>";
                                    break;
                            }
                            ?>
                        </tr>
                        <tr>
                            <td><a href="history.php?nome=ultrasonico&nometxt"><?php echo $us["nome"]; ?></a></td>
                            <td><?php echo formatNumber($us["valor"]); ?>cm</td>
                            <td><?php echo $us["hora"]; ?></td>
                            <?php
                            switch (true) {
                                case ($us["valor"] >= 100.00):
                                    echo "<td><span class='badge bg-danger'>Muito Longe</span></td>";
                                    break;
                                case ($us["valor"] > 50.00 && $us["valor"] < 100.00):
                                    echo "<td><span class='badge bg-danger'>Longe</span></td>";
                                    break;
                                case ($us["valor"] <= 50.00 && $us["valor"] > 20.00):
                                    echo "<td><span class='badge bg-warning'>+/- Longe</span></td>";
                                    break;
                                case ($us["valor"] <= 20.00 && $us["valor"] > 10.00):
                                    echo "<td><span class='badge bg-warning'>Perto</span></td>";
                                    break;
                                case ($us["valor"] <= 10.00 && $us["valor"] > 0.00):
                                    echo "<td><span class='badge bg-success'>Muito Perto</span></td>";
                                    break;
                                default:
                                    echo "<td class='badge bg-secondary'>Número negativo | Erro de sensor!!</td>";
                                    break;
                            }
                            ?>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
        <?php include("add-ons/footer.php"); ?>
    </div>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/js/all.min.js" defer></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" defer></script>
    <script src="assets/script.js" defer></script>
</body>

</html>