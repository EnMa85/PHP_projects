<?php
    include_once "_connection.php";
    include_once "_functions.php";
    require 'vendor/autoload.php';
    
    session_start();
    
    // if all post data are sets, uses functions to creates pdf or xlsx file with received array and title
    if (isset($_POST["pdf"]) && isset($_POST["title"]) && isset($_POST["array"])) {
        $title = $_POST["title"];
        $array = json_decode($_POST["array"], true);
        $table = array_to_table($array);
        save_pdf($table, $title);
    }   
    if (isset($_POST["xlsx"]) && isset($_POST["title"]) && isset($_POST["array"])) {
        $title = $_POST["title"];
        $array = json_decode($_POST["array"], true);
        export_xlsx($array, $title);
    }
?>
