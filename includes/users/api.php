<?php
require_once dirname(__DIR__, 2) . "/config/config.php";
require_once ROOT_PATH . '/config/database.php';
require_once ROOT_PATH . '/utils/is_session_active.php';
require_once ROOT_PATH . '/utils/users.utils.php';
require_once ROOT_PATH . '/utils/auth.utils.php';
require_once ROOT_PATH . '/utils/api.utils.php';

header('Content-Type: application/json');

if(!is_logged()) {
    sendJsonResponse(null, 401, 'Utilisateur non authentifié');
    http_response_code(401);
    exit;
}

switch ($_SERVER['REQUEST_METHOD']) {
    case 'GET':
        require_once ROOT_PATH . '/includes/users/routes/GET.php';
        GET();
        break;

    case 'POST':
        require_once ROOT_PATH . '/includes/users/routes/POST.php';
        POST();
        break;

    case 'PUT':
        require_once ROOT_PATH . '/includes/users/routes/PUT.php';
        PUT();
        break;
    
    case 'DELETE':
        require_once ROOT_PATH . '/includes/users/routes/DELETE.php';
        DELETE();
        break;

    default:
        sendJsonResponse(null, 405, 'Méthode non autorisée');
        http_response_code(405);
        exit;
}
