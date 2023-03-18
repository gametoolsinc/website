<?php
include_once($_SERVER['DOCUMENT_ROOT'] . "/library/template/tool/toolWebpage.php");
include_once $_SERVER['DOCUMENT_ROOT'] . "/library/dataManagement/games/units.php";

$tool_webpage = new ToolWebpage($_GET["id"]);
$tool_webpage->startContent();
?>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<style id="style"></style>


<div class="experiment" id="random">
    <div class="information">
        <h2>Random</h2>
        <p>Total photons simulated: <span class="total"></span></p>
    </div>
    <div class="HBT">
        <div class="photons main-source"></div>
        <div class="photons split-1"></div>
        <div class="photons split-2"></div>
        <div class="timer"></div>
    </div>

    <div class="chart">
        <canvas class="chart-canvas"></canvas>
    </div>
</div>

<div class="experiment" id="coherent_source">
    <div class="information">
        <h2>Coherent light source</h2>
        <p>Total photons simulated: <span class="total"></span></p>
    </div>
    <div class="HBT">
        <div class="photons main-source"></div>
        <div class="photons split-1"></div>
        <div class="photons split-2"></div>
        <div class="timer"></div>
    </div>

    <div class="chart">
        <canvas class="chart-canvas"></canvas>
    </div>
</div>

<div class="experiment" id="single_source">
    <div class="information">
        <h2>Single photon light source</h2>
        <p>Total photons simulated: <span class="total"></span></p>
    </div>
    <div class="HBT">
        <div class="photons main-source"></div>
        <div class="photons split-1"></div>
        <div class="photons split-2"></div>
        <div class="timer"></div>
    </div>

    <div class="chart">
        <canvas class="chart-canvas"></canvas>
    </div>
</div>


<?php

$tool_webpage->endContent();
