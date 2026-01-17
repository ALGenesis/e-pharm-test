<?php
require_once   dirname(__DIR__, 2) . "/config/config.php";
require ROOT_PATH . '/config/database.php';
// require ROOT_PATH . '/config/functions/is_session_active.php';

// Configuration des en-têtes HTTP
header('Content-Type: application/json');

// Fonction pour envoyer une réponse JSON standardisée
function sendJsonResponse($data = null, $statusCode = 200, $message = '') {
    
    echo json_encode([
        'success' => $statusCode >= 200 && $statusCode < 300,
        'message' => $message,
        'data' => $data,
        'status' => $statusCode
    ]);
    exit;
}

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
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    sendJsonResponse(null, 405, 'Méthode non autorisée');
    http_response_code(405);
}

// Récupération des données de la requête
$data = json_decode(file_get_contents('php://input'), true);
$action = $_GET['action'] ?? '';

// Vérification des données
if (empty($data) && $action !== 'get') {
    sendJsonResponse(null, 400, 'Aucune donnée fournie');
}

if (empty($action)) {
    sendJsonResponse(null, 400, 'Paramètre "action" manquant');
}

// Démarrer la session si pas déjà démarrée
// is_session_active();
session_start();

// Initialisation du panier s'il n'existe pas
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

try {
    // Traitement des différentes actions
    switch ($action) {
        case 'add':
            // Validation des données
            $productId = filter_var($data['product_id'] ?? null, FILTER_VALIDATE_INT);
            $quantity = filter_var($data['quantity'] ?? 1, FILTER_VALIDATE_INT, [
                'options' => ['min_range' => 1]
            ]);

            if (!$productId) {
                sendJsonResponse(null, 400, 'ID de produit invalide');
            }

            // Vérifier si le produit existe
            $stmt = $pdo->prepare("SELECT id, name, price, stock FROM products WHERE id = :id");
            $stmt->execute(['id'=>$productId]);
            $product = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$product) {
                sendJsonResponse(null, 404, 'Produit non trouvé');
            }

            // Vérifier le stock
            if ($product['stock'] < $quantity) {
                sendJsonResponse([
                    'available_stock' => $product['stock']
                ], 400, 'Stock insuffisant');
            }

            // Ajouter au panier
            if (isset($_SESSION['cart'][$productId])) {
                $_SESSION['cart'][$productId] += $quantity;
            } else {
                $_SESSION['cart'][$productId] = $quantity;
            }

            sendJsonResponse([
                'count' => array_sum($_SESSION['cart']),
                'totalPrice' => calculateCartTotal($pdo, $_SESSION['cart'])
            ], 201, 'Produit ajouté au panier');
            break;

        case 'remove':
            $productId = filter_var($data['product_id'] ?? null, FILTER_VALIDATE_INT);
            
            if (!$productId) {
                sendJsonResponse(null, 400, 'ID de produit invalide');
            }

            if (isset($_SESSION['cart'][$productId])) {
                unset($_SESSION['cart'][$productId]);
                sendJsonResponse([
                    'count' => array_sum($_SESSION['cart']),
                    'totalPrice' => calculateCartTotal($pdo, $_SESSION['cart'])
                ], 200, 'Produit retiré du panier');
            } else {
                sendJsonResponse(null, 404, 'Produit non trouvé dans le panier');
            }
            break;

        case 'get':
            $cartDetails = [];
            $total = 0;

            if (!empty($_SESSION['cart'])) {
                $productIds = array_keys($_SESSION['cart']);
                $placeholders = str_repeat('?,', count($productIds) - 1) . '?';
                
                $stmt = $pdo->prepare("
                    SELECT id, name, price, stock 
                    FROM products 
                    WHERE id IN ($placeholders)
                ");
                $stmt->execute($productIds);
                
                while ($product = $stmt->fetch(PDO::FETCH_ASSOC)) {
                    $quantity = $_SESSION['cart'][$product['id']];
                    $subtotal = $product['price'] * $quantity;
                    $total += $subtotal;
                    
                    $cartDetails[] = [
                        'id' => $product['id'],
                        'name' => $product['name'],
                        'price' => (float)$product['price'],
                        'quantity' => $quantity,
                        'subtotal' => $subtotal,
                        'available_stock' => $product['stock']
                    ];
                }
            }

            sendJsonResponse([
                'items' => $cartDetails,
                'count' => array_sum($_SESSION['cart']),
                'totalPrice' => $total
            ]);
            break;

        default:
            sendJsonResponse(null, 400, 'Action non reconnue');
    }
} catch (PDOException $e) {
    error_log('Erreur de base de données: ' . $e->getMessage());
    sendJsonResponse(null, 500, 'Erreur lors du traitement de la requête');
} catch (Exception $e) {
    error_log('Erreur: ' . $e->getMessage());
    sendJsonResponse(null, 500, 'Une erreur est survenue');
}

