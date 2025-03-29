<?php
session_start();

if (isset($_SESSION['username'])) { // Verifica se o utilizador está logado
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

    <body class="bg-light">
        <div class="container d-flex justify-content-center align-items-center vh-100">
            <div class="card shadow-lg" style="width: 100%; max-width: 500px;">
                <div class="card-body text-center">
                    <h1 class="card-title">You are logged out.</h1>
                    <p class="card-text">Redirecting to the login page in <span id="countdown">5</span> seconds...</p>
                    <form action="login.php" method="post">
                        <button type="submit" class="btn btn-primary mt-3">Go back to login page</button>
                    </form>
                </div>
            </div>
        </div>

        <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.3/dist/umd/popper.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    </body>

    </html>
<?php } else {
    header('Location: login.php');
    exit;
}
?>