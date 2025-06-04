<?php

require "./utils/init.php";
require "./db/language.php";
$languages = getAllLanguages($db);

require "./layout/head.phtml";

require "./view_languages.phtml";
if(isset($_POST["setDeactiveLanguageSubmit"])){
    $result = deactiveLanguage($db, $_POST["language_id"]);
    if($result)
        echo "The language was successfully deactiveded";
    else
        echo "Cannot deactive the language";

}
if(isset($_POST["setActiveLanguageSubmit"])){
    $result = setActiveLanguage($db, $_POST["language_id"]);
    if($result)
        echo "The language was successfully activeded";
    else
        echo "Cannot active the language";

}


require "./layout/tail.phtml";