<?php
include_once($_SERVER['DOCUMENT_ROOT'] . "/library/articles/articles.php");

Articles::deleteArticle($_POST["id"]);
Articles::saveArticle($_POST, $_POST["id"]);

echo "Updated your article!";