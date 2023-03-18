<?php
include_once($_SERVER['DOCUMENT_ROOT'] . "/library/server/dataManagement/games/gameDataManagement.php");

class Transportation extends GameDataManagement{
    private array $methodes;

    function __construct(Tool $tool){
        $data = $this->openFile($tool->getGame()->getPathResources() . "transportation.json");
        $this->methodes = [];
        foreach ($data as $key=>$value){
            $transportationMethode = new TransportationMethode($key, $value);
            array_push($this->methodes, $transportationMethode);
        }
    }

    function getGameSpecificWords()
    {
        return [];
    }

    /**
     * @return TransportationMethode[]
     */
    function getMethodes(){
        return $this->methodes;
    }
}

class TransportationMethode {
    private string $name;
    private float $speed;

    function __construct(string $name, float $speed)
    {
        $this->name = $name;
        $this->speed = $speed;
    }

    public function getName(){
        return $this->name;
    }

    public function getSpeed(){
        return $this->speed;
    }
}