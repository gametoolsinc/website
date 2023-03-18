<?php
include_once($_SERVER['DOCUMENT_ROOT'] . "/library/server/articles/article.php");
include_once($_SERVER['DOCUMENT_ROOT'] . "/library/server/template/webpage/webpage.php");

$article = new Article($_GET['id']);
$webpage = new Webpage();
$webpage->setTitle($article->getTitle());
$webpage->setDescription("An article with the titel '{$article->getTitle()}'");
$webpage->setKeywords([$article->getTitle()]);
$webpage->addStylesheet('/public/articles/article.css');

$webpage->startContent();
?>
<article>
    <?php

    // Display title
    echo "<h1>{$article->getTitle()}</h1>";

    // Display extra information
    $amountOfWords = $article->getAmountOfWords();
    $readingTime = ceil($amountOfWords / 250);
    echo "<div class='information'><p>$amountOfWords words | $readingTime minute read </p></div>";

    // Display article parts
    echo "<div class='content'>";
    $article->placeArticleParts();
    echo "</div>";

    ?>
</article>

<?php

$webpage->endContent();
$webpage->generateWebpage();
