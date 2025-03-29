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
    <meta http-equiv="refresh" content="15">
    <link rel="icon" href="assets/imagens/favicon.png" type="image/x-icon">
    <link rel="stylesheet" href="assets/style.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
</head>

<body>
    <nav class="navbar navbar-expand-lg navbar-light bg-light d-flex justify-content-evenly" id="navbar">
        <a class="navbar-brand fw-bold ms-5" href="dashboard.php">Dashboard EI-TI</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"
            aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse justify-content-between me-5" id="navbarNav">
            <ul class="navbar-nav">
                <li class="nav-item active">
                    <a class="nav-link" href="dashboard.php">Home</a>
                </li>
            </ul>
            <form action="auth/logout.php" method="get">
                <button class="btn btn-outline-dark">Logout</button>
            </form>
        </div>
    </nav>
    <div class="container">
        <div class="row mt-4">
            <div id="title-header" class="col-md-6 ps-5">
                <h1>Servidor IoT</h1>
                <h6 id="user-name"><img src="assets/imagens/user.png" alt="Fotografia do utilizador" width="25"> <?php echo $_SESSION['username'] ?></h6>
            </div>
            <div id="logo-header" class="col-md-6 pe-5">
                <a href="https://www.ipleiria.pt/estg" target="_blank"><img class="estg" src="assets/imagens/estg.png"
                        alt="Politécnico de Leiria"></a>
            </div>
        </div>
        <div class="row mt-4">
            <div class="col-sm-4 mb-2">
                <div class="card text-center">
                    <div class="card-header sensor">
                        <p class="mb-0">Temperatura: <?php echo formatNumber($temperatura['valor']); ?>º</p>
                    </div>
                    <div class="card-body">
                        <img src="assets/imagens/temperature-high.png" alt="temperatura">
                    </div>
                    <div class="card-footer">
                        <p class="mb-0"><span class="fw-bold">Atualizado: </span><?php echo $temperatura["hora"]; ?> - <a
                                href="history.php?nome=temperatura">Histórico</a></p>
                    </div>
                </div>
            </div>
            <div class="col-sm-4 mb-2">
                <div class="card text-center">
                    <div class="card-header sensor">
                        <p class="mb-0">Humidade: 70%</p>
                    </div>
                    <div class="card-body">
                        <img src="assets/imagens/humidity-high.png" alt="humidade">
                    </div>
                    <div class="card-footer">
                        <p class="mb-0"><span class="fw-bold">Atualizado: </span>2025/03/09 18:25 - <a
                                href="#">Histórico</a></p>
                    </div>
                </div>
            </div>
            <div class="col-sm-4 mb-2">
                <div class="card text-center">
                    <div class="card-header atuador">
                        <p class="mb-0">Led Arduino: Ligado</p>
                    </div>
                    <div class="card-body">
                        <img src="assets/imagens/light-on.png" alt="led">
                    </div>
                    <div class="card-footer">
                        <p class="mb-0"><span class="fw-bold">Atualizado: </span>2025/03/09 18:25 - <a
                                href="#">Histórico</a></p>
                    </div>
                </div>
            </div>
        </div>
        <div class="row mt-4">
            <div class="col-sm-12">
                <div class="card">
                    <div class="card-header fw-bold">
                        Tabela de Sensores
                    </div>
                    <div class="card-body">
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
                                    <td><?php echo $temperatura["nome"]; ?></td>
                                    <td><?php echo formatNumber($temperatura['valor']); ?>º</td>
                                    <td><?php echo $temperatura["hora"]; ?></td>
                                    <?php
                                    if ($temperatura["valor"] >= 40.00) {
                                        echo "<td><span class='badge bg-danger'>Crítico</span></td>";
                                    } elseif ($temperatura["valor"] > 20.00 && $temperatura["valor"] < 40.00) {
                                        echo "<td><span class='badge bg-warning'>Elevado</span></td>";
                                    } elseif ($temperatura["valor"] <= 20.00) {
                                        echo "<td><span class='badge bg-success'>Normal</span></td>";
                                    }
                                    ?>
                                </tr>
                                <tr>
                                    <td>Humidade</td>
                                    <td>70%</td>
                                    <td>2025/03/10 14:31</td>
                                    <td><span class="badge bg-primary">Normal</span></td>
                                </tr>
                                <tr>
                                    <td>Led Arduino</td>
                                    <td>Ligado</td>
                                    <td>2025/03/10 14:31</td>
                                    <td><span class="badge bg-success">Ativo</span></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <footer class="bg-body-tertiary">
        <div class="d-flex justify-content-between align-items-center">
            <div class="text-center p-3 ms-5">
                <a href="https://duartelacerda.github.io" target="_blank"><img src="assets/imagens/signed.png"
                        alt="Duarte Lacerda" class="img-fluid" width="150"></a>
            </div>
            <div class="text-center p-3 me-5">
                <a href="https://www.ipleiria.pt/curso/licenciatura-em-engenharia-informatica/" target="_blank"><img src="assets/imagens/estg.png" alt="ESTG" width="150"></a>
            </div>
        </div>
    </footer>
    <script src="assets/script.js" defer></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" defer></script>
</body>

</html>