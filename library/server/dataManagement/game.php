<?php

include_once($_SERVER['DOCUMENT_ROOT'] . "/library/server/colours/colours.php");
include_once($_SERVER['DOCUMENT_ROOT'] . "/library/server/template/webpage/webpage.php");

class Game
{
    private $id;
    private $data;

    static function gameExists(string $id) {
        $path = $_SERVER['DOCUMENT_ROOT'] . Game::getPathResources($id);
        if (file_exists($path)) {
            return true;
        } else {
            return false;
        }
    }

    static function getAllGameIds(){
        $ids = scandir($_SERVER['DOCUMENT_ROOT'] . "/resources/games");
        $ids = array_values(array_filter($ids, array("Game", "validFileName")));
        return $ids;
    }

    public function __construct(string $id)
    {
        $this->id = $id;
        $path = $_SERVER['DOCUMENT_ROOT'] . $this->getPathResources() . "gameInformation.json";
        if (file_exists($path)) {
            $string = file_get_contents($path);
            $this->data = json_decode($string, true);
        } else {
            Webpage::show404("'{$id}' is not a game!");
        }
    }

    public function getPathResources() {
        return "/resources/games/".$this->id."/";
    }

    public function getId(){
        return $this->id;
    }

    public function getName(){
        return $this->data["name"];
    }

    public function getDescription(){
        return $this->data["description"];
    }

    public function getUrl(){
        return Webpage::upgradeUrl("/games/$this->id");
    }

    public function getBackgroundColors(): array {
        $hsl1 = [$this->data["main-color"], 0.59, 0.73];
        $rgb1 = Colours::hslToRgb($hsl1);
        $hex1 = Colours::RgbtoHex($rgb1);
        $hsl2 = [$this->data["main-color"], 0.47, 0.68];
        $rgb2 = Colours::hslToRgb($hsl2);
        $hex2 = Colours::RgbtoHex($rgb2);
        return [$hex1, $hex2];
    }

    public function getMainColor(): string
    {
        $hsl = [$this->data["main-color"], 0.41, 0.53];
        $rgb = Colours::hslToRgb($hsl);
        $hex = Colours::RgbtoHex($rgb);
        return $hex;
    }

    public function getSecondaryColor(){
        $hsl = [$this->data["main-color"] + 25 % 360, 0.75, 0.88];
        $rgb = Colours::hslToRgb($hsl);
        $hex = Colours::RgbtoHex($rgb);
        return $hex;
    }

    public function getAccentColor() {
        $hsl = [$this->data["main-color"] + 205 % 360, 0.4, 0.7];
        $rgb = Colours::hslToRgb($hsl);
        $hex = Colours::RgbtoHex($rgb);
        return $hex;
    }

    public function getToolIds(): array {
        $db = new SQLite3($_SERVER['DOCUMENT_ROOT'] . '/resources/tools/tools.sqlite');
        $sql = "SELECT id FROM Tool WHERE game='$this->id' AND released==1";
        $ret = $db->query($sql);
        $toolIds = [];
        while($row = $ret->fetchArray(SQLITE3_ASSOC) ){
            array_push($toolIds, $row["id"]);
        };
        return $toolIds;
    }

    public function getPreviewImage() {
        $path = $this->getPathResources() . "background/";
        $backgrounds = scandir($_SERVER['DOCUMENT_ROOT'] . $path);
        $backgrounds = array_values(array_filter($backgrounds, array("Game", "validFileName")));
        $url = $path . $backgrounds[array_rand($backgrounds)];
        return $url;
    }

    public function isVisible() {
        $db = new SQLite3($_SERVER['DOCUMENT_ROOT'] . '/resources/tools/tools.sqlite');
        $sql = "SELECT COUNT(*) AS amount FROM Tool WHERE released==1 AND game='{$this->id}'";
        $ret = $db->query($sql);
        $row = $ret->fetchArray(SQLITE3_ASSOC);
        $amount = $row["amount"];
        $db->close();
        return $amount > 0;
    }

    private static function validFileName(string $name): bool
    {
        return !($name === "." || $name === "..");
    }
}

// Testing
if (basename(__FILE__) == basename($_SERVER["SCRIPT_FILENAME"])) {
    $test = new Game("minecraft");
    echo $test->getName();
}
