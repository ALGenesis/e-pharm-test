<?php
require_once   dirname(__DIR__, 2) . "/config/config.php";
require_once ROOT_PATH . '/config/database.php';
require_once ROOT_PATH . '/utils/is_session_active.php';
require_once ROOT_PATH . '/utils/api.utils.php';
require_once ROOT_PATH . '/includes/cart/routes/POST.php';

// Configuration des en-têtes HTTP
header('Content-Type: application/json');

// Fonction pour calculer le total du panier
function calculateCartTotal($pdo, $cart) {
    if (empty($cart)) {
        return 0;
    }

    $productIds = array_keys($cart);
    $placeholders = str_repeat('?,', count($productIds) - 1) . '?';
    
    $stmt = $pdo->prepare("
        SELECT id, price 
        FROM products 
        WHERE id IN ($placeholders)
    ");
    $stmt->execute($productIds);
    
    $total = 0;
    while ($product = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $total += $product['price'] * $cart[$product['id']];
    }
    
    return $total;
}

// Vérification de la méthode HTTP
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    set_session();
    POST($pdo);
} else {
    sendJsonResponse(null, 405, 'Méthode non autorisée');
    http_response_code(405);
}