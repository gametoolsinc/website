<?php
include_once($_SERVER['DOCUMENT_ROOT'] . "/library/dataManagement/game.php");
include_once($_SERVER['DOCUMENT_ROOT'] . "/library/dataManagement/application.php");
include_once($_SERVER['DOCUMENT_ROOT'] . "/library/dataManagement/tool.php");
include_once($_SERVER['DOCUMENT_ROOT'] . "/library/articles/articles.php");


class Sitemap
{
    private array $pages;
    
    function __construct()
    {
        $this->pages = [];

        // Default
        array_push($this->pages, new Page("", "weekly", 0.5));

        // Games
        array_push($this->pages, new Page("/games", "monthly", 0.4));
        $gameIds = Game::getAllGameIds();
        foreach ($gameIds as $gameId){
            $game = new Game($gameId);
            if ($game->isVisible()){
                array_push($this->pages, new Page($game->getUrl(), "monthly", 0.3));
            }
        }

        // Applications
        array_push($this->pages, new Page("/applications", "monthly", 0.4));
        $applicationIds = Application::getAllApplicationIds();
        foreach ($applicationIds as $applicationId){
            $application = new Application($applicationId);
            if ($application->isVisible()){
                array_push($this->pages, new Page($application->getUrl(), "monthly", 0.3));
            }
        }

        // Tools
        $toolIds = Tool::getAllToolIds();
        foreach ($toolIds as $toolId){
            $tool = new Tool($toolId);
            if ($tool->isVisible()){
                array_push($this->pages, new Page($tool->getUrl(), "yearly", 1.0));
            }
        }

        // Articles
        array_push($this->pages, new Page("/articles", "monthly", 0.2));
        $articles = Articles::getAllArticles();
        foreach ($articles as $article){
            array_push($this->pages, new Page($article->getUrl(), "never", 0.5));
        }
    }


    function getXml()
    {
        $html = '<?xml version="1.0" encoding="UTF-8"?><urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">';

        foreach ($this->pages as $page){
            $html .= $page->getXml();
        }

        $html .= "</urlset>";

        return $html;
    }

    // Get base url
    static function getBaseUrl()
    {
        return sprintf(
            "%s://%s",
            isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off' ? 'https' : 'http',
            $_SERVER['SERVER_NAME']
        );
    }
}

class Page {
    private $path;
    private $changefreq;
    private $priority;

    function __construct(string $path, string $changefreq, float $priority)
    {
        $this->path = $path;
        $this->changefreq = $changefreq;
        $this->priority = $priority;
    }

    function getPath(){
        return $this->path;
    }

    function getXml(){
        $url = Sitemap::getBaseUrl() . $this->path;
        return "
        <url>
            <loc>{$url}</loc>
            <changefreq>{$this->changefreq}</changefreq>
            <priority>{$this->priority}</priority>
        </url>";
    }
}