<?php
include_once($_SERVER['DOCUMENT_ROOT'] . "/library/imageHost/imageHost.php");
include_once($_SERVER['DOCUMENT_ROOT'] . "/library/dataManagement/game.php");
include_once($_SERVER['DOCUMENT_ROOT'] . "/library/dataManagement/application.php");
include_once($_SERVER['DOCUMENT_ROOT'] . "/library/dataManagement/tool.php");
include_once($_SERVER['DOCUMENT_ROOT'] . "/library/dataManagement/tools.php");
include_once($_SERVER['DOCUMENT_ROOT'] . "/library/articles/articles.php");
include_once($_SERVER['DOCUMENT_ROOT'] . "/library/template/webpage/webpage.php");
include_once($_SERVER['DOCUMENT_ROOT'] . "/module/link/link.php");

$webpage = new Webpage();

$webpage->setTitle("Home");
$webpage->setDescription("Gamertools has all the tools for multiple games. Select the needed tool for your game!");
$webpage->setKeywords(["Gamertools", "game calculators" . "game tools"]);
$webpage->addStylesheet("/public/main/homepage/stylesheet.css");

$webpage->startContent();
?>
<header>
    <h1>GamerTools</h1>
</header>
<section>
    <h2><a href="<?= $webpage::upgradeUrl("/games") ?>">Games</a></h2>
    <?php
    $links = new LinkGrid($webpage);
    $links->setAmountOfRows(1);
    $gameIds = Game::getAllGameIds();
    foreach ($gameIds as $gameId) {
        $game = new Game($gameId);
        if ($game->isVisible()) {
            $links->newLink(LinkType::game, $game->getName(), $game->getDescription(), $game->getPreviewImage(), $game->getUrl());
        }
    }
    $links->place();
    ?>
</section>
<section>
    <h2><a href="<?= $webpage::upgradeUrl("/articles") ?>">Articles</a></h2>
    <?php
    $links = new LinkGrid($webpage);
    $links->setAmountOfRows(1);
    $articles = Articles::getAllArticles();
    foreach ($articles as $article) {
        $links->newLink(LinkType::article, $article->getTitle(), $article->getIntroduction(), $article->getImage(), $article->getUrl());
    }
    $links->place();
    ?>
</section>
<section>
    <h2><a href="<?= $webpage::upgradeUrl("/applications") ?>">Applications</a></h2>
    <?php
    $links = new LinkGrid($webpage);
    $links->setAmountOfRows(1);
    $applicationIds = Application::getAllApplicationIds();
    foreach ($applicationIds as $applicationId) {
        $application = new Application($applicationId);
        if ($application->isVisible()) {
            $links->newLink(LinkType::application, $application->getName(), $application->getDescription(), $application->getIcon(), $application->getUrl());
        }
    }
    $links->place();
    ?>
</section>
<section>
    <h2><a href="<?= $webpage::upgradeUrl("/tools") ?>">Tools</a></h2>
    <?php
    $links = new LinkGrid($webpage);
    $links->setAmountOfRows(1);
    $tools = new Tools();
    foreach ($tools->getAllTools() as $tool) {
        if ($tool->isVisible()) {
            $links->newLink(LinkType::tool, $tool->getName(), $tool->getDescription(), [$tool->getGame()->getPreviewImage(), $tool->getApplication()->getIcon()], $tool->getUrl());
        }
    }
    $links->place();
    ?>
</section>
<?php
$webpage->endContent();

$webpage->generateWebpage();
