<?php

function POST() {
    $data = json_decode(file_get_contents('php://input'), true);
    try {
       if(isset($_GET['action']) && $_GET['action'] === 'create' && is_admin()) {
            // Créer un nouvel utilisateur (admin uniquement)
            $email = $data['email'] ?? '';
            $username = $data['username'] ?? '';
            $password = $data['password'] ?? '';

            if(empty($email) || empty($username) || empty($password) || !is_email_valid($email)) {
                sendJsonResponse(null, 400, 'Données invalides pour la création de l\'utilisateur');
                return;
            }

            if(!is_password_solid($password)) {
                sendJsonResponse(null, 400, 'Mot de passe trop faible');
                return;
            }

            // Vérifier si l'email est déjà utilisé
            $existingUser = getUserByEmail($email);
            if($existingUser) {
                sendJsonResponse(null, 409, 'Email déjà utilisé');
                return;
            }

            $hashedPassword = password_hash($password, PASSWORD_BCRYPT);
            $newUserId = createUser($email, $username, $hashedPassword);

            sendJsonResponse(['user_id' => $newUserId], 201, 'Utilisateur créé avec succès');
            return;
        }

        if(empty($_GET['action'])) {
            sendJsonResponse(null, 400, 'Action non spécifiée');
            return;
        }

        sendJsonResponse(null, 400, 'Action non reconnue ou permissions insuffisantes');
    } catch (Exception $e) {
        error_log('Erreur serveur: ' . $e->getMessage());
        sendJsonResponse(null, 500, 'Erreur serveur');
    }
}