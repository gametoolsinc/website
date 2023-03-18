<?php 
include_once($_SERVER['DOCUMENT_ROOT'] . "/library/server/template/tool/toolWebpage.php");
include_once $_SERVER['DOCUMENT_ROOT'] . "/library/server/dataManagement/games/furnaces.php";

$tool_webpage = new ToolWebpage($_GET["id"]);
$tool_webpage->startContent();

$tool = new Tool($_GET['id']);
$data = new Furnaces($tool);

$smelt_template = '
<div class="smelt_type">
    <div class="time_calculator">
        <div class="images">
            {img}
        </div>
        <div class="input">
            <label>
                <input type="number" id="input" oninput="calcTime(this, {time})" placeholder="Amount" min="0">
            </label>
            <p>This will take:</p>
            <p id="output"></p>
        </div>
    </div>

    <hr>
    <div class="furnace_calculator">
        <h2>Calculate required furnaces</h2>
        <div class="time_input">
            <label><input type="number" id="days" oninput="calcFurnace(this, {time})" placeholder="Days" min="0"></label>
            <label><input type="number" id="hours" oninput="calcFurnace(this, {time})" placeholder="Hours" min="0"></label>
            <label><input type="number" id="minutes" oninput="calcFurnace(this, {time})" placeholder="Minutes" min="0"></label>
            <label><input type="number" id="seconds" oninput="calcFurnace(this, {time})" placeholder="Seconds" min="0"></label>
        </div>
        <div>
            <label><input type="number" id="amount" oninput="calcFurnace(this, {time})" placeholder="Amount of items" min="0"></label>
        </div>
        <p>Required furnace amount:</p>
        <p id="furnace_output">0 furnaces</p>
    </div>
</div>';

foreach($data->getFurnaces() as $furnace) {
    $html = str_replace("{time}", $furnace->getTime(), $smelt_template);

    $url = '<img src="/resources/games/minecraft/user_interface/' . $furnace->getItemImage() . '" id="normal">';
    $url .= '<img src="/resources/games/minecraft/user_interface/' . $furnace->getSmeltingImage() . '" id="smelting">';
    $html = str_replace("{img}", $url, $html);
    echo $html;
}

$tool_webpage->endContent();