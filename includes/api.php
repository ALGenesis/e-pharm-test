<?php
require_once dirname(__DIR__) . '/config/functions/is_session_active.php';
is_session_active();

$data = json_decode(file_get_contents("php://input"), true);
$action = $_GET['action'] ?? '';

if(empty($data)) {
    echo json_encode(["success" => false, "message" => "No data provided"]);
    exit;
}

switch($action) {
    case 'add_cart':
        require ROOT_PATH . "/includes/cart/add.php";
        break;

    case 'remove_cart':
        require ROOT_PATH . "/includes/cart/remove.php";
        break;

    default:
        echo json_encode(["error" => "Action inconnue"]);
        break;
}
