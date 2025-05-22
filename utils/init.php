<?php
session_start();
$dotenv = parse_ini_file(__DIR__ . "/../.env");
require_once __DIR__ . "/../db/connect.php";

