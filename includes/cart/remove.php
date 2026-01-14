<?php

$product_id = (int)($data['product_id'] ?? 0);
unset($_SESSION['cart'][$product_id]);

echo json_encode(["success" => true, "cart" => $_SESSION['cart']]);