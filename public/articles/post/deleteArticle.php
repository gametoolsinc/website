<?php
include_once($_SERVER['DOCUMENT_ROOT'] . "/library/articles/articles.php");

$id = $_GET["id"];
Articles::deleteArticle($id);

echo "Deleted $id!";