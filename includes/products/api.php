<?php
require_once dirname(__DIR__, 2) . "/config/config.php";
require ROOT_PATH . '/config/database.php';
require_once ROOT_PATH . '/includes/products/routes/GET.php';

header('Content-Type: application/json');

// Vérification de la méthode HTTP
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    GET($pdo);
} else {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Méthode non autorisée']);
    exit;
}