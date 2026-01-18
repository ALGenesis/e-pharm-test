<?php

function handleLogout() {
    require_once ROOT_PATH . '/utils/auth.utils.php';
    set_session();
    session_unset();
    session_destroy();
    header("Location: /index.php");
    exit();
}