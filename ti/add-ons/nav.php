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