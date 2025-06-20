<button class="btn btn-outline-dark d-lg-none" id="sidebarToggle">☰</button>

<nav id="sidebar" class="bg-light">
    <div class="p-3">
        <h4>Dashboard</h4>
        <ul class="nav flex-column">

            <li class="nav-item">
                <a class="nav-link" href="dashboard.php">Início</a>
            </li>
            <?php // Verifica se o utilizador é administrador
            if ($_SESSION['permission'] === 'admin') { ?>
                <!-- Gaveta Sensores -->
                <li class="nav-item">
                    <a class="nav-link d-flex justify-content-between align-items-center" data-bs-toggle="collapse" href="#sensoresCollapse" role="button" aria-expanded="false" aria-controls="sensoresCollapse">
                        Sensores <span class="icon-collapse">▼</span>
                    </a>
                    <div class="collapse ps-3" id="sensoresCollapse">
                        <ul class="nav flex-column">
                            <li class="nav-item"><a class="nav-link" href="history.php?nome=temperatura&nometxt">Temperatura</a></li>
                            <li class="nav-item"><a class="nav-link" href="history.php?nome=humidade&nometxt">Humidade</a></li>
                            <li class="nav-item"><a class="nav-link" href="history.php?nome=distancia&nometxt">Distancia</a></li>
                        </ul>
                    </div>
                </li>

                <!-- Gaveta Atuadores -->
                <li class="nav-item">
                    <a class="nav-link d-flex justify-content-between align-items-center" data-bs-toggle="collapse" href="#atuadoresCollapse" role="button" aria-expanded="false" aria-controls="atuadoresCollapse">
                        Atuadores <span class="icon-collapse">▼</span>
                    </a>
                    <div class="collapse ps-3" id="atuadoresCollapse">
                        <ul class="nav flex-column">
                            <li class="nav-item"><a class="nav-link" href="history.php?nome=ventoinha&nometxt">Ventoinha</a></li>
                            <li class="nav-item"><a class="nav-link" href="history.php?nome=cancela&nometxt">Cancela</a></li>
                            <li class="nav-item"><a class="nav-link" href="history.php?nome=led&nometxt">LED</a></li>
                        </ul>
                    </div>
                </li>

                <!-- Botão Webcam -->
                <li class="nav-item">
                    <a class="nav-link" href="history.php?nome=webcam&nometxt">Webcam</a>
                </li>
            <?php } ?>


            <!-- Logout -->
            <li class="nav-item mt-3">
                <form action="auth/logout.php" method="get">
                    <button class="btn btn-outline-dark w-100">Terminar Sessão</button>
                </form>
            </li>
        </ul>
        <div class="hr"></div>
        <div class="nav-img">
            <a href="https://www.ipleiria.pt/" target="_blank"><img src="assets/imagens/estg.png" alt="Logo" class="img-fluid"></a>
        </div>
    </div>
</nav>