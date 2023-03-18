

<?php
include_once($_SERVER['DOCUMENT_ROOT'] . "/library/template/webpage/webpage.php");
include_once($_SERVER['DOCUMENT_ROOT'] . "/module/module.php");

class CoinFlip extends Module {
    private string $width;

    function __construct(Webpage $webpage){
        $this->width = "100%";
        $webpage->addStylesheet("/module/coin_flip/stylesheet.css");
        $webpage->addJavascript("/module/coin_flip/javascript.js");
    }

    function setWidth(string $width){
        $this->width = $width;
    }

    function place(){
        echo "<div class='coin' onclick='throwCoin()' style='width: {$this->width};'></div>";
    }
}



if (basename(__FILE__) == basename($_SERVER["SCRIPT_FILENAME"])) {
    $webpage = new Webpage();
    $webpage->startContent();
    $dice = new CoinFlip($webpage);
    $dice->setWidth("100px");
    $dice->place();
    $webpage->endContent();
    $webpage->generateWebpage();
}