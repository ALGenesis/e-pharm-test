<?php
require_once ROOT_PATH . '/utils/users.utils.php';
require_once ROOT_PATH . '/utils/is_session_active.php';
require_once ROOT_PATH . '/utils/auth.utils.php';

function handleLogin() {
    // Validate input
    if(empty(trim($_POST['email'])) || empty(trim($_POST['password']))) {
        set_session();
        $_SESSION['error'] = ['login' => 'l\'email et le mot de passe sont requis.'];
        header('Location: /pages/auth.php');
        exit();
    }

    $email = trim($_POST['email']);
    $password = trim($_POST['password']);


    $user = getUserByEmail($email, true); 

    if(!$user || !password_verify($password, $user['password'])) {
        $_SESSION['error'] = ['login' => 'Username and password are required.'];
        header('Location: /pages/auth.php');
        exit();
    }

    // Start session and set user _POST
    set_user_session($user);

    if(is_admin()) {
        header('Location: pages/admin/dashboard.php');
        exit();
    }

    header('Location: /index.php');
    exit();
}