<?php
include_once($_SERVER['DOCUMENT_ROOT'] . "/library/server/template/tool/toolWebpage.php");
include_once($_SERVER['DOCUMENT_ROOT'] . "/library/server/dataManagement/games/cheatcodes.php");

$tool_webpage = new ToolWebpage($_GET["id"]);

$tool_webpage->startContent();
?>

<div id="screen">
<div class="top-part">
    <h1>Select the right color</h1>
    <p><span id="correct">0</span> correct</p>
    <p><span id="wrong">0</span> wrong</p>
    <div id="name"></div>
</div>
<div id="random-values">
    <div onclick="check(this)">1</div>
    <div onclick="check(this)">2</div>
    <div onclick="check(this)">3</div>
    <div onclick="check(this)">4</div>
</div>
</div>

<?php
$tool_webpage->endContent();
