<?php

include_once($_SERVER['DOCUMENT_ROOT'] . "/library/server/dataManagement/game.php");
include_once($_SERVER['DOCUMENT_ROOT'] . "/library/server/dataManagement/application.php");
include_once($_SERVER['DOCUMENT_ROOT'] . "/library/server/articles/articles.php");
include_once($_SERVER['DOCUMENT_ROOT'] . "/library/server/dataManagement/tool.php");
include_once($_SERVER['DOCUMENT_ROOT'] . "/library/server/template/webpage/webpage.php");

?>

<nav>
    <div class="nav-wrapper">
        <div class="hamburger-icon" onclick="toggleNavBarMenu()">
            <span class="material-symbols-outlined">menu</span>
        </div>
        <a href="<?= \Webpage::upgradeUrl("/") ?>">
            <div class="logo">
                <img loading="lazy" src="/resources/main/logo.png" alt="logo"></img>
            </div>
        </a>
        <div class="navigation">
            <div>
                <a href="<?= \Webpage::upgradeUrl("/games") ?>">
                    <p class="title">Games</p>
                </a>
                <div class="dropdown">
                    <?php

                    // Get current games
                    $gameIds = \Game::getAllGameIds();

                    // get data
                    foreach ($gameIds as $gameId) {
                        $game = new \Game($gameId);
                        if ($game->isVisible()) {
                            $url = $game->getUrl();
                            $name = $game->getName();
                            echo "<a href='$url'>$name</a>";
                        }
                    }

                    ?>
                </div>
            </div>

            <div>
                <a href="<?= \Webpage::upgradeUrl("/applications") ?>">
                    <p class="title">Applications</p>
                </a>
                <div class="dropdown">
                    <?php

                    // Get current games
                    $applicationIds = \Application::getAllApplicationIds();

                    // get data
                    foreach ($applicationIds as $applicationId) {
                        $application = new \Application($applicationId);
                        if ($application->isVisible()) {
                            $url = $application->getUrl();
                            $name = $application->getName();
                            echo "<a href='$url'>$name</a>";
                        }
                    }

                    ?>
                </div>
            </div>
            <div>
                <a href="<?= \Webpage::upgradeUrl("/articles") ?>">
                    <p class="title">Articles</p>
                </a>
                <div class="dropdown">
                    <?php

                    // Get current games
                    $articles = \Articles::getAllArticles();

                    // get data
                    for ($i = 0; $i < 4; $i++) {
                        $article = $articles[$i];
                        $url = $article->getUrl();
                        $name = $article->getTitle();
                        echo "<a href='$url'>$name</a>";
                    }

                    ?>
                </div>
            </div>
        </div>
    </div>
</nav>
<div class="mobile-menu">
    <a href="<?= \Webpage::upgradeUrl("/") ?>"><span>Home</span></a>
    <span>Games</span>
    <div>
        <?php

        // get data
        foreach ($gameIds as $gameId) {
            $game = new \Game($gameId);
            if ($game->isVisible()) {
                $url = $game->getUrl();
                $name = $game->getName();
                echo "<a href='$url'>$name</a>";
            }
        }

        ?>
    </div>

    <a href="<?= \Webpage::upgradeUrl("/applications") ?>"><span>Applications</span></a>

    <a href="<?= \Webpage::upgradeUrl("/articles") ?>"><span>Articles</span></a>
</div>
<div class="overlay" onclick="toggleNavBarMenu()"></div>