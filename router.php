<?php
include_once($_SERVER['DOCUMENT_ROOT'] . "/library/server/caching/caching.php");
include_once($_SERVER['DOCUMENT_ROOT'] . "/library/server/template/webpage/webpage.php");

$webpage_path = $_SERVER['REQUEST_URI'];
$type = "debug";

// Find correct page
$regexs = [
    "/^\/$/" => "/public/main/homepage/webpage.php?",

    "/^\/games$/" => "/public/indexes/indexIndex/indexIndex.php?type=game",
    "/^\/games\/(\w+)$/" => "/public/indexes/toolIndex/toolIndex.php?game=$1",

    "/^\/applications$/" => "/public/indexes/indexIndex/indexIndex.php?type=application",
    "/^\/applications\/(\w+)$/" => "/public/indexes/toolIndex/toolIndex.php?application=$1",

    "/^\/articles$/" => "/public/indexes/indexIndex/indexIndex.php?type=article",
    "/^\/articles\/([0-9]+)$/" => "/public/articles/article.php?id=$1",

    "/^\/tools$/" => "/public/indexes/toolIndex/toolIndex.php?",
    "/^\/tools\/([0-9]+)$/" => "/public/applications/router.php?id=$1",
    "/^\/admingamertools$/"=> "/public/main/admin/webpage.php",
];

foreach ($regexs as $regex => $replace) {
    if (preg_match($regex, $webpage_path)) {
        $internal_path =  $_SERVER['DOCUMENT_ROOT'] . preg_replace($regex, $replace, $webpage_path);
        break; 
    }
}


if (!isset($internal_path)){
    Webpage::show404();
}

// Cached
if ($type == "cached") {
  $internal_path = $_SERVER['DOCUMENT_ROOT'] . preg_replace("/^\/([0-9a-zA-Z\/_]*)\?{0,1}([0-9a-zA-Z\/_=&]*)$/", "/cachedWebpages/$1-/webpage.html?$2", $webpage_path);
}

error_log($type.": ".$webpage_path." to ".$internal_path);

// Set parameters
$_GET = [];
$parameters = explode("?", $internal_path)[1];
$parameters = explode("&", $parameters);
foreach ($parameters as $parameter) {
    if ($parameter != ""){
        $_GET[explode("=", $parameter)[0]] = explode("=", $parameter)[1];
    }
}

// Caching
if ($type == "cach"){
    $caching = new Caching($webpage_path);
    $caching->startCaching();
}

// include the page
$file_location = explode("?", $internal_path)[0];
include $file_location;

// Caching
if ($type == "cach"){
    $caching->endCaching();
    $caching->generateNewWebpage();
}
