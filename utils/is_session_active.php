<?php
require_once dirname(__DIR__) . '/config/config.php';

function set_session () {
    if(session_status() === PHP_SESSION_ACTIVE) {
        return;
    } else {
        require ROOT_PATH . '/config/session_start.php';
    }
}
