<?php
include_once($_SERVER['DOCUMENT_ROOT'] . "/library/template/webpage/webpage.php");
include_once($_SERVER['DOCUMENT_ROOT'] . "/module/link/link.php");
include_once($_SERVER['DOCUMENT_ROOT'] . "/library/dataManagement/game.php");
include_once($_SERVER['DOCUMENT_ROOT'] . "/library/dataManagement/application.php");
include_once($_SERVER['DOCUMENT_ROOT'] . "/library/articles/articles.php");

$type_webpage = $_GET["type"];

$webpage = new Webpage();
$webpage->setTitle("All $type_webpage");

$webpage->startContent();

$links = new LinkGrid($webpage);
switch ($type_webpage) {
    case "game":
        echo "<h1>Games</h1>";
        $gameIds = Game::getAllGameIds();
        foreach ($gameIds as $gameId) {
            $game = new Game($gameId);
            if ($game->isVisible()) {
                $links->newLink(LinkType::game, $game->getName(), $game->getDescription(), $game->getPreviewImage(), $game->getUrl());
            }
        }
        break;
    case "article":
        echo "<h1>Articles</h1>";
        $articles = Articles::getAllArticles();
        foreach ($articles as $article) {
            $links->newLink(LinkType::article, $article->getTitle(), $article->getIntroduction(), $article->getImage(), $article->getUrl());
        }
        break;
    case "application":
        echo "<h1>Applications</h1>";
        $applicationIds = Application::getAllApplicationIds();
        foreach ($applicationIds as $applicationId) {
            $application = new Application($applicationId);
            if ($application->isVisible()) {
                $links->newLink(LinkType::application, $application->getName(), $application->getDescription(), $application->getIcon(), $application->getUrl());
            }
        }
        break;
}

$links->place();



$webpage->endContent();
$webpage->generateWebpage();
