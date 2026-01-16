<?php

function remove_cart ($product_id) {
    session_start();
    unset($_SESSION['cart'][$product_id]);
     $response = [
        'success' => 'true',
        'data' => $_SESSION['cart']
     ];

     return $response;
}