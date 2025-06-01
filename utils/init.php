<?php
session_start();
if (!isset($_SESSION["role"])) $_SESSION["role"] = "";
if (!isset($_SESSION["username"])) $_SESSION["username"] = "";
if (!isset($_SESSION["user_id"])) $_SESSION["user_id"] = 0;

$dotenv = parse_ini_file(__DIR__ . "/../.env");
require_once __DIR__ . "/../db/connect.php";

