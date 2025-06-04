<?php

function createUser($db, $username, $password, $email) {
    $stmt = mysqli_prepare($db, "
        INSERT INTO users
        (username, email, password_hash)
        VALUES
        (?, ?, ?)
    ");
    if ($stmt === false) {
        echo "<p>Cannot create user</p>";
        echo "<p>" . mysqli_error($db) . "</p>";
        exit;
    }

    $hashedPassword = password_hash($password, PASSWORD_BCRYPT);
    mysqli_stmt_bind_param($stmt, "sss", $username, $email, $hashedPassword);
    $result = mysqli_execute($stmt);

    if ($result === false) {
        echo "<p>Cannot create user</p>";
        echo "<p>" . mysqli_error($db) . "</p>";
        exit;
    }
}


function loginUser($db, $username, $password) {
    $stmt = mysqli_prepare($db, "
        SELECT id, username, password_hash, role
        FROM users
        WHERE username = ? AND is_active = 1
    ");
    mysqli_stmt_bind_param($stmt, "s", $username);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    
    if ($row = mysqli_fetch_assoc($result)) {
        if (password_verify($password, $row["password_hash"])) {
            $_SESSION["user_id"] = $row["id"];
            $_SESSION["username"] = $row["username"];
            $_SESSION["role"] = $row["role"];
            return true;
        }
    }
    return false; 
}


function getUsers($db){
    $stmt = mysqli_prepare($db,
    "SELECT * FROM users"
    );
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    return mysqli_fetch_all($result, MYSQLI_ASSOC);
}


function deactiveUser($db, $user_id){
    $result = mysqli_query($db, 
    "UPDATE users SET is_active = 0 WHERE id = $user_id"
    );
    if(!$result)
        return false;
    else
        return true;

}

function setActiveUser($db, $user_id){
    $result = mysqli_query($db, 
    "UPDATE users SET is_active = 1 WHERE id = $user_id"
    );
    if(!$result)
        return false;
    else
        return true;
}



