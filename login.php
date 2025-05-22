<?php
require "./utils/init.php";

require "./db/user.php";

require "./layout/head.phtml";
require "./login.phtml";

if(isset($_POST["loginForm"])){
    
    if(loginUser($db, $_POST["username"], $_POST["password"])){
        echo "You are in";
    } else {
        echo "User does not exist";

    }
    
}

require "./layout/tail.phtml";