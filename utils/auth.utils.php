<?php
require_once dirname(__DIR__) . '/config/config.php';
require_once ROOT_PATH . "/utils/is_session_active.php";

function is_logged() {
    set_session();
    if (isset($_SESSION['user_id'])) {
        return true;
    } else {
        return false;
    }
}

function is_admin($user = null) {
    if(isset($user)) {
        return check_role($user) === 'admin';
    }
    
    set_session();
    if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin') {
        return true;
    } else {
        return false;
    }
}

function check_role($user) {
    if(isset($user['role']) && $user['role'] === 'admin') {
        return 'admin';
    } else {
        return ;
    }   
}

function is_email_valid($email) {
    if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
        return true;
    } else {
        return false;
    }
}

function is_password_solid($password) {
    $pattern = "/^(?=.*[A-Za-z])(?=.*\d)[A-Za-z\d@$!%*#?&]{6,}$/";
    if (preg_match($pattern, $password)) {
        return true;
    } else {
        return false;
    }
}

function set_user_session($user) {
    if(isset($user) && isset($user['password'])) {
        unset($user['password']);
    }

    set_session();
    $_SESSION['user_id'] = $user['id'];
    $_SESSION['username'] = $user['username'];
    $_SESSION['email'] = $user['email'];
    $_SESSION['role'] = check_role($user);
}