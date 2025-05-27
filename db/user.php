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
