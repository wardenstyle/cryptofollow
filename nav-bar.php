<nav class="navbar navbar-expand-lg bg-white navbar-light sticky-top p-0 px-4 px-lg-5">
        <a href="index.php" class="navbar-brand d-flex align-items-center">
            <h2 class="m-0 text-primary"><img class="img-fluid me-2" src="img/icon-1.png" alt="" style="width: 45px;">CryptoFollow</h2>
        </a>
        <button type="button" class="navbar-toggler" data-bs-toggle="collapse" data-bs-target="#navbarCollapse">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarCollapse">
            <div class="navbar-nav ms-auto py-4 py-lg-0">
                <a href="index.php" class="nav-item nav-link active">Home</a>
            <?php if(isset($_SESSION['id_u'])) { ?><a href="logout.php" class="nav-item nav-link">Déconnexion</a> 
                <div class="nav-item dropdown">
                    <a href="#" class="nav-link dropdown-toggle" data-bs-toggle="dropdown">Profil</a>
                    <div class="dropdown-menu shadow-sm m-0">
                        <a href="price_crypto.php" class="dropdown-item">Nouveau marqueur</a>
                        <a href="markers_crypto.php" class="dropdown-item">Mes marqueurs</a>
                    </div>
                </div>
            <?php } 
            else {?>
                <a href="log-in.php" class="nav-item nav-link">Se connecter / s'inscrire</a>
            <?php }?>
                <a href="#" class="nav-item nav-link">à propos de nous</a>
            </div>
            <div class="h-100 d-lg-inline-flex align-items-center d-none">
                <a class="btn btn-square rounded-circle bg-light text-primary me-2" href=""><i class="fab fa-facebook-f"></i></a>
                <a class="btn btn-square rounded-circle bg-light text-primary me-2" href=""><i class="fab fa-twitter"></i></a>
                <a class="btn btn-square rounded-circle bg-light text-primary me-0" href=""><i class="fab fa-linkedin-in"></i></a>
            </div>
            <button id="themeToggle" class="btn btn-outline-secondary ms-3 rounded-circle" title="Mode nuit">
                <i class="fas fa-moon"></i>
            </button>
        </div>
</nav>