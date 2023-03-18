<?php
include_once($_SERVER['DOCUMENT_ROOT'] . "/library/server/dataManagement/application.php");

$ids = Application::getAllApplicationIds();

foreach ($ids as $id){
    $file = $_SERVER['DOCUMENT_ROOT'] . "/public/applications/".$id;
    $string = file_get_contents($file . "/toolInformation.json");
    $data = json_decode($string, true);
    showData($data, 1);
    echo '<hr class="pb" style="page-break-before: always">';
}

function showData($data, int $lvl)
{
    if (is_array($data)) {
        foreach ($data as $key => $value){
            echo "<h".$lvl.">".$key."</h".$lvl.">";
            showData($data[$key], $lvl+1);
        }
    } else {
        $data = str_replace("&", "&amp", $data);
        $data = str_replace("<", "&lt", $data);
        $data = str_replace(">", "&gt", $data);
        echo "<p>".$data."</p>";
    }
}   