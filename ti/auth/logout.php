<?php
session_start();

if (isset($_SESSION['loggedin'])) { // Verifica se o utilizador está logado
    session_unset();
    session_destroy();
?>
    <!DOCTYPE html>
    <html lang="en">

    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Logged Out</title>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
        <link rel="shortcut icon" href="assets/imagens/favicon.png" type="image/x-icon">
        <link rel="stylesheet" href="../assets/login.css">
        <script>
            let count = 3;

            function countdown() {
                document.getElementById("countdown").innerText = count;
                if (count === 0) {
                    window.location.href = "login.php";
                } else {
                    count--;
                    setTimeout(countdown, 1000);
                }
            }
            window.onload = countdown;
        </script>
    </head>

    <body>
        <img class="background" src="../assets/imagens/bg.jpeg" alt="Background">
        <div class="container d-flex justify-content-center align-items-center vh-100">
            <div class="card">
                <div class="card-body text-center">
                    <h1 class="card-title">Sessão encerrada</h1>
                    <p class="card-text">Redirecionando para a página de login em <span id="countdown">3</span> segundos...</p>
                    <form action="login.php" method="post">
                        <button type="submit" class="formBtn" style="color: #fff;">Ir para a página de login</button>
                    </form>
                </div>
            </div>
        </div>

        <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    </body>

    </html>
<?php } else {
    header('Location: login.php');
    exit;
}
?>