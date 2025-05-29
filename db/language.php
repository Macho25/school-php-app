<?php 
function getLanguageLinks($db, $sql, &$languages, $link_key){
    $result = mysqli_query($db, $sql);
    while($row = mysqli_fetch_assoc($result))
        $languages[$row['language_id']][$link_key][] = $row['name'];
}


function getAllLanguages($db){
    $languages = [];
    
    if($_SESSION["role"] === "admin")
        $sql = "SELECT * FROM languages";
    else
        $sql = "SELECT * FROM languages WHERE is_active = 1";

    $result = mysqli_query($db, $sql);
    
    while($row = mysqli_fetch_assoc($result)){
        $languages[$row['id']] = $row;
        $languages[$row['id']]['tags'] = [];
        $languages[$row['id']]['paradigms'] = [];
        $languages[$row['id']]['type_systems'] = [];
    }

     getLanguageLinks(
        $db,
        "SELECT language_tags.language_id, tags.name
         FROM language_tags
         JOIN tags ON language_tags.tag_id = tags.id",
        $languages,
        "tags"
    );

    getLanguageLinks(
        $db,
        "SELECT language_paradigms.language_id, paradigms.name
         FROM language_paradigms
         JOIN paradigms ON language_paradigms.paradigm_id = paradigms.id",
        $languages,
        "paradigms"
    );

    getLanguageLinks(
        $db,
        "SELECT language_type_systems.language_id, type_systems.name
         FROM language_type_systems
         JOIN type_systems ON language_type_systems.type_system_id = type_systems.id",
        $languages,
        "type_systems"
    );
    

    return array_values($languages); 
}





function addLanguageLinks($db, $sql, $language_id, $links){
    $stmt = mysqli_prepare($db, $sql);
    if(!$stmt)
        die("1Error in query preparation: " . mysqli_error($db));
    

    foreach($links as $link){
        mysqli_stmt_bind_param($stmt, "ii", $language_id, $link);
        if (!mysqli_stmt_execute($stmt)) 
            die("2Error in query preparation: " . mysqli_stmt_error($stmt));
    }
}

function insertToTable($db, $sql, $format, ...$values) {
    $stmt = mysqli_prepare($db, $sql);
    if (!$stmt) {
        die("Chyba při přípravě dotazu: " . mysqli_error($db));
    }

    mysqli_stmt_bind_param($stmt, $format, ...$values);
    mysqli_stmt_execute($stmt);
}


function addLanguage($db, $name, $purpose,
                    $description, $type_systems,
                    $paradigms, $tags, $version, 
                    $release_year, $note
                    ){
    $user_id = $_SESSION["user_id"];

    insertToTable(
        $db,
        "INSERT INTO languages (user_id, name, purpose, description) VALUES (?, ?, ?, ?)",
        "isss",
        $user_id, $name, $purpose, $description
    );

    $language_id = mysqli_insert_id($db);

    insertToTable(
        $db,
        "INSERT INTO language_versions (language_id, version, release_year, note) VALUES (?, ?, ?, ?)",
        "isis",
        $language_id, $version, $release_year, $note
    );

    addLanguageLinks($db, 
        "INSERT INTO language_paradigms (language_id, paradigm_id) VALUES (?, ?)",
        $language_id, 
        $paradigms
    );

    addLanguageLinks($db, 
        "INSERT INTO language_tags (language_id, tag_id) VALUES (?, ?)",
        $language_id, 
        $tags
    );

    addLanguageLinks($db, 
        "INSERT INTO language_type_systems (language_id, type_system_id) VALUES (?, ?)",
        $language_id, 
        $type_systems
    );

    

}
function getLanguageProperties($db, $tables){
    $language_props = [];

    foreach ($tables as $table){
        $sql = "SELECT id, name FROM {$table} ORDER BY name;";
        $result = mysqli_query($db, $sql);

        while ($row = mysqli_fetch_assoc($result))
            $language_props[$table][] = $row;
    }
    return $language_props;
}

function printOptions($options){    
    foreach ($options as $option){
    echo "<option value=\"{$option["id"]}\">{$option["name"]}</option>";
    }
}