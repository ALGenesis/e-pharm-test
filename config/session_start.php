<?php

ini_set('session.use_only_cookies', 1); 
ini_set('session.use_strict_mode', 1); 


session_set_cookie_params([
    'lifetime' => 1800,
    'path' => '/',
    'domain' => 'e-pharm-test.test',
    'secure' => false,  // ou true si HTTPS
    'httponly' => true,
    'samesite' => 'Strict'  // ou 'Lax' selon vos besoins
]);

session_start();

function refreshSessionId() {
    session_regenerate_id(true);
    $_SESSION['last_refresh_time'] = time();
};

//refresh session ID after a certain interval
if(!isset($_SESSION['last_refresh_time'])) {
    refreshSessionId();
} else {
    $interval = 60 * 30;

    if(time() - $_SESSION['last_refresh_time'] >= $interval) {
        refreshSessionId();
    };
};