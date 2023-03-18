<?php
include_once($_SERVER['DOCUMENT_ROOT'] . "/library/server/template/tool/toolWebpage.php");
include_once $_SERVER['DOCUMENT_ROOT'] . "/library/server/dataManagement/games/units.php";

$tool_webpage = new ToolWebpage($_GET["id"]);
$transportation = new Units($tool_webpage->getTool(),  $unitType = "transportation");
$tool_webpage->startContent();

?>

<div class="input">
    <div id="start" class="location">
        <div class="inputs">
            <h3>Start location</h3>
            <label class="x">
                <div class="number">
                    <p>X</p>
                </div>
                <input type="number" id="startX" oninput="updatePosition()" value=0>
            </label>
            <label class="y">
                <div class="number">
                    <p>Z</p>
                </div>
                <input type="number" id="startZ" oninput="updatePosition()" value=0>
            </label>
        </div>
    </div>
    <div id="end" class="location">
        <div class="inputs">
            <h3>End location</h3>
            <label class="x">
                <div class="number">
                    <p>X</p>
                </div><input type="number" id="endX" oninput="updatePosition()" value=0>
            </label>
            <label class="y">
                <div class="number">
                    <p>Z</p>
                </div>
                <input type="number" id="endZ" oninput="updatePosition()" value=0>
            </label>
        </div>
    </div>
</div>
<div class="output">
    <div class="direction-output">
        <h3>Travel direction in <?=$tool->getGame()->getName();?></h3>
        <p>Travel at <br><span id="direction-number"></span><canvas id="direction" width="500" height="500"></canvas><br> for <span id="length-number"></span> blocks</p>
    </div>
    <div class="movement-output">
        <h3>Approximate travel time</h3>
        <div class="movement-types">
        <?php
        foreach ($transportation->getUnitCategories() as $methode){
            $name = ucfirst($methode->getName());
            $speed = $methode->getUnits()[0]->getSize();
            echo "<div class='movement-output-item'><p class='movement-name'>$name</p><p class='movement-time' speed='$speed'>-</p></div>";
        }
        ?>
        </div>
    </div>
</div>

<?php

$tool_webpage->endContent();