<?php
require_once dirname(__DIR__) . '/config/config.php';
function is_session_active () {
    if(session_status() === PHP_SESSION_ACTIVE) {
        return;
    } else {
        require ROOT_PATH . '/config/session_start.inc.php';
    }
}