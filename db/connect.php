<?php

mysqli_report(MYSQLI_REPORT_OFF);
$db = mysqli_connect( 
        $dotenv['DB_HOST'], 
        $dotenv['DB_USER'], 
        $dotenv['DB_PASS'],  
        $dotenv['DB_NAME']
    );
if ($db === false) { 
    echo "<p>Failed to connect to databse.</p>";
    exit;
}
mysqli_set_charset($db, "utf8mb4");
