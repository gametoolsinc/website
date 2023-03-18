<?php
include_once($_SERVER['DOCUMENT_ROOT'] . "/library/template/tool/toolWebpage.php");
$tool_webpage = new ToolWebpage($_GET["id"]);
$tool_webpage->startContent();

$file = file_get_contents($_SERVER['DOCUMENT_ROOT'] . "/resources/games/codcoldwar/classCounter.json");
$object = json_decode($file, true);
echo '<p class="counter">' . $object["counter"] . ' classes rolled </p>';

?>

<div class="button" onclick="randomClass()">
    <h3>Generate Class</h3>
</div>

<div class="generated_class"></div>

<?php
$tool_webpage->endContent();