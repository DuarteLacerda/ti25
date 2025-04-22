<?php
session_start();
if (!isset($_SESSION['loggedin'])) { // Verifica se o utilizador está logado
    header("refresh:3;url=auth/login.php");
    die("Acesso não autorizado. <a href='auth/login.php'>Clique aqui</a> se não for redirecionado automaticamente.");
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
    <div id="preloader" class="loader-container">
        <div class="loader"></div>
    </div>
    <div id="site-content">
        <div id="content">
            <div class="container-fluid">
                <?php include("add-ons/nav.php"); ?>
                <div class="row mt-4 mb-2" id="header" style="padding-left: 35px; padding-right: 25px;">
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
                                            <button onclick="location.href='history.php?nome=temperatura&nometxt'" class="btn btn-outline-dark text-decoration-none fw-bold">Histórico
                                            </button></span></strong></p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4 col-sm-12 mb-2">
                        <div class="card bg-c-blue order-card">
                            <div class="card-block">
                                <h6 class="m-b-20"><strong><span id="nome-distancia"></span></strong></h6>
                                <h3 class="text-right"><i class="fa-solid fa-people-arrows pulse"></i><span id="valor-distancia"></span></h3>
                                <p class="m-b-0"><strong>Ultima atualização: <span id="hora-distancia"></span><span class="f-right">
                                            <button onclick="location.href='history.php?nome=distancia&nometxt'" class="btn btn-outline-dark text-decoration-none fw-bold">Histórico
                                            </button></span></strong></p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4 col-sm-12 mb-2">
                        <div class="card bg-c-yellow order-card">
                            <div class="card-block">
                                <h6 class="m-b-20"><strong><span id="nome-humidade"></span></strong></h6>
                                <h3 class="text-right"><i class="fa-solid fa-droplet pulse"></i><span id="valor-humidade"></span></h3>
                                <p class="m-b-0"><strong>Ultima atualização: <span id="hora-humidade"></span><span class="f-right">
                                            <button onclick="location.href='history.php?nome=humidade&nometxt'" class="btn btn-outline-dark text-decoration-none fw-bold">Histórico
                                            </button></span></strong></p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4 col-sm-12 mb-2">
                        <div class="card bg-c-orange order-card">
                            <div class="card-block">
                                <h6 class="m-b-20"><strong><span id="nome-ventoinha"></span></strong></h6>
                                <h3 class="text-right"><i class="fa-solid fa-fan spin"></i><span id="valor-ventoinha"></span></h3>
                                <p class="m-b-0"><strong>Ultima atualização: <span id="hora-ventoinha"></span><span class="f-right">
                                            <button onclick="location.href='history.php?nome=ventoinha&nometxt'" class="btn btn-outline-dark text-decoration-none fw-bold">Histórico
                                            </button></span></strong></p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4 col-sm-12 mb-2">
                        <div class="card bg-c-pink order-card">
                            <div class="card-block">
                                <h6 class="m-b-20"><strong><span id="nome-cancela"></span></strong></h6>
                                <h3 class="text-right"><i class="fa-solid fa-door-open pulse"></i><span id="valor-cancela"></span></h3>
                                <p class="m-b-0"><strong>Ultima atualização: <span id="hora-cancela"></span><span class="f-right">
                                            <button onclick="location.href='history.php?nome=cancela&nometxt'" class="btn btn-outline-dark text-decoration-none fw-bold">Histórico
                                            </button></span></strong></p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4 col-sm-12 mb-2">
                        <div class="card bg-c-purple order-card">
                            <div class="card-block">
                                <h6 class="m-b-20"><strong><span id="nome-led"></span></strong></h6>
                                <h3 class="text-right"><i class="fa-solid fa-sun spin_reverse"></i><span id="valor-led"></span></h3>
                                <p class="m-b-0"><strong>Ultima atualização: <span id="hora-led"></span><span class="f-right">
                                            <button onclick="location.href='history.php?nome=led&nometxt'" class="btn btn-outline-dark text-decoration-none fw-bold">Histórico
                                            </button></span></strong></p>
                            </div>
                        </div>
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
                            <tbody>
                                <!-- Ventoinha -->
                                <tr onclick="location.href='history.php?nome=ventoinha&nometxt'" style="cursor: pointer;">
                                    <td>🌬️ <span id="nome-ventoinha"></span></td>
                                    <td><span id="valor-ventoinha"></span> RPM</td>
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