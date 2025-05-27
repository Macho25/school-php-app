<?php

require "./utils/init.php";

require "./db/user.php";
require "./layout/head.phtml";
require "./register.phtml";
if(isset($_POST["registerSubmit"])){

    if($_POST["password"] === $_POST["passwordAgain"]){

        createUser($db, $_POST["username"], $_POST["password"], $_POST["email"]);
        header("Location: /Webproject/login.php");
        echo "the account was created successfully";

    } else {
        
        echo "passwod are not same";

    }

}
require "./layout/tail.phtml";