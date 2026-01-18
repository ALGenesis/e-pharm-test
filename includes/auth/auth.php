<?php
require_once dirname(__DIR__, 2) . "/config/config.php";
if($_SERVER['REQUEST_METHOD'] === 'POST') {

    if(isset($_POST['login'])) {
        require_once __DIR__ . '/handler/login.php';
        handleLogin();
    }

    if(isset($_POST['register'])) {
        require_once __DIR__ . '/handler/register.php';
        handleRegister();
    }

    if(isset($_POST['logout'])) {
        require_once __DIR__ . '/handler/logout.php';
        handleLogout();
    }else {
        header('Location: ' . ROOT_PATH . '/index.php');
        die();
    }
} else {
   header('Location: ' . ROOT_PATH . '/index.php');
}