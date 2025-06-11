<?php

require "./utils/init.php";
require "./db/user.php";


require "./layout/head.phtml";
if ($_SESSION["user_id"] === 0) {
    header("Location: ./login.php");
    exit();
}
$users = getUsers($db);

require "./view_users.phtml";




require "./layout/tail.phtml";