<?php
include_once($_SERVER['DOCUMENT_ROOT'] . "/library/imageHost/imageHost.php");
include_once($_SERVER['DOCUMENT_ROOT'] . "/library/template/webpage/webpage.php");

class Application
{
    private string $id;
    private array $data;
    private int $version;

    static function getAllApplicationIds()
    {
        $ids = scandir($_SERVER['DOCUMENT_ROOT'] . "/public/applications/");
        $ids = array_values(array_filter($ids, array("Application", "validFileName")));
        return $ids;
    }

    private static function validFileName(string $name): bool
    {
        return !($name === "." || $name === ".." || $name === "_template" || $name === "router.php");
    }

    public function __construct(string $id)
    {
        $this->id = $id;
        $path = $_SERVER['DOCUMENT_ROOT'] . $this->getPathResources();
        if (file_exists($path)) {
            $string = file_get_contents($path . "/toolInformation.json");
            $this->data = json_decode($string, true);
            $this->version = $this->data["version"];
        } else {
            trigger_error("'{$id}' is not an application!", E_USER_ERROR);
        }
    }

    public function getId()
    {
        return $this->id;
    }

    public function getVersion()
    {
        return $this->version;
    }

    private function getPathResources()
    {
        return "/public/applications/" . $this->id;
    }

    public function getToolData()
    {
        return $this->data["tool"];
    }

    /**
     * @deprecated 
     * */
    public function getDataLocations()
    {
        return $this->data["data"];
    }

    public function getName(): string
    {
        if ($this->version <= 1) {
            return $this->data["name"];
        } else {
            return $this->data["application"]["name"];
        }
    }

    public function getDescription(): string
    {
        if ($this->version <= 1) {
            return $this->data["description"];
        } else {
            return $this->data["application"]["description"];
        }
    }

    /**
     * @deprecated 
     * */
    public function getExplanation(): array
    {
        return $this->data["explanation"];
    }

    /**
     * @deprecated 
     * */
    public function getGameSpecificWords(): array
    {
        return $this->data["gameSpecificWords"];
    }

    public function getIcon(): string
    {
        $path_svg = $this->getPathResources() . "/icon.svg";
        $path_png = $this->getPathResources() . "/icon.png";
        $path_jpg = $this->getPathResources() . "/icon.jpg";
        if (file_exists($_SERVER['DOCUMENT_ROOT'] . $path_svg)) {
            $url = $path_svg;
        } else if (file_exists($_SERVER['DOCUMENT_ROOT'] . $path_png)) {
            $url = $path_png;
        } else if (file_exists($_SERVER['DOCUMENT_ROOT'] . $path_jpg)) {
            $url = $path_jpg;
        } else {
            $url = "/resources/applications/default/calculator_icon.png";
        }
        $url = ImageHost::getBetterUrl($url);
        return $url;
    }

    public function isVisible()
    {
        $db = new SQLite3($_SERVER['DOCUMENT_ROOT'] . '/resources/tools/tools.sqlite');
        $sql = "SELECT COUNT(*) AS amount FROM Tool WHERE released==1 AND application='{$this->id}'";
        $ret = $db->query($sql);
        $row = $ret->fetchArray(SQLITE3_ASSOC);
        $amount = $row["amount"];
        $db->close();
        return $amount > 0;
    }

    public function getKeyWords()
    {
        if ($this->version <= 1) {
            return $this->data["keywords"];
        } else {
            return $this->data["application"]["keywords"];
        }
    }

    public function getStylesheetUrl()
    {
        return "/public/applications/{$this->id}/stylesheet.css";
    }

    public function getJavascriptUrl()
    {
        return "/public/applications/{$this->id}/javascript.js";
    }

    public function getPhpUrl()
    {
        return "/public/applications/{$this->id}/tool.php";
    }

    public function getUrl()
    {
        return Webpage::upgradeUrl("/applications/" . $this->id);
    }
}
