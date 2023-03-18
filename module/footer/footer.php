<?php
include_once($_SERVER['DOCUMENT_ROOT'] . "/library/server/template/webpage/webpage.php");
include_once($_SERVER['DOCUMENT_ROOT'] . "/module/module.php");

class Footer extends Module {
    function __construct(Webpage $webpage){
        $webpage->addStylesheet("/module/footer/stylesheet.css");
    }

    function place(): void {
        include $_SERVER['DOCUMENT_ROOT'] . "/module/footer/webpage.php";
    }
}
