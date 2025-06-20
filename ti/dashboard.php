<?php
session_start();
if (!isset($_SESSION['loggedin'])) { // Verifica se o utilizador está logado
    header("refresh:3;url=auth/login.php");
    die("Acesso não autorizado. <a href='auth/login.php'>Clique aqui</a> se não for redirecionado automaticamente.");
}

// Exemplo: guardar comandos num ficheiro txt para o backend IoT ler
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['ventoinha'])) {
        $estadoVentoinha = $_POST['ventoinha'] === "1" ? "1" : "0";
        file_put_contents('api/ventoinha/valor.txt', $estadoVentoinha);
    }
    if (isset($_POST['cancela'])) {
        $posicaoCancela = intval($_POST['cancela']);
        file_put_contents('api/cancela/valor.txt', $posicaoCancela);
    }
    if (isset($_POST['led'])) {
        $estadoLed = $_POST['led'] === "1" ? "1" : "0";
        file_put_contents('api/led/valor.txt', $estadoLed);
    }
}

$ventoinha_atual = file_exists('api/ventoinha/valor.txt') ? file_get_contents('api/ventoinha/valor.txt') : "0";
$cancela_atual = file_exists('api/cancela/valor.txt') ? file_get_contents('api/cancela/valor.txt') : "0";
$led_atual = file_exists('api/led/valor.txt') ? file_get_contents('api/led/valor.txt') : "0";
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
    <div id="preloader" class="loader-container">
        <div class="loader"></div>
    </div>
    <div id="site-content">
        <div id="content">
            <div class="container-fluid">
                <?php include("add-ons/nav.php"); ?>
                <div class="row mt-2 mb-2" id="header" style="padding-left: 35px; padding-right: 25px;">
                    <div id="title-header" class="col-md-6 col-sm-5">
                        <h1>Servidor IoT</h1>
                        <h6 class="user-logo" id="user-name"><img src="assets/imagens/user.png" alt="Fotografia do utilizador" width="25"> <?php echo $_SESSION['username'] ?></h6>
                    </div>
                    <div id="logo-header" class="col-md-6 col-sm-7">
                        <img class="estg" src="assets/imagens/ProduceShop.png" alt="Logotipo do supermercado Produce Shop">
                    </div>
                </div>
                <div class="row mt-2 mb-2">
                    <div class="col-md-4 col-sm-12 mb-2">
                        <div class="card bg-c-green order-card">
                            <div class="card-block">
                                <h6 class="m-b-20"><strong><span id="nome-temperatura"></span></strong></h6>
                                <h3 class="text-right"><i class="fa-solid fa-temperature-half pulse"></i><span id="valor-temperatura"></span></h3>
                                <p class="m-b-0"><strong>Ultima atualização: <span id="hora-temperatura"></span><span class="f-right">
                                            <?php
                                            // Verifica se o utilizador é um administrador
                                            if ($_SESSION['permission'] === 'admin' || $_SESSION['permission'] === 'mod') { ?>
                                                <button onclick="location.href='history.php?nome=temperatura&nometxt'" class="botao btn btn-outline-dark text-decoration-none fw-bold">Histórico
                                                </button>
                                            <?php } ?></span></strong></p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4 col-sm-12 mb-2">
                        <div class="card bg-c-blue order-card">
                            <div class="card-block">
                                <h6 class="m-b-20"><strong><span id="nome-distancia"></span></strong></h6>
                                <h3 class="text-right"><i class="fa-solid fa-people-arrows pulse"></i><span id="valor-distancia"></span></h3>
                                <p class="m-b-0"><strong>Ultima atualização: <span id="hora-distancia"></span><span class="f-right">
                                            <?php
                                            // Verifica se o utilizador é um administrador
                                            if ($_SESSION['permission'] === 'admin' || $_SESSION['permission'] === 'mod') { ?>
                                                <button onclick="location.href='history.php?nome=temperatura&nometxt'" class="botao btn btn-outline-dark text-decoration-none fw-bold">Histórico
                                                </button>
                                            <?php } ?></span></strong></p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4 col-sm-12 mb-2">
                        <div class="card bg-c-yellow order-card">
                            <div class="card-block">
                                <h6 class="m-b-20"><strong><span id="nome-humidade"></span></strong></h6>
                                <h3 class="text-right"><i class="fa-solid fa-droplet pulse"></i><span id="valor-humidade"></span></h3>
                                <p class="m-b-0"><strong>Ultima atualização: <span id="hora-humidade"></span><span class="f-right">
                                            <?php
                                            // Verifica se o utilizador é um administrador
                                            if ($_SESSION['permission'] === 'admin' || $_SESSION['permission'] === 'mod') { ?>
                                                <button onclick="location.href='history.php?nome=temperatura&nometxt'" class="botao btn btn-outline-dark text-decoration-none fw-bold">Histórico
                                                </button>
                                            <?php } ?></span></strong></p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4 col-sm-12 mb-2">
                        <div class="card bg-c-orange order-card">
                            <div class="card-block">
                                <h6 class="m-b-20"><strong><span id="nome-ventoinha"></span></strong></h6>
                                <h3 class="text-right"><i class="fa-solid fa-fan spin"></i><span id="valor-ventoinha"></span>
                                    <?php
                                    // Verifica se o utilizador é um administrador
                                    if ($_SESSION['permission'] === 'admin') { ?>
                                        <span class="f-right">
                                            <form action="" method="post">
                                                <input type="hidden" name="ventoinha" value="<?php echo $ventoinha_atual === '1' ? '0' : '1'; ?>">
                                                <button type="submit" class="botao btn btn-outline-dark text-decoration-none fw-bold">
                                                    <span><?php echo $ventoinha_atual === '1' ? 'Desligar' : 'Ligar'; ?></span>
                                                </button>
                                            </form>
                                        </span>
                                    <?php } ?>
                                </h3>
                                <p class="m-b-0"><strong>Ultima atualização: <span id="hora-ventoinha"></span>
                                        <?php
                                        // Verifica se o utilizador é um administrador
                                        if ($_SESSION['permission'] === 'admin' || $_SESSION['permission'] === 'mod') { ?>
                                            <span class="f-right">
                                                <button onclick="location.href='history.php?nome=ventoinha&nometxt'" class="botao btn btn-outline-dark text-decoration-none fw-bold">Histórico
                                                </button></span>
                                        <?php } ?>
                                    </strong></p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4 col-sm-12 mb-2">
                        <div class="card bg-c-pink order-card">
                            <div class="card-block">
                                <h6 class="m-b-20"><strong><span id="nome-cancela"></span></strong></h6>
                                <h3 class="text-right"><i class="fa-solid fa-door-open pulse"></i><span id="valor-cancela"></span>
                                    <?php
                                    // Verifica se o utilizador é um administrador
                                    if ($_SESSION['permission'] === 'admin') { ?>
                                        <span class="f-right">
                                            <form action="" method="post">
                                                <input type="hidden" name="cancela" value="<?php echo $cancela_atual === '1' ? '-1' : '1'; ?>">
                                                <button type="submit" class="botao btn btn-outline-dark text-decoration-none fw-bold">
                                                    <span><?php echo $cancela_atual === '1' ? 'Fechar' : 'Abrir'; ?></span>
                                                </button>
                                            </form>
                                        </span>
                                    <?php } ?>
                                </h3>
                                <p class="m-b-0"><strong>Ultima atualização: <span id="hora-cancela"></span>
                                        <?php
                                        // Verifica se o utilizador é um administrador
                                        if ($_SESSION['permission'] === 'admin' || $_SESSION['permission'] === 'mod') { ?>
                                            <span class="f-right">
                                                <button onclick="location.href='history.php?nome=cancela&nometxt'" class="botao btn btn-outline-dark text-decoration-none fw-bold">Histórico
                                                </button></span>
                                        <?php } ?>
                                    </strong></p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4 col-sm-12 mb-2">
                        <div class="card bg-c-purple order-card">
                            <div class="card-block">
                                <h6 class="m-b-20"><strong><span id="nome-led"></span></strong></h6>
                                <h3 class="text-right"><i class="fa-solid fa-sun spin_reverse"></i><span id="valor-led"></span>
                                    <?php
                                    // Verifica se o utilizador é um administrador
                                    if ($_SESSION['permission'] === 'admin') { ?>
                                        <span class="f-right">
                                            <form action="" method="post">
                                                <input type="hidden" name="led" value="<?php echo ($led_atual != '0') ? '0' : '1'; ?>">
                                                <button type="submit" class="botao btn btn-outline-dark text-decoration-none fw-bold">
                                                    <span><?php echo ($led_atual != 'desligado') ? 'Desligar' : 'Ligar'; ?></span>
                                                </button>
                                            </form>
                                        </span>
                                    <?php } ?>
                                </h3>
                                <p class="m-b-0"><strong>Ultima atualização: <span id="hora-led"></span>
                                        <?php
                                        // Verifica se o utilizador é um administrador
                                        if ($_SESSION['permission'] === 'admin' || $_SESSION['permission'] === 'mod') { ?>
                                            <span class="f-right">
                                                <button onclick="location.href='history.php?nome=led&nometxt'" class="botao btn btn-outline-dark text-decoration-none fw-bold">Histórico
                                                </button></span>
                                        <?php } ?>
                                    </strong></p>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-4 mb-2">
                    </div>
                    <div class="col-md-4 mb-2">
                        <div class="card bg-c-gray order-card">
                            <div class="card-block">
                                <h6 class="m-b-20"><strong>Webcam</strong></h6>
                                <div class="d-flex flex-columns align-items-center justify-content-center">
                                    <div class="d-flex flex-column align-items-center justify-content-center">
                                        <img id="imagem-webcam" alt="Imagem da webcam" class="img-fluid rounded">
                                        <p class="mt-2"><strong>Ultima atualização: <span id="hora-webcam"></span></strong></p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4 mb-2">
                    </div>
                </div>
                <div class="row mt-4 mb-2">
                    <h2>Tabela de Sensores</h2>
                    <div class="table-responsive-sm">
                        <table id="tabela-sensores" class="table table-hover text-center align-middle">
                            <thead class="table-dark">
                                <tr>
                                    <th>Sensor</th>
                                    <th>Valor</th>
                                    <th>Data</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <?php // Verifica se o utilizador é um administrador
                            if ($_SESSION['permission'] === 'admin' || $_SESSION['permission'] === 'mod') { ?>
                                <tbody>
                                    <!-- Temperatura -->
                                    <tr onclick="location.href='history.php?nome=temperatura&nometxt'" style="cursor: pointer;">
                                        <td>🌡️ <span id="nome-temperatura"></span></td>
                                        <td><span id="valor-temperatura"></span>ºC</td>
                                        <td><span id="hora-temperatura"></span></td>
                                        <td><span id="status-temperatura"></span></td>
                                    </tr>
                                    <!-- Distancia -->
                                    <tr onclick="location.href='history.php?nome=distancia&nometxt'" style="cursor: pointer;">
                                        <td>📏 <span id="nome-distancia"></span></td>
                                        <td><span id="valor-distancia"></span> cm</td>
                                        <td><span id="hora-distancia"></span></td>
                                        <td><span id="status-distancia"></span></td>
                                    </tr>
                                    <!-- Humidade -->
                                    <tr onclick="location.href='history.php?nome=humidade&nometxt'" style="cursor: pointer;">
                                        <td>💧 <span id="nome-humidade"></span></td>
                                        <td><span id="valor-humidade"></span>%</td>
                                        <td><span id="hora-humidade"></span></td>
                                        <td><span id="status-humidade"></span></td>
                                    </tr>
                                </tbody>
                            <?php } else { ?>
                                <tbody>
                                    <!-- Temperatura -->
                                    <tr>
                                        <td>🌡️ <span id="nome-temperatura"></span></td>
                                        <td><span id="valor-temperatura"></span>ºC</td>
                                        <td><span id="hora-temperatura"></span></td>
                                        <td><span id="status-temperatura"></span></td>
                                    </tr>
                                    <!-- Distancia -->
                                    <tr>
                                        <td>📏 <span id="nome-distancia"></span></td>
                                        <td><span id="valor-distancia"></span> cm</td>
                                        <td><span id="hora-distancia"></span></td>
                                        <td><span id="status-distancia"></span></td>
                                    </tr>
                                    <!-- Humidade -->
                                    <tr>
                                        <td>💧 <span id="nome-humidade"></span></td>
                                        <td><span id="valor-humidade"></span>%</td>
                                        <td><span id="hora-humidade"></span></td>
                                        <td><span id="status-humidade"></span></td>
                                    </tr>
                                </tbody>
                            <?php } ?>
                        </table>
                    </div>
                </div>
                <div class="row mt-4 mb-2">
                    <h2>Tabela de Atuadores</h2>
                    <div class="table-responsive-sm">
                        <table id="tabela-sensores" class="table table-hover text-center align-middle">
                            <thead class="table-dark">
                                <tr>
                                    <th>Atuador</th>
                                    <th>Valor</th>
                                    <th>Data</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <?php // Verifica se o utilizador é um administrador
                            if ($_SESSION['permission'] === 'admin') { ?>
                                <tbody>
                                    <!-- Ventoinha -->
                                    <tr onclick="location.href='history.php?nome=ventoinha&nometxt'" style="cursor: pointer;">
                                        <td>🌬️ <span id="nome-ventoinha"></span></td>
                                        <td><span id="valor-ventoinha"></span></td>
                                        <td><span id="hora-ventoinha"></span></td>
                                        <td><span id="status-ventoinha"></span></td>
                                    </tr>
                                    <!-- Cancela -->
                                    <tr onclick="location.href='history.php?nome=cancela&nometxt'" style="cursor: pointer;">
                                        <td>⚙️ <span id="nome-cancela"></span></td>
                                        <td><span id="valor-cancela"></span>º</td>
                                        <td><span id="hora-cancela"></span></td>
                                        <td><span id="status-cancela"></span></td>
                                    </tr>
                                    <!-- Led -->
                                    <tr onclick="location.href='history.php?nome=led&nometxt'" style="cursor: pointer;">
                                        <td>💡 <span id="nome-led"></span></td>
                                        <td><span id="valor-led"></span></td>
                                        <td><span id="hora-led"></span></td>
                                        <td><span id="status-led"></span></td>
                                    </tr>
                                </tbody>
                            <?php } else { ?>
                                <tbody>
                                    <!-- Ventoinha -->
                                    <tr>
                                        <td>🌬️ <span id="nome-ventoinha"></span></td>
                                        <td><span id="valor-ventoinha"></span></td>
                                        <td><span id="hora-ventoinha"></span></td>
                                        <td><span id="status-ventoinha"></span></td>
                                    </tr>
                                    <!-- Cancela -->
                                    <tr>
                                        <td>⚙️ <span id="nome-cancela"></span></td>
                                        <td><span id="valor-cancela"></span>º</td>
                                        <td><span id="hora-cancela"></span></td>
                                        <td><span id="status-cancela"></span></td>
                                    </tr>
                                    <!-- Led -->
                                    <tr>
                                        <td>💡 <span id="nome-led"></span></td>
                                        <td><span id="valor-led"></span></td>
                                        <td><span id="hora-led"></span></td>
                                        <td><span id="status-led"></span></td>
                                    </tr>
                                </tbody>
                            <?php } ?>
                        </table>
                    </div>
                </div>
                <?php include("add-ons/footer.php"); ?>
            </div>
        </div>
    </div>
    <script>
        window.onload = function() {
            const preloader = document.getElementById('preloader');
            const siteContent = document.getElementById('site-content');

            if (preloader && siteContent) {
                setTimeout(() => {
                    preloader.style.opacity = 0;
                    siteContent.style.visibility = 'visible';
                    siteContent.style.opacity = 1;

                    setTimeout(() => {
                        preloader.style.display = 'none';
                    }, 500); // Espera pela transição de opacidade do preloader
                }, 1500); // Espera antes de ocultar o preloader
            }
        };
    </script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/js/all.min.js" defer></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" defer></script>
    <script src="assets/script.js" defer></script>
    <script src="assets/datascript.js" defer></script>
</body>

</html>