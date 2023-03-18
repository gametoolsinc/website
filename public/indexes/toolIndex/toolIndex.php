<?php
include_once($_SERVER['DOCUMENT_ROOT'] . "/library/server/template/webpage/webpage.php");
include_once($_SERVER['DOCUMENT_ROOT'] . "/module/link/link.php");
include_once($_SERVER['DOCUMENT_ROOT'] . "/library/server/dataManagement/tool.php");

$current_game_id = "";
$current_application_id = "";
if (array_key_exists("game", $_GET)) {
    $current_game_id = $_GET["game"];
}
if (array_key_exists("application", $_GET)) {
    $current_application_id = $_GET["application"];
}

$webpage = new Webpage();
$webpage->setTitle("Tools");
$webpage->addStylesheet("/public/indexes/toolIndex/stylesheet.css");

$webpage->startContent();
?>
<header class="header">
    <h1>Tools</h1>
</header>
<div class="options">
    <select name="games" id="games" onchange="location = this.value;">
        <option value="" selected disabled hidden>Choose game</option>
        <?php

        // Get current games
        $gameIds = \Game::getAllGameIds();

        // get data
        foreach ($gameIds as $gameId) {
            $game = new \Game($gameId);
            if ($game->isVisible()) {
                $url = $game->getUrl();
                $name = $game->getName();
                $html = "<option selected value='$url'>$name</option>";
                if ($gameId != $current_game_id){
                    $html = str_replace("selected", "", $html);
                }
                echo $html;
            }
        }

        ?>
    </select>
    <select name="application" id="application" onchange="location = this.value;">
        <option value="" selected disabled hidden>Choose application</option>
        <?php

        // Get current games
        $applicationIds = \Application::getAllApplicationIds();

        // get data
        foreach ($applicationIds as $applicationId) {
            $application = new \Application($applicationId);
            if ($application->isVisible()) {
                $url = $application->getUrl();
                $name = $application->getName();
                $html = "<option selected value='$url'>$name</option>";
                if ($applicationId != $current_application_id){
                    $html = str_replace("selected", "", $html);
                }
                echo $html;
            }
        }

        ?>
    </select>
</div>
<div class="tools">
    <?php
    $links = new LinkGrid($webpage);

    $toolIds = Tool::getAllToolIds();
    foreach ($toolIds as $toolId) {
        $tool = new Tool($toolId);
        if (
            $tool->isVisible()
            && ($tool->getGame()->getId() == $current_game_id || $current_game_id == "")
            && ($tool->getApplication()->getId() == $current_application_id || $current_application_id == "")
        ) {
            $links->newLink(LinkType::tool, $tool->getName(), $tool->getDescription(), [$tool->getGame()->getPreviewImage(), $tool->getApplication()->getIcon()], $tool->getUrl());
        }
    }

    $links->place();

    ?>

</div>

<?php

$webpage->endContent();
$webpage->generateWebpage();
