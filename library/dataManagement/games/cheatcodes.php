<?php
include_once($_SERVER['DOCUMENT_ROOT'] . "/library/dataManagement/games/gameDataManagement.php");

class Cheatcodes extends GameDataManagement {
    private array $cheatcodes;
    private array $cheatcodesCategories;
    private array $platforms;

    function __construct(Tool $tool)
    {
        $data = $this->openFile($tool->getGame()->getPathResources() . "cheatcodes.json");

        $this->cheatcodes = [];
        $this->cheatcodesCategories = [];
        foreach ($data["categories"] as $categorie=>$categorieData){
            $cheatcodes = [];
            foreach ($categorieData as $cheatcodeData){
                $cheatcode = new Cheatcode($cheatcodeData["name"], $cheatcodeData["cheatcodes"]);
                array_push($this->cheatcodes, $cheatcode);
                array_push($cheatcodes, $cheatcode);
            }
            $cheatcodesCategorie = new CheatcodesCategorie($categorie, $cheatcodes);
            array_push($this->cheatcodesCategories, $cheatcodesCategorie);
        }
        $this->platforms = $data["platforms"];
    }

    function getGameSpecificWords()
    {
        return [];
    }

    /**
     * @return string[]
     */
    function getPlatforms(): array{
        return $this->platforms;
    }
    
    /**
     * @return CheatcodesCategorie[]
     */
    function getCheatcodesCategories(): array{
        return $this->cheatcodesCategories;
    }
}

class CheatcodesCategorie{
    private string $name;
    private array $cheatcodes;

    function __construct(string $name, array $cheatcodes)
    {
        $this->name = $name;
        $this->cheatcodes = $cheatcodes;
    }

    function getName(): string{
        return $this->name;
    }

    /**
     * @return Cheatcode[]
     */
    function getCheatcodes(): array{
        return $this->cheatcodes;
    }
}

class Cheatcode {
    private string $name;
    private array $cheatcodes;

    function __construct(string $name, array $cheatcodes)
    {
        $this->name = $name;
        $this->cheatcodes = $cheatcodes;
    }

    function getName(): string{
        return $this->name;
    }

    function getCheatcodes(): array{
        return $this->cheatcodes;
    }
}