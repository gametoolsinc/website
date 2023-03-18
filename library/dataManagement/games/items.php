<?php
include_once($_SERVER['DOCUMENT_ROOT'] . "/library/dataManagement/games/gameDataManagement.php");

class Items extends GameDataManagement
{
    private array $items;
    private array $obtainingMethodes;
    private array $icons;
    private array $versions;
    private string $spritePath;

    function __construct(Tool $tool)
    {
        $this->versions = [];

        $this->spritePath = $tool->getGame()->getPathResources() . "/icons.png";
        $data = $this->openFile($tool->getGame()->getPathResources() . "items.json");

        $this->items = [];
        foreach ($data["items"] as $key => $value) {
            $item = new Item($key, $value);
            array_push($this->items, $item);
        }

        $this->obtainingMethodes = [];
        $obtainingMethode = new ObtainingMethode("general", $this->openFile("/library/dataManagement/games/itemsDefaultObtainingMethode.json"));
        array_push($this->obtainingMethodes, $obtainingMethode);
        foreach ($data["obtaining"] as $key => $value) {
            $obtainingMethode = new ObtainingMethode($key, $value);
            array_push($this->obtainingMethodes, $obtainingMethode);
        }

        $this->icons = $data["icons"];
    }

    function getGameSpecificWords()
    {
        $items = $this->getItems();
        usort($items, function($a, $b)
        {
            return strcmp($b->getValue(), $a->getValue());
        });

        $diffecult_item = $items[0];
        $amount = count($items);
        $medium_item = $items[floor($amount / 2)];
        return [
            "difficult_item_name" => $diffecult_item->getName(),
            "difficult_item_id" => $diffecult_item->getId(),
            "medium_item_name" => $medium_item->getName(),
            "medium_item_id" => $medium_item->getId(),
            "amount" => count($items)
        ];
    }

    function setVersions(array $versions)
    {
        $this->versions = $versions;
    }

    /**
     * @return Item[]
     */
    function getItems(): array
    {
        $items = [];
        foreach ($this->items as $item) {
            if (count($item->getVersion()) == 0 || count(array_intersect($item->getVersion(), $this->versions)) > 0) {
                array_push($items, $item);
            }
        }
        return $items;
    }

    /**
     * @return ObtainingMethode[]
     */
    function getObtainingMethodes(): array
    {
        return $this->obtainingMethodes;
    }

    function getIcons(): array
    {
        return $this->icons;
    }

    function placeIconsCss(): void
    {
        // information icons
        $amount = $this->icons["amountOnRow"];
        $rendering = $this->icons["render"];

        echo "
            .gameItem{
                background-image: url({$this->spritePath}); 
                background-size: calc(100% * {$amount});
                overflow: hidden;
                image-rendering: {$rendering};
            }";
    }
}

class Item
{
    private string $id;
    private array $data;
    private array $obtaining;

    function __construct(string $id, array $data)
    {
        $this->id = $id;
        $this->data = $data;

        $this->obtaining = [];
        foreach ($this->data["obtaining"] as $value) {
            $obtaining = new Obtaining($value);
            array_push($this->obtaining, $obtaining);
        }
    }

    function getId(): string
    {
        return $this->id;
    }

    function getName(): string
    {
        return $this->data["name"];
    }

    function getLocationImage(): string
    {
        return $this->data["location image"];
    }

    function getCategories(): array
    {
        return $this->data["categories"];
    }

    function getValue(): float
    {
        return $this->data["value"];
    }

    /**
     * @return Obtaining[]
     */
    function getObtaining(): array
    {
        return $this->obtaining;
    }

    function getVersion(): array
    {
        if (array_key_exists("version", $this->data)){
            return $this->data["version"];
        } else {
            return [];
        }
    }
}

class Obtaining
{
    private array $data;

    function __construct(array $data)
    {
        $this->data = $data;
    }

    function getType(): string
    {
        return $this->data["type"];
    }

    function getInput(): array
    {
        return $this->data["input"];
    }

    function getOption(): array
    {
        return $this->data["option"];
    }

    function getOutput(): array
    {
        return $this->data["output"];
    }
}

class ObtainingMethode
{
    private string $id;
    private array $data;

    function __construct(string $id, array $data)
    {
        if (strpos($id, ' ') !== false) {
            throw new Exception('Id can not containg spaces!');
        }
        $this->id = $id;
        $this->data = $data;
    }

    function getId(): string
    {
        return $this->id;
    }

    function getName(): string
    {
        return $this->data["methode"];
    }

    function getText(): string
    {
        return $this->data["text"];
    }

    function getImage(): string
    {
        return $this->data["image"];
    }

    function getInput(): array
    {
        if (array_key_exists("input", $this->data)) {
            return $this->data["input"];
        } else {
            return [];
        }
    }

    function getOption(): array
    {
        if (array_key_exists("option", $this->data)) {
            return $this->data["option"];
        } else {
            return [];
        }
    }

    function getOuput(): array
    {
        return $this->data["output"];
    }
}
