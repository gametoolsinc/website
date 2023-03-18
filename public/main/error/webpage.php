<?php
include_once($_SERVER['DOCUMENT_ROOT'] . "/library/template/webpage/webpage.php");
include_once($_SERVER['DOCUMENT_ROOT'] . "/library/redirect/redirect.php");

if (strpos($_SERVER['REQUEST_URI'], '?') !== false) {
    $url = explode("?", $_SERVER['REQUEST_URI'])[0];
} else {
    $url = $_SERVER['REQUEST_URI'];
}
$redirect = Redirect::getRedirect($url);
if ($redirect !== false){
    header("HTTP/1.1 301 Moved Permanently");
    header("Location: $redirect");
    exit();
}

$webpage = new Webpage();

$webpage->setTitle("Error");
$webpage->addStylesheet("/public/main/error/stylesheet.css");
$webpage->startContent();
?>

<div class="wrapper">
    <h1>There was an error!</h1>
    <?php
    echo "<p>{$_GET['errormessage']}</p>";
    ?>
    <p>Return <a href="/">home</a></p>
</div>
<?php

$webpage->endContent();

$webpage->generateWebpage();