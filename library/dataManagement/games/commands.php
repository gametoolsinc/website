<?php 
include_once($_SERVER['DOCUMENT_ROOT'] . "/library/dataManagement/games/gameDataManagement.php");

class Command extends GameDataManagement {

    private Tool $tool;
    public $command;
    public array $types;
    private array $commands;
    

    function __construct(Tool $tool, $command = "") {
        $this->tool = $tool;
        $this->command = $command;
        
        $data = $this->openFile($this->tool->getGame()->getPathResources() . "commandsInformation.json");
        $this->commands = $data["commands"];

        $this->types = $data["types"];
    }

    function getGameSpecificWords() {

    }

    public function getCommands() {
        return $this->commands;
    }

    public function getCommand() {
        return $this->commands[$this->command];
    }

    public function getTypes() {
        return $this->types;
    }

    public function getNavItem($name) {
        $game = $this->tool->getGame()->getName();
        return '<div class="page-menu-item"><a href="/' . $game . '/commands/'. $name . '">' . $name . '</a></div>';
    }

    public function getJSONFile($file_name) {
        $file_data = $this->openFile($this->tool->getGame()->getPathResources() . $file_name);

        return $file_data;
    }
}

?>