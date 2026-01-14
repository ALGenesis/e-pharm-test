<?php
require_once dirname(__DIR__) . '/config/config.php';
require ROOT_PATH . "/config/functions/is_session_active.php";
function is_logged() {
    is_session_active();
    if (isset($_SESSION['user_id'])) {
        return true;
    } else {
        return false;
    }
}

function is_admin() {
    is_session_active();
    if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin') {
        return true;
    } else {
        return false;
    }
}