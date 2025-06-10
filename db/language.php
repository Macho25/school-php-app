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

function actionToTable($db, $sql, $format, ...$values) {
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

    actionToTable(
        $db,
        "INSERT INTO languages (user_id, name, purpose, description) VALUES (?, ?, ?, ?)",
        "isss",
        $user_id, $name, $purpose, $description
    );

    $language_id = mysqli_insert_id($db);

    actionToTable(
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
function updateLanguage($db, $name, $purpose,
                    $description, $type_systems,
                    $paradigms, $tags, $version, 
                    $release_year, $note, $language_id
                    ){
    

                        
// TODO improve this function to make more readable 
                        
    actionToTable(
        $db,
        "UPDATE languages SET name = ?, purpose = ?, description = ? WHERE id = ?",
        "sssi",
        $name, $purpose, $description, $language_id
    );


    actionToTable(
        $db,
        "UPDATE language_versions SET version = ?, release_year = ?, note = ? WHERE language_id = ?",
        "sisi",
        $version, $release_year, $note, $language_id
    );


    actionToTable($db, "DELETE FROM language_paradigms WHERE language_id = ?", "i", $language_id);
    addLanguageLinks($db, 
        "INSERT INTO language_paradigms (language_id, paradigm_id) VALUES (?, ?)",
        $language_id, 
        $paradigms
    );


    actionToTable($db, "DELETE FROM language_tags WHERE language_id = ?", "i", $language_id);
    addLanguageLinks($db, 
        "INSERT INTO language_tags (language_id, tag_id) VALUES (?, ?)",
        $language_id, 
        $tags
    );


    actionToTable($db, "DELETE FROM language_type_systems WHERE language_id = ?", "i", $language_id);
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

function printOptions($options, $selected = []){  
    foreach ($options as $option){
        $is_selected = in_array((string)$option["id"], $selected) ? "selected" : "";
        echo "<option value=\"{$option["id"]}\" $is_selected>{$option["name"]}</option>";
    }
}


function printOptionsFilters($options, $filters = []){  
    foreach ($options as $option){
        $is_selected = in_array((string)$option["id"], $filters) ? "selected" : "";
        echo "<option value=\"{$option["id"]}\"  $is_selected>{$option["name"]} </option>";
    }
}



function getLanguageById($db, $id){
    $stmt = mysqli_prepare($db,  "SELECT * FROM languages WHERE id = ?");
    mysqli_stmt_bind_param($stmt, "i", $id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $language = mysqli_fetch_assoc($result);

    if(!$language) return [];

    $typeRes = mysqli_query($db, "SELECT type_system_id FROM language_type_systems WHERE language_id = $id");
    $language["type_systems"] = [];
    while($row = mysqli_fetch_assoc($typeRes)){
        $language["type_systems"][] = $row["type_system_id"];
    }

    $paradigmRes = mysqli_query($db, "SELECT paradigm_id FROM language_paradigms WHERE language_id = $id");
    $language["paradigms"] = [];
    while($row = mysqli_fetch_assoc($paradigmRes)){
        $language["paradigms"][] = $row["paradigm_id"];
    }

    $tagRes = mysqli_query($db, "SELECT tag_id FROM language_tags WHERE language_id = $id");
    $language["tags"] = [];
    while($row = mysqli_fetch_assoc($tagRes)){
        $language["tags"][] = $row["tag_id"];
    }

    $versionRes = mysqli_query($db, "SELECT * FROM language_versions WHERE language_id = $id ORDER BY id ASC LIMIT 1");
    $versionRow = mysqli_fetch_assoc($versionRes);
    if($versionRow){
        $language["version"] = $versionRow["version"];
        $language["release_year"] = $versionRow["release_year"];
        $language["note"] = $versionRow["note"];
    }

    return $language;
}




function deactiveLanguage($db, $language_id){
    $result = mysqli_query($db, 
    "UPDATE languages SET is_active = 0 WHERE id = $language_id"
    );
    if(!$result)
        return false;
    else
        return true;
}

function setActiveLanguage($db, $language_id){
    $result = mysqli_query($db, 
    "UPDATE languages SET is_active = 1 WHERE id = $language_id"
    );
    if(!$result)
        return false;
    else
        return true;
}



function filterLanguage($selectedFilterIds,$languageProperties){
     if(empty($selectedFilterIds)){
        return false;
    }
    foreach($selectedFilterIds as $filter){
        if(in_array($filter, $languageProperties)){
            return false;
        }
    }
    return true;
}



function shouldShowLanguage($language,$selectedFilters,$idToNameMap){
    
    foreach($selectedFilters as $category => $selectedIds){
        if(empty($selectedIds)) continue;

        $selectedNames = [];
        foreach($selectedIds as $id){
            $uniqueId = $category . '_' . $id;
            if (isset($idToNameMap[$uniqueId])) {
                $selectedNames[] = $idToNameMap[$uniqueId];
            }
        }
        
        $langProps = $language[$category] ?? [];
        
        $hasMatch = false;
        foreach($selectedNames as $name){
            if(in_array($name, $langProps)){
                $hasMatch = true;
                break;
            }
        }
        
        if(!$hasMatch){
            return false;
        }
    }
    
    return true;
}