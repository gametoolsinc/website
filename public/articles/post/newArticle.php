<?php
include_once($_SERVER['DOCUMENT_ROOT'] . "/library/articles/articles.php");

$id = Articles::saveArticle($_POST);

$link = "/public/articles/editArticle.php?id={$id}";
echo "Edit your article at <a href='$link'>$link</a>";