<?php
session_start();
if(!isset($_SESSION["role"]) and !isset($_SESSION["username"])){
    $_SESSION["role"] = "";
    $_SESSION["username"] = "";
}
$dotenv = parse_ini_file(__DIR__ . "/../.env");
require_once __DIR__ . "/../db/connect.php";

