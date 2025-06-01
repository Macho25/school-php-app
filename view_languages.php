<?php

require "./utils/init.php";
require "./db/language.php";
$languages = getAllLanguages($db);

require "./layout/head.phtml";

require "./view_languages.phtml";



require "./layout/tail.phtml";