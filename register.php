<?php

require "./utils/init.php";

require "./db/user.php";
require "./layout/head.phtml";

$username = $_POST["username"] ?? "";
$email = $_POST["email"] ?? "";

require "./register.phtml";
if(isset($_POST["registerSubmit"])){

    if(isEmailAlreadyExists($db, $_POST["email"])){
        echo "Email already exits";
        exit;
    }
        

    if($_POST["password"] === $_POST["passwordAgain"]){
        $username = strip_tags(trim($_POST["username"]));
        $email = filter_var(trim($_POST["email"]), FILTER_SANITIZE_EMAIL);
        $password = strip_tags(trim($_POST["password"]));
        createUser($db, $username, $password, $email);
        header("Location: /Webproject/login.php");
        echo "Registration successful! Welcome, ".  htmlspecialchars($username);

    } else {
        
        echo "passwods are not same";

    }



}
require "./layout/tail.phtml";