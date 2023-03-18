<?php
include_once($_SERVER['DOCUMENT_ROOT'] . "/library/server/template/tool/toolWebpage.php");
$tool_webpage = new ToolWebpage($_GET["id"]);
$tool_webpage->startContent();
?>

<div class="nether_calculator">
    <div class="box_container">
        <div class="overworld">
            <div class="gradient">
                <h2>Overworld</h2>
            </div>
            <div class="content">
                <div class="overworld_x">
                    <h2>X</h2>
                    <input type="number" id="overworld_x" oninput="calculateCoordinates(this)" value="0">
                </div>
                <div class="overworld_y">
                    <h2>Y</h2>
                    <input type="number" id="overworld_y" oninput="calculateCoordinates(this)" value="65">
                </div>
                <div class="overworld_z">
                    <h2>Z</h2>
                    <input type="number" id="overworld_z" oninput="calculateCoordinates(this)" value="0">
                </div>
            </div>
            
        </div>

        <div class="nether">
            <div class="gradient">
                <h2>Nether</h2>
            </div>
            <div class="content">
                <div class="nether_x">
                    <h2>X</h2>
                    <input type="number" id="nether_x" oninput="calculateCoordinates(this)">
                </div>
                <div class="nether_y">
                    <h2>Y</h2>
                    <input type="number" id="nether_y" oninput="calculateCoordinates(this)">
                </div>
                <div class="nether_z">
                    <h2>Z</h2>
                    <input type="number" id="nether_z" oninput="calculateCoordinates(this)">
                </div>
            </div>
        </div>
    </div>

    <h2>Instructions</h2>
    <ol class="instructions">
        <li>Build your nether portal in the overworld, but <b>do not light it yet</b>.</li>
        <li>Press F3 while inside your portal frame and note your portal's coordinates. Fill these overworld coordinates in in the calculator. The nether coordinates will automatically be calculated.</li>
        <li>Light your portal and go into the Nether</li>
        <li>Go to your coordinates in the Nether: <span class="coordinates">(0,65,0)</span>.</li>
        <li>Place a obsidian block on these coordinates</li>
        <li>Use the obsidian as the base of your portal, finish your portal and light it</li>
        <li><b>Important! Remove/disable your old portal you came through in step 3</b></li>
        <li>Go through your newly built portal in the nether at <span class="coordinates">(0,65,0)<span>. The portals should now be linked</li>
    </ol>
</div>

<?php
$tool_webpage->endContent();