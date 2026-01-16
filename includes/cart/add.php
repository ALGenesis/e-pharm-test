<?php

function add_cart($product_id, $quantity, $pdo) {

    $stmt = $pdo->prepare('SELECT * FROM products WHERE id = :id');
    $stmt->execute([
        'id' => $product_id
    ]);
    $product = $stmt->fetch(PDO::FETCH_ASSOC);

    if(!empty($product)) {
        session_start();
        $_SESSION['cart'][$product_id] = ($_SESSION['cart'][$product_id] ?? 0) + $quantity;

        $response =  [
            'success' => true,
            'data' => $_SESSION['cart']
            ];

    } else {
        $response = [
            'success' => false,
            'message' => 'Produit non trouvé'
            ];
    }

    return $response;
    
}