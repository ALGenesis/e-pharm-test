<?php

$product_id = $data['product_id'];
$quantity = $data['quantity'] ?? 1;

$_SESSION['cart'][$product_id] = ($_SESSION['cart'][$product_id] ?? 0) + $quantity;
echo json_encode(["success" => true, "cart" => $_SESSION['cart']]);