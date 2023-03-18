<?php
include_once($_SERVER['DOCUMENT_ROOT'] . "/library/server/template/tool/toolWebpage.php");
include_once($_SERVER['DOCUMENT_ROOT'] . "/module/dice/dice.php");
include_once($_SERVER['DOCUMENT_ROOT'] . "/module/coin_flip/coinFlip.php");

$tool_webpage = new ToolWebpage($_GET["id"]);
$tool_webpage->startContent();

?>
<div id="tool-scoreboard">
    <div id="scoreboards">
        <div class="scoreboard">
            <p class="last-score">0</p>
            <p class="score">8000</p>
            <div class="plus" data-tooltip="Add score" onclick="addScore(0, 1)">
                <span class="material-symbols-outlined">
                    add
                </span>
            </div>
            <div class="minus" data-tooltip="Subtract score" onclick="addScore(0, -1)">
                <span class="material-symbols-outlined">
                    remove
                </span>
            </div>
        </div>
        <div class="scoreboard">
            <p class="last-score">0</p>
            <p class="score">8000</p>
            <div class="plus" data-tooltip="Add score" onclick="addScore(1, 1)">
                <span class="material-symbols-outlined">
                    add
                </span>
            </div>
            <div class="minus" data-tooltip="Subtract score" onclick="addScore(1, -1)">
                <span class="material-symbols-outlined">
                    remove
                </span>
            </div>
        </div>
    </div>

    <div class="input">
        <div class="controls">
            <div id="history-button" data-tooltip="View history" onclick="toggleShowLastScores()" >
                <span class="material-symbols-outlined" id="show-history-button">
                    history
                </span>
            </div>
            <div id="undo-button" data-tooltip="Undo" onclick="undoLastScore()">
                <span class="material-symbols-outlined">
                    undo
                </span>
            </div>
            <div id="random-button" data-tooltip="Open randomnes menu" onclick="toggleShowRandom()">
                <span class="material-symbols-outlined" id="show-random-button">
                    casino
                </span>
            </div>
            <div id="restart-button" data-tooltip="Reset scoreboard" onclick="reset()">
                <span class="material-symbols-outlined">
                    restart_alt
                </span>
            </div>
        </div>

        <div id="history" class="popup" style="display:none">
            <h3>History</h3>
            <div id="scores"></div>
        </div>

        <div id="random" class="popup" style="display:none">
            <h3>Random</h3>
            <div class="popup-tools">
                <?php
                $coinFlip = new CoinFlip($tool_webpage->getWebpage());
                $coinFlip->setWidth("40%");
                $coinFlip->place();
                $dice = new Dice($tool_webpage->getWebpage());
                $dice->setWidth("40%");
                $dice->place();
                ?>
            </div>
        </div>

        <div class="numpad">
            <div class="above">
                <div id="output">
                    <input type="number"></input>
                </div>
                <div class="delete-button" data-tooltip="Backspace" onclick="backspaceInput()">
                    <span class="material-symbols-outlined">
                        backspace
                    </span>
                </div>
            </div>
            <div class="numbers">
                <?php
                $numbers = ['1', '2', '3', '4', '5', '6', '7', '8', '9', '0', '00', '000'];
                foreach ($numbers as $number) {
                    echo "<div class='number' onclick='addToInput(\"{$number}\")'>
                <p>{$number}</p>
            </div>";
                }
                ?>
            </div>
        </div>
    </div>
</div>

<?php

$tool_webpage->endContent();