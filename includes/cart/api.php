<?php
require_once   dirname(__DIR__, 2) . "/config/config.php";
require ROOT_PATH . '/config/database.php';
require ROOT_PATH . '/includes/cart/add.php';
require ROOT_PATH . '/includes/cart/remove.php';

if($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    $action = $_GET['action'] ?? "";

    if(empty($data)) {
        http_response_code(400);
        echo json_encode(['sucess' => false, 'message' => 'Aucune donnees fournies']);
    }

    if(empty($action)) {
        $response = [
        'success' => 'true',
        'message' => "Paramètre Action non fourni"
    ];
    } else {
        $product_id = $data['product_id'] ?? '';
        $quantity = $data['quantity'] ?? '';

        switch($action) {
            case 'add_cart' : 
                $response = add_cart($product_id, $quantity, $pdo);
                break;

            case 'remove_cart' : 
                $response = remove_cart($product_id);
                break;

            default :
                
                $response = [
                'success' => 'false',
                'message' => 'Action non reconnue'
                ];
                http_response_code(400);
                break;

        }
    }

    echo json_encode($response);

} else{
    http_response_code(405);
    $response = ['success' => false, 'message' => 'Methode non authorisée'];
    echo json_encode($response);
}