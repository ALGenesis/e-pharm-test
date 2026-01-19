<?php

function GET($pdo) {
    try {
        // Initialisation de la réponse par défaut
        $response = ['success' => false, 'message' => 'Aucun paramètre de recherche fourni'];
        $params = [];

        // Construction de la requête de base
        $sql = "SELECT 
                    p.*, 
                    c.name as category_name,
                    ph.name as pharmacy_name,
                    ph.address as pharmacy_address
                FROM products p
                LEFT JOIN categories c ON p.category_id = c.id
                LEFT JOIN pharmacies ph ON p.pharmacy_id = ph.id
                WHERE p.stock > 0";

        // Filtre par recherche
        if (isset($_GET['filter']) && !empty(trim($_GET['filter']))) {
            $sql .= " AND (p.name LIKE :filter OR p.about LIKE :filter)";
            $params[':filter'] = '%' . trim($_GET['filter']) . '%';
        }

        // Filtre par catégorie
        if (isset($_GET['category']) && !empty(trim($_GET['category']))) {
            $sql .= " AND c.name = :category";
            $params[':category'] = trim($_GET['category']);
        }

        // Filtre par pharmacie
        if (isset($_GET['pharmacy']) && !empty(trim($_GET['pharmacy']))) {
            $sql .= " AND ph.name = :pharmacy";
            $params[':pharmacy'] = trim($_GET['pharmacy']);
        }

        if(isset($_GET["id"]) && is_numeric($_GET["id"])) {
            $sql .= " AND p.id = :id";
            $params[':id'] = (int)$_GET['id'];
        }

        // Limite des résultats
        $sql .= " LIMIT 50";

        // Exécution de la requête
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        $products = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Vérification des résultats
        if (empty($products)) {
            $message = 'Aucun produit trouvé';
            if (isset($_GET['filter'])) {
                $message = 'Aucun produit ne correspond à votre recherche';
            } elseif (isset($_GET['category'])) {
                $message = 'Aucun produit dans cette catégorie';
            } elseif (isset($_GET['pharmacy'])) {
                $message = 'Aucun produit disponible dans cette pharmacie';
            } elseif(isset($_GET["id"])) {
                $message = 'Produit non trouvé';
            }
            
            echo json_encode(['success' => false, 'message' => $message]);
            exit;
        }

        // Réponse en cas de succès
        $response = [
            'success' => true,
            'data' => $products,
            'count' => count($products)
        ];

    } catch (PDOException $e) {
        http_response_code(500);
        $response = [
            'success' => false,
            'message' => 'Erreur Serveur',
            'error' => $e->getMessage()
        ];
    } catch (Exception $e) {
        http_response_code(500);
        $response = [
            'success' => false,
            'message' => 'Une erreur est survenue',
            'error' => $e->getMessage()
        ];
    }

    // Envoi de la réponse
    echo json_encode($response);
}