<?php
include_once($_SERVER['DOCUMENT_ROOT'] . "/library/template/webpage/webpage.php");
include_once($_SERVER['DOCUMENT_ROOT'] . "/module/module.php");

class Navbar extends Module {
    function __construct(Webpage $webpage){
        $webpage->addJavascript("/module/navbar/menu.js");
        $webpage->addStylesheet("/module/navbar/stylesheet.css");
    }

    function place(): void {
        include($_SERVER['DOCUMENT_ROOT'] . "/module/navbar/webpage.php");
    }
}
