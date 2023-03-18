<?php
// Set content type
header("Content-type: text/xml");

include_once($_SERVER['DOCUMENT_ROOT'] . "/library/server/sitemap/sitemap.php");

$sitemap = new Sitemap();
echo $sitemap->getXml();
