<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

//include('head.php');
include('nav.php');

if (isset($_SESSION['id_u'])) {



} else {
    header('Location: log-in.php');
    exit();
}