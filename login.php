<?php
ob_start();
require "./utils/init.php";

require "./db/user.php";

require "./layout/head.phtml";
$username = $_POST["username"] ?? "";
$email = $_POST["password"] ?? "";

require "./login.phtml";



if(isset($_POST["loginSubmit"])){
    
    if(loginUser($db, $_POST["username"], $_POST["password"])){
        
        header("Location: /Webproject/index.php");
        echo "You are in";

    } else {
        
        echo "User does not exist";

    }
    
}

require "./layout/tail.phtml";
ob_end_flush();