<?php



require "./utils/init.php";
require "./db/language.php";
require "./layout/head.phtml";

if ($_SESSION["user_id"] === 0) {
    header("Location: /WebProject/login.php");
    exit();
}

$language_props = getLanguageProperties($db, ["type_systems", "paradigms", "tags"]);
// $edit = false;

if(isset($_GET["id"])){
    $language = getLanguageById($db, (int)$_GET["id"]);
    if($language){
        echo "Jazyk: " . $language["name"];
    } else {
        echo "Záznam nenalezen.";
    }   
    $edit = true;
}
require "./add_language.phtml";
if(isset($_POST["addLanguageSubmit"])){
    addLanguage($db, $_POST["name"], $_POST["purpose"], 
                    $_POST["description"], $_POST["type_systems"], 
                    $_POST["paradigms"], $_POST["tags"], 
                    $_POST["version"], $_POST["release_year"], 
                    $_POST["note"] 
                );
    echo "Language successfully added.";
}
if(isset($_POST["updateLanguageSubmit"])){
    updateLanguage($db, $_POST["name"], $_POST["purpose"], 
                    $_POST["description"], $_POST["type_systems"], 
                    $_POST["paradigms"], $_POST["tags"], 
                    $_POST["version"], $_POST["release_year"], 
                    $_POST["note"], $language["id"]
                );
}


require "./layout/tail.phtml";