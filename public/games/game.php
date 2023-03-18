<?php

include_once($_SERVER['DOCUMENT_ROOT'] . "/library/dataManagement/game.php");
include_once($_SERVER['DOCUMENT_ROOT'] . "/library/dataManagement/tool.php");
include_once($_SERVER['DOCUMENT_ROOT'] . "/library/template/webpage/webpage.php");

$game_id = $_GET['id'];

$game = new Game($game_id);

$webpage = new Webpage($game->getBackgroundColors());
$webpage->setTitle($game->getName());
$webpage->setKeywords(["{$game->getName()} tools", "{$game->getName()} calculators"]);
$webpage->addStylesheet("/public/games/stylesheet.css");

$toolIds = $game->getToolIds();
$tools = [];
foreach ($toolIds as $toolId) {
    $tool = new Tool($toolId);
    array_push($tools, $tool->getApplication()->getName());
}
$availableToolsStr = implode(", ", $tools);
$webpage->setDescription("Here you can select the needed tool for {$game->getName()}. We currently have these tools available: $availableToolsStr");

$webpage->addStyle("
            :root {
                --main: {$game->getMainColor()};
                --accent: {$game->getAccentColor()};
            }");
$webpage->startContent();
?>
<div class="wrapper">
    <div class="tools">
        <div class="main_title">
            <?= "<h1>Tools for {$game->getName()}</h1>" ?>
        </div>
        <section class="pages">
            <?php
            // place pages
            $sampleHtmlItem = '
                    <div class="page">
                        <a href={link}>
                            <img src="{icon}" alt="icon {name}">
                            <div class="gradient">
                                <h2 class=page_title>{name}</h2>
                                <p>{description}</p>
                            </div>
                        </a>
                    </div>
                    ';

            foreach ($toolIds as $toolId) {
                $tool = new Tool($toolId);

                $html = $sampleHtmlItem;

                //Tool related
                $html = str_replace("{link}", $tool->getUrl($game_id), $html);
                $html = str_replace("{icon}", $tool->getApplication()->getIcon(), $html);
                $html = str_replace("{name}", $tool->getName(), $html);
                $html = str_replace("{description}", $tool->getDescription(), $html);
                echo $html;
            }
            ?>
        </section>
    </div>
</div>

<?php
$webpage->endContent();
$webpage->generateWebpage();
?>