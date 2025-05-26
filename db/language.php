<?php 





function addLanguageLinks($db, $sql, $language_id, $links){
    // "INSERT INTO language_paradigms (language_id, paradigm_id) VALUES (?, ?)"
    $stmt = mysqli_prepare($db, $sql);
    if(!$stmt){
        die("1Error in query preparation: " . mysqli_error($db));
    }

    foreach($links as $link){
        mysqli_stmt_bind_param($stmt, "ii", $language_id, $link);
        if (!mysqli_stmt_execute($stmt)) {
            die("2Error in query preparation: " . mysqli_stmt_error($stmt));
        }
    }
}


function addLanguage($db, $name, $purpose,
                    $description, $type_systems,
                    $paradigms, $tags, $version, 
                    $release_year, $note
                    ){
    $stmt = mysqli_prepare($db, "
        INSERT INTO languages (user_id, name, purpose, description)
        VALUES (?, ?, ?, ?)
    ");
    if(!$stmt){
        die("3Error in query preparation: " . mysqli_error($db));
    }
    mysqli_stmt_bind_param($stmt, "isss", $_SESSION["user_id"],$name, $purpose,
                            $description);
    mysqli_stmt_execute($stmt);

    $language_id = mysqli_insert_id($db);

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

    foreach ($tables as $table) {
        $sql = "SELECT id, name FROM {$table} ORDER BY name;";
        $result = mysqli_query($db, $sql);

        while ($row = mysqli_fetch_assoc($result)) {
            $language_props[$table][] = $row;
        }
    }

    return $language_props;
}

function printOptions($options){    
    foreach ($options as $option) {
    echo "<option value=\"{$option["id"]}\">{$option["name"]}</option>";
    }
}