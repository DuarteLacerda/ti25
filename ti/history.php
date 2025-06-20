<?php
session_start();
if (!isset($_SESSION['loggedin'])) { // Verifica se o utilizador está logado
    header("refresh:3;url=auth/login.php");
    die("Acesso não autorizado. <a href='auth/login.php'>Clique aqui</a> se não for redirecionado automaticamente.");
}

function formatNumber($num) // Formata o número para duas casas decimais e remove zeros à direita
{
    return rtrim(rtrim(number_format($num, 2, '.', ''), '0'), '.');
}

if ($_SESSION['permission'] !== 'admin' && $_SESSION['permission'] !== 'mod') { // Verifica se o utilizador é administrador
    header("refresh:3;url=dashboard.php");
    die("Acesso restrito. <a href='dashboard.php'>Clique aqui</a> se não for redirecionado automaticamente.");
}

$nome = $_GET['nome']; // Obter o nome passado na URL
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
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">

</head>

<body>
    <div id="preloader" class="loader-container">
        <div class="loader"></div>
    </div>
    <div id="site-content">
        <div id="content">
            <div class="container-fluid">
                <?php include("add-ons/nav.php"); ?>
                <div class="row mb-4">
                    <div class="col-md-12">
                        <h2 class="mt-5 mb-4">Histórico de <?php echo ucfirst($nome); ?></h2>
                    </div>
                </div>
                <?php
                if ($nome == 'webcam') { ?>
                    <div class="row mt-4">
                        <table id="historico-<?php echo $nome; ?>" class="table table-bordered table-striped text-center">
                            <thead class="table-dark">
                                <tr>
                                    <th>Valor</th>
                                    <th>Data de Atualização</th>
                                </tr>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>
                        <p>
                            <strong>Nota:</strong> O histórico é atualizado automaticamente a cada 3 segundos.
                        </p>
                    </div>
                <?php } else { ?>
                    <div class="row mt-4">
                        <table id="historico-<?php echo $nome; ?>" class="table table-bordered table-striped text-center">
                            <thead class="table-dark">
                                <tr>
                                    <th>Valor</th>
                                    <th>Data de Atualização</th>
                                    <th>Estado Alertas</th>
                                </tr>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>
                        <p>
                            <strong>Nota:</strong> O histórico é atualizado automaticamente a cada 3 segundos.
                        </p>
                    </div>
                <?php } ?>
                <div class="row mt-5 mb-4">
                    <div class="col-md-10 text-center">
                        <canvas id="chartjs-line" width="500" height="250"></canvas>
                    </div>
                    <div class="col-md-2 text-end mt-4 mb-4">
                        <form action="dashboard.php" method="GET">
                            <button class="btn btn-outline-dark">Voltar</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <?php include("add-ons/footer.php"); ?>
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
                    }, 1500); // Espera pela transição de opacidade do preloader
                }, 3500); // Espera antes de ocultar o preloader
            }
        };
    </script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js" defer></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/js/all.min.js" defer></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" defer></script>
    <script src="assets/script.js" defe></script>
    <script src="assets/datascript.js" defer></script>
</body>

</html>