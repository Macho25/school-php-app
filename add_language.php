<?php



require "./utils/init.php";
require "./db/language.php";
require "./layout/head.phtml";
$language_props = getLanguageProperties($db, ["type_systems", "paradigms", "tags"]);

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



require "./layout/tail.phtml";