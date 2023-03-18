<?php
include_once($_SERVER['DOCUMENT_ROOT'] . "/library/server/template/tool/toolWebpage.php");
include_once $_SERVER['DOCUMENT_ROOT'] . "/library/server/dataManagement/games/units.php";

$tool_webpage = new ToolWebpage($_GET["id"]);
$tool_webpage->startContent();

$tool = new Tool($_GET['id']);

?>

<div id="options">
    <div class="standard">
        <button class="sorting-type" onclick="sound_array.shuffle()">Shuffle</button>
        <div><p>Speed</p><input type="range" id="speed" value="90" min="1" max="1000"></div>
        <div><p>Size</p><input type="range" id="size" value="90" min="2" max="1000" onInput="sound_array.setSize(this.value)"></div>
    </div>
    <div id="sorting-types">
        <button class="sorting-type" onclick="bubbleSort(sound_array)">Bubble Sort</button>
        <button class="sorting-type" onclick="selectionSort(sound_array)">Selection Sort</button>
        <button class="sorting-type" onclick="insertionSort(sound_array)">Insertion Sort</button>
    </div>
</div>

<div id="sorting-visual">

</div>



<?php

$tool_webpage->endContent();
