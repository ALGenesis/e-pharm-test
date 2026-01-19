<?php

function DELETE(){
    try {
        $data = json_decode(file_get_contents('php://input'), true);

        if(isset($data['userId']) && is_admin()) {
            $userId = $data['userId'];
        } else {
            $userId = $_SESSION['user_id'];
        }

        $user = getUserById($userId);
        if (!$user) {
            sendJsonResponse(null, 404, 'Utilisateur non trouvé');
            return;
        }

        if(is_admin($user)) {
            sendJsonResponse(null, 403, 'Impossible de supprimer un administrateur');
            return;
        }

        deleteUserById($userId);

        sendJsonResponse(null, 200, 'Utilisateur supprimé avec succès');
    } catch (Exception $e) {
        sendJsonResponse(null, 500, 'Erreur serveur: ' . $e->getMessage());
    }
}