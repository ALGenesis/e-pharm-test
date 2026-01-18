<?php
require_once ROOT_PATH . '/utils/users.utils.php';
require_once ROOT_PATH . '/utils/is_session_active.php';
require_once ROOT_PATH . '/utils/auth.utils.php';

function handleRegister() {
    
    $email = trim($_POST["email"]);
    $username = trim($_POST["username"]);
    $password = password_hash((trim($_POST["password"]) ?? null), PASSWORD_BCRYPT);

    set_session();

    if(empty(trim($email)) || empty(trim($username)) || empty(trim($_POST["password"]))) {
        $_SESSION['error'] = ['register' => 'Tous les champs sont requis.'];
        header('Location: /pages/auth.php');  
        exit();
    }

    if(!is_password_solid(trim($_POST["password"]))) {
        $_SESSION['error'] = ['register' => 'Le mot de passe doit contenir au moins 6 caractères, une lettre, un chiffre et un caractère spécial.'];
        header('Location: /pages/auth.php');
        exit();
    }

    if(!is_email_valid($email)) {
        $_SESSION['error'] = ['register' => 'L\'adresse email n\'est pas valide.'];
        header('Location: /pages/auth.php');
        exit();
    }

    $existingUser = getUserByEmail($email);
    if($existingUser) {
        $_SESSION['error'] = ['register' => 'Un utilisateur avec cet email existe déjà.'];
        header('Location: /pages/auth.php');
        exit();
    }

    $userId = createUser($email, $username, $password);

    $user = getUserById($userId);
    set_user_session($user);

    if(is_admin()) {
        header('Location: pages/admin/dashboard.php');
        exit();
    }

    header('Location: /index.php');
    exit();
    
}