<?php
require_once ROOT_PATH . '/utils/users.utils.php';

function GET() {
    $userId = $_SESSION['user_id'];

    try {
        // Lister tous les utilisateurs (admin uniquement)
        if(isset($_GET['action']) && $_GET['action'] === 'list' && is_admin()) {
           $users = findManyUsers(); 

           if(!$users) {
               sendJsonResponse([], 404, 'Aucun utilisateur trouvé');
               return;
           }
           sendJsonResponse($users, 200, 'Utilisateurs récupérés avec succès');
        }
        
        // Récupérer les informations de l'utilisateur connecté
        $user = getUserById($userId);

        if ($user) {
            sendJsonResponse($user, 200, 'Utilisateur récupéré avec succès');
        } else {
            sendJsonResponse(null, 404, 'Utilisateur non trouvé');
        }
    } catch (PDOException $e) {
        error_log('Erreur de base de données: ' . $e->getMessage());
        sendJsonResponse(null, 500, 'Erreur serveur: ');
    } catch (Exception $e) {
        error_log('Erreur serveur: ' . $e->getMessage());
        sendJsonResponse(null, 500, 'Erreur lors du traitement de la requête: ' . $e->getMessage());
    }
}