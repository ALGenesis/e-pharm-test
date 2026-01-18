<?php


function getUserByEmail($email) {
    require_once dirname(__DIR__) . '/config/database.php';
    global $pdo;
    
    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = :email LIMIT 1");
    $stmt->execute([
        'email' => $email,
    ]);

    return $stmt->fetch(PDO::FETCH_ASSOC);
}

function createUser($email, $username, $hashedPassword) {
    require_once dirname(__DIR__) . '/config/database.php';
    global $pdo;

    $stmt = $pdo->prepare("INSERT INTO users (email, username, password) VALUES (:email, :username, :password)");
    $stmt->execute([
        'email' => $email,
        'username' => $username,
        'password' => $hashedPassword,
    ]);

    return $pdo->lastInsertId();
} 

function getUserById($id) {
    require_once dirname(__DIR__) . '/config/database.php';
    global $pdo;
    
    $stmt = $pdo->prepare("SELECT * FROM users WHERE id = :id LIMIT 1");
    $stmt->execute([
        'id' => $id,
    ]);

    return $stmt->fetch(PDO::FETCH_ASSOC);
}