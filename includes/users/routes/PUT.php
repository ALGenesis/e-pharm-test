<?php

function PUT() {
    
    try {
        $data = json_decode(file_get_contents('php://input'), true);

        if(isset($_GET['action']) && $_GET['action'] === 'password') {
            // Mettre à jour le mot de passe de l'utilisateur
            
            $userId = $_SESSION['user_id'];
            $currentPassword = $data['currentPassword'] ?? '';
            $newPassword = $data['newPassword'] ?? '';

            if($currentPassword === $newPassword) {
                sendJsonResponse(null, 400, 'Le nouveau mot de passe doit être différent de l\'actuel');
                return;
            }

            if(!is_password_solid($newPassword)) {
                sendJsonResponse(null, 400, 'Nouveau mot de passe trop faible');
                return;
            }

            // Récupérer l'utilisateur
            $user = getUserById($userId, true);
            if (!$user) {
                sendJsonResponse(null, 404, 'Utilisateur non trouvé');
                return;
            }

            // Vérifier le mot de passe actuel
            if (!password_verify($currentPassword, $user['password'])) {
                sendJsonResponse(null, 401, 'Mot de passe actuel incorrect');
                return;
            }

            // Mettre à jour le mot de passe
            $hashedPassword = password_hash($newPassword, PASSWORD_BCRYPT);
            updateUserInfo($userId, ['password' => $hashedPassword]);

            sendJsonResponse(null, 200, 'Mot de passe mis à jour avec succès');
            return;
        }

        if(isset($_GET['action']) && $_GET['action'] === 'info') {
            // Mettre à jour les informations de l'utilisateur
            $userId = $_SESSION['user_id'];

            $updateData = [];
            if(isset($data['email'])) {
                $updateData['email'] = $data['email'];

                $existingUser = getUserByEmail($data['email']);
                if(($existingUser && $existingUser['id'] != $userId )|| !is_email_valid($data['email'])) {
                    sendJsonResponse(null, 400, 'Email invalide ou déjà utilisé');
                    return;
                }
            }
            
            if(isset($data['username'])) {
                $updateData['username'] = $data['username'];
            }

            if(empty($updateData)) {
                sendJsonResponse(null, 400, 'Aucune donnée à mettre à jour');
                return;
            }

            updateUserInfo($userId, $updateData);
            sendJsonResponse(null, 200, 'Informations mises à jour avec succès');
            return;
        }

        if((isset($_GET['action']) && $_GET['action'] === 'role') && is_admin()) {
            $userId = $data['userId'] ?? null;

            if(!$userId) {
                sendJsonResponse(null, 400, 'ID utilisateur requis');
                return;
            }

            // Mettre à jour le rôle de l'utilisateur
            $newRole = $data['role'] ?? null;
            if(!$newRole || !in_array($newRole, ['user', 'admin'])) {
                sendJsonResponse(null, 400, 'Rôle invalide');
                return;
            }

            updateUserInfo($userId, ['role' => $newRole]);
            sendJsonResponse(null, 200, 'Rôle mis à jour avec succès');
            return;
            
        }

        if(empty($_GET['action'])) {
            sendJsonResponse(null, 400, 'Action non spécifiée');
            return;
        }

    } catch (Exception $e) {
        error_log('Erreur serveur: ' . $e->getMessage());
        sendJsonResponse(null, 500, 'Erreur serveur');
        exit;
    }
}