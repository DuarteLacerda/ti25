<?php
session_start();
$logFilePath = "credential.txt"; // Caminho do ficheiro de log
$logData = file($logFilePath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
$historico = [];

foreach ($logData as $linha) { // Processar cada linha do ficheiro
    $dados = explode(";", $linha); // Separar valores por ";"
    if (count($dados) == 2) {
        $historico[] = [
            "username" => trim($dados[0]), // O email
            "password" => trim($dados[1])  // A password (hash)
        ];
    }
}

if (isset($_POST['username']) && isset($_POST['password'])) { // Verificar se o utilizador preencheu os campos
    $email = $_POST['username'];
    $password = $_POST['password'];
    $loginSuccess = false;

    foreach ($historico as $user) {
        if ($email === $user['username'] && password_verify($password, $user['password'])) {
            $loginSuccess = true;
            break;
        }
    }

    if ($loginSuccess) { // Login bem-sucedido
        $_SESSION['loggedin'] = true; // Definir a sessão como logada
        $_SESSION['username'] = $email;
        if (isset($_POST['remember'])) {
            setcookie('username', $email, time() + (86400 * 30), "/"); // 30 dias
        }
        header('Location: ../dashboard.php');
        exit();
    } else {
        $_SESSION['error'] = "Email ou password incorretos!";
        header('Location: login.php');
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="pt">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TI - Servidor IoT</title>
    <link rel="icon" href="../assets/imagens/favicon.png" type="image/x-icon">
    <link rel="stylesheet" href="../assets/style.css">
    <link rel="stylesheet" href="../assets/login.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
</head>

<body>
    <?php
    $email_cookie = isset($_COOKIE['username']) ? $_COOKIE['username'] : '';
    if (isset($_SESSION['error'])) {
        echo "<script>
                alert('{$_SESSION['error']}');
                window.location.href = 'login.php';
              </script>";
        unset($_SESSION['error']);
        exit();
    }
    ?>
    <div id="preloader" class="loader-container">
        <div class="loader"></div>
    </div>
    <div id="site-content">
        <img class="background" src="../assets/imagens/bg.jpeg" alt="Background">
        <div class="container d-flex justify-content-center align-items-center vh-100">
            <form class="AulaForm" action="login.php" method="post">
                <a href="login.php"><img class="img-fluid formImg" src="../assets/imagens/ProduceShop.png" alt="ESTG" width="350"></a>
                <div class="mb-3">
                    <label for="username" class="form-label">Username</label>
                    <input type="text" class="form-control" id="username" name="username" value="<?= htmlspecialchars($_COOKIE['username'] ?? '') ?>" required>
                </div>
                <div class="mb-3">
                    <label for="password" class="form-label">Password</label>
                    <input type="password" class="form-control" id="password" name="password" required>
                </div>
                <div class="mb-3 form-check">
                    <input type="checkbox" class="form-check-input" id="remember" name="remember">
                    <label class="form-check-label" for="remember">Check me out</label>
                </div>
                <button type="submit" class="formBtn" style="color: white;">Submit</button>
            </form>
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
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" defer></script>
</body>

</html>