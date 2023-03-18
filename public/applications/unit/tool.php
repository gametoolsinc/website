<?php
include_once($_SERVER['DOCUMENT_ROOT'] . "/library/template/tool/toolWebpage.php");
include_once $_SERVER['DOCUMENT_ROOT'] . "/library/dataManagement/games/units.php";

$tool_webpage = new ToolWebpage($_GET["id"]);
$tool_webpage->startContent();

$tool = new Tool($_GET['id']);
$data = new Units($tool_webpage->getTool());

function generateOption(Units $data, String $side)
{
    $optionHTML = '<option {parameters} {selected}>{name}</option>
        ';

    foreach ($data->getUnitCategories() as $unitCategorie) {
        // Generate parameters
        $options = [];
        foreach ($unitCategorie->getUnits() as $index => $unit) {
            array_push($options, "unit-{$index}='{$unit->getName()}'");
            array_push($options, "size-{$index}='{$unit->getSize()}'");
            array_push($options, "decimals-{$index}='0'");
        }

        // Generate html
        $html = $optionHTML;
        if (($side === "left" && $unitCategorie === $data->getLeftDefaultUnit()) || ($side === "right" && $unitCategorie === $data->getRightDefaultUnit())) {
            $html = str_replace("{selected}", "selected='true'", $html);
        } else {
            $html = str_replace("{selected}", "", $html);
        }
        $html = str_replace("{name}", $unitCategorie->getName(), $html);
        $html = str_replace("{parameters}", implode(" ", $options), $html);
        echo $html;
    }
}
?>

<div class="unit_container">
    <div class="left_unit">
        <select oninput="tabSwitch(this)" onload="tabSwitch(this)">
            <?php generateOption($data, "left"); ?>
        </select>
        <div class="content"></div>
    </div>
    <div class="switch_units" onclick="switchUnits(this)">
        <p>
            < Switch>
        </p>
    </div>
    <div class="right_unit">
        <select oninput="tabSwitch(this)" onload="tabSwitch(this)">
            <?php generateOption($data, "right"); ?>
        </select>
        <div class="content"></div>
    </div>
</div>

<?php

$tool_webpage->endContent();