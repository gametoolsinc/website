<?php
include_once($_SERVER['DOCUMENT_ROOT'] . "/module/module.php");

class Consent extends Module {
    function __construct(Webpage $webpage)
    {
        $webpage->addJavascript("/module/consent/javascript.js");
        $webpage->addStylesheet("/module/consent/stylesheet.css");
    }

    function place(){
        include($_SERVER['DOCUMENT_ROOT'] . "/module/consent/consentBanner.html");
        include($_SERVER['DOCUMENT_ROOT'] . "/module/consent/preferences.html");
    }
}