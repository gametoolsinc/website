<?php
include_once($_SERVER['DOCUMENT_ROOT'] . "/library/dataManagement/games/gameDataManagement.php");

class Furnaces extends GameDataManagement {
    private array $furnaces;

    function __construct(Tool $tool)
    {
        $data = $this->openFile($tool->getGame()->getPathResources() . "furnaces.json");

        $this->furnaces = [];
        foreach ($data as $furnace){
            $furnace = new Furnace($furnace["name"], $furnace["time"], $furnace["item_image"], $furnace["smelting_image"]);
            array_push($this->furnaces, $furnace);
        }
    }

    function getGameSpecificWords(){
        return [];
    }
    
    /**
     * @return Furnace[]
     */
    function getFurnaces(): array{
        return $this->furnaces;
    }
}


class Furnace {
    private string $name;
    private int $time;
    private string $item_image;
    private string $smelting_image;

    function __construct(string $name, int $time, string $item_image, string $smelting_image)
    {
        $this->name = $name;
        $this->time = $time;
        $this->item_image = $item_image;
        $this->smelting_image = $smelting_image;
    }

    function getName(): string{
        return $this->name;
    }

    function getTime(): int{
        return $this->time;
    }

    function getItemImage(): string{
        return $this->item_image;
    }

    function getSmeltingImage(): string{
        return $this->smelting_image;
    }
}