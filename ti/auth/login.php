<?php
session_start();
include_once('credential.php');

if (isset($_POST['username']) && isset($_POST['pass1'])) {
    $email = $_POST['username'];
    $password = $_POST['pass1'];

    if ($email == $username1 && password_verify($password, $password1) || $email == $username2 && password_verify($password, $password2)) {
        $_SESSION['username'] = $email;
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
    <meta name="author" content="Duarte Lacerda">
    <link rel="icon" href="../assets/imagens/favicon.png" type="image/x-icon">
    <link rel="stylesheet" href="../assets/style.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <script src="assets/script.js" defer></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" defer></script>
</head>

<body>
    <?php
    if (isset($_SESSION['error'])) {
        echo "<script>
                alert('{$_SESSION['error']}');
                window.location.href = 'login.php';
              </script>";
        unset($_SESSION['error']);
        exit();
    }
    ?>
    <img class="background" src="../assets/imagens/bg.svg" alt="Background">
    <div class="container d-flex justify-content-center align-items-center vh-100">
        <form class="AulaForm" action="login.php" method="post">
            <a href="login.php"><img class="formImg" src="../assets/imagens/estg.png" alt="ESTG" class="img-fluid" width="350px"></a>
            <div class="mb-3">
                <label for="username" class="form-label">Username</label>
                <input type="text" class="form-control" id="username" name="username" required>
            </div>
            <div class="mb-3">
                <label for="pass1" class="form-label">Password</label>
                <input type="password" class="form-control" id="pass1" name="pass1" required>
            </div>
            <div class="mb-3 form-check">
                <input type="checkbox" class="form-check-input" id="exampleCheck1">
                <label class="form-check-label" for="exampleCheck1">Check me out</label>
            </div>
            <button type="submit" class="formBtn" style="color: white;">Submit</button>
        </form>
    </div>
    <footer class="bg-body-transparent">
        <div class="d-flex justify-content-between align-items-center">
            <div class="text-center p-3 ms-5">
                <a href="https://duartelacerda.github.io" target="_blank"><img class="footerPic" src="../assets/imagens/signed.png"
                        alt="Duarte Lacerda" class="img-fluid" width="150px"></a>
            </div>
            <div class="text-center p-3 me-5">
                <a href="https://www.ipleiria.pt/curso/licenciatura-em-engenharia-informatica/" target="_blank">
                    <p style="color: white;"><img class="footerPic" src="../assets/imagens/estg.png" alt="ESTG" width="150"></p>
                </a>
            </div>
        </div>
    </footer>
</body>

</html>