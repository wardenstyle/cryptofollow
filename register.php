<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

include('head.php'); 
//include('nav.php'); 

if (isset($_SESSION['id_u'])) {

    header("Location: index.php");
    exit(); // Arret du script après la redirection

}else{
?>

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
                <a href="#" class="nav-item nav-link">à propos de nous</a>
            </div>
            <div class="h-100 d-lg-inline-flex align-items-center d-none">
                <a class="btn btn-square rounded-circle bg-light text-primary me-2" href=""><i class="fab fa-facebook-f"></i></a>
                <a class="btn btn-square rounded-circle bg-light text-primary me-2" href=""><i class="fab fa-twitter"></i></a>
                <a class="btn btn-square rounded-circle bg-light text-primary me-0" href=""><i class="fab fa-linkedin-in"></i></a>
            </div>
        </div>
</nav>

<div class="d-flex justify-content-center align-items-center vh-100 position-relative" style="top:-150px;">
    <div class="col-lg-6 col-md-8 col-sm-10 col-12">
        <h4 class="text-center">
            <img src="img/hero-2.png" width="25%" alt=""> S'inscrire |<a href="log-in.php"> Se connecter</a>
        </h4>
        <form id="registerForm" class="p-4 border rounded shadow bg-white">
            <div class="mb-3">
                <label for="email" class="form-label">Email</label>
                <input type="email" class="form-control" id="email" required>
            </div>
            <div class="mb-3">
                <label for="password" class="form-label">Mot de passe</label>
                <input type="password" class="form-control" id="password" required>
            </div>
            <button class="btn btn-success w-100" type="submit">S'inscrire</button>
        </form>
    </div>
</div>
<?php 
}
?>
<script src="js/register.js"></script>