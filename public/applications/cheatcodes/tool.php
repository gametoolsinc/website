<?php 
include_once($_SERVER['DOCUMENT_ROOT'] . "/library/server/template/tool/toolWebpage.php");
include_once($_SERVER['DOCUMENT_ROOT'] . "/library/server/dataManagement/games/cheatcodes.php");

$tool_webpage = new ToolWebpage($_GET["id"]);

$tool_webpage->startContent();
$data = new Cheatcodes($tool_webpage->getTool());

echo "<select name='platform' oninput='selectPlatform(this)'>";
foreach($data->getPlatforms() as $name){
    echo "<option value='{$name}'>{$name}</option>";
}
echo "</select>";
foreach($data->getCheatcodesCategories() as $categorie){
    echo "<h2>{$categorie->getName()}</h2>";
    echo "<div class='table'>";
    foreach ($categorie->getCheatcodes() as $cheatcode){
        echo "<div class='card' id='{$cheatcode->getName()}'><h3>{$cheatcode->getName()}</h3>";
        foreach ($cheatcode->getCheatcodes() as $platform=>$code){
            echo "<p class='{$platform}'><strong>{$platform}</strong>: {$code}</p>";
        }
        echo "</div>";
    }
    echo "</div>";
}


$tool_webpage->endContent();