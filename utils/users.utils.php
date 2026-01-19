<?php


function getUserByEmail($email, $password = false) {
    require_once dirname(__DIR__) . '/config/database.php';
    global $pdo;
    
    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = :email LIMIT 1");
    $stmt->execute([
        'email' => $email,
    ]);

    if($password) {
        return $stmt->fetch(PDO::FETCH_ASSOC);
    } else {
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        if($user) {
            unset($user['password']);
        }
        return $user;
    }
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

function getUserById($id, $password = false) {
    require_once dirname(__DIR__) . '/config/database.php';
    global $pdo;
    
    $stmt = $pdo->prepare("SELECT * FROM users WHERE id = :id LIMIT 1");
    $stmt->execute([
        'id' => $id,
    ]);

    if($password) {
        return $stmt->fetch(PDO::FETCH_ASSOC);
    } else {
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        if($user) {
            unset($user['password']);
        }
        return $user;
    }

}

function findManyUsers($limit = 10, $offset = 0) {
    require_once dirname(__DIR__) . '/config/database.php';
    global $pdo;

    $stmt = $pdo->prepare("SELECT * FROM users LIMIT :limit OFFSET :offset");
    $stmt->bindValue(':limit', (int)$limit, PDO::PARAM_INT);
    $stmt->bindValue(':offset', (int)$offset, PDO::PARAM_INT);
    $stmt->execute();

    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
    foreach ($users as &$user) {
        unset($user['password']);
    }
    return $users;
}


function updateUserInfo($id, $fields) {
    require_once dirname(__DIR__) . '/config/database.php';
    global $pdo;

    if(!is_array($fields) || empty($fields) || !isset($id)) {
        return false;
    }

    $setClause = [];
    $params = [':id' => $id];

    foreach ($fields as $key => $value) {
        $setClause[] = "$key = :$key";
        $params[":$key"] = $value;
    }

    $setClauseStr = implode(', ', $setClause);
    $stmt = $pdo->prepare("UPDATE users SET $setClauseStr WHERE id = :id");
    return $stmt->execute($params);
}

function deleteUserById($id) {
    require_once dirname(__DIR__) . '/config/database.php';
    global $pdo;

    $stmt = $pdo->prepare("DELETE FROM users WHERE id = :id");
    return $stmt->execute([':id' => $id]);
}