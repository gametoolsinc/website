<?php
include_once($_SERVER['DOCUMENT_ROOT'] . "/library/caching/caching.php");
include_once($_SERVER['DOCUMENT_ROOT'] . "/library/template/webpage/webpage.php");

$webpage_path = strtok($_SERVER["REQUEST_URI"], '?');
if (array_key_exists("type", $_GET)) {
    $type = $_GET["type"];
} else {
    $type = "cached";
}

$special_pages = [
    "/^\/adminGamertools$/" => "/public/main/admin/webpage.php?",
];

foreach ($special_pages as $page => $replace) {
    if (preg_match($page, $webpage_path)) {
        $internal_path =  $_SERVER['DOCUMENT_ROOT'] . preg_replace($page, $replace, $webpage_path);
        $type = "debug";
        break;
    }
}

// Show cached version
if ($type == "cached") {
    $cached = new Caching($webpage_path);
    $cached->show();
    exit();
}

// Find correct page
$pages = [
    "/^\/$/" => "/public/main/homepage/webpage.php?",

    "/^\/games$/" => "/public/overview/index_overview/index_overview.php?type=game",
    "/^\/games\/(\w+)$/" => "/public/overview/tool_overview/tool_overview.php?game=$1",

    "/^\/applications$/" => "/public/overview/index_overview/index_overview.php?type=application",
    "/^\/applications\/(\w+)$/" => "/public/overview/tool_overview/tool_overview.php?application=$1",

    "/^\/articles$/" => "/public/overview/index_overview/index_overview.php?type=article",
    "/^\/articles\/([0-9]+)$/" => "/public/articles/article.php?id=$1",

    "/^\/tools$/" => "/public/overview/tool_overview/tool_overview.php?",
    "/^\/tools\/([0-9]+)$/" => "/public/applications/router.php?id=$1",

];

foreach ($pages as $page => $replace) {
    if (preg_match($page, $webpage_path)) {
        $internal_path =  $_SERVER['DOCUMENT_ROOT'] . preg_replace($page, $replace, $webpage_path);
        break;
    }
}



// Show 404
if (!isset($internal_path)) {
    Webpage::show404();
    exit();
}

// Set parameters
$_GET = [];
$parameters = explode("?", $internal_path)[1];
$parameters = explode("&", $parameters);
foreach ($parameters as $parameter) {
    if ($parameter != "") {
        $_GET[explode("=", $parameter)[0]] = explode("=", $parameter)[1];
    }
}

// Caching
if ($type == "cach") {
    $caching = new Caching($webpage_path);
    $caching->startCaching();
}

// include the page
$file_location = explode("?", $internal_path)[0];
include $file_location;

// Caching
if ($type == "cach") {
    $caching->endCaching();
    $caching->generateNewWebpage();
}
