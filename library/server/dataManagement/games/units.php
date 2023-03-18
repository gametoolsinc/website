<?php
include_once($_SERVER['DOCUMENT_ROOT'] . "/library/server/dataManagement/games/gameDataManagement.php");

class Units extends GameDataManagement
{
    private Tool $tool;
    private array $units;
    private array $unitCategories;

    function __construct(Tool $tool, String $unitType = null)
    {
        $this->tool = $tool;
        if ($unitType == null){
            $unitType = $tool->getSubject();
        }
        $data = $this->openFile($this->tool->getGame()->getPathResources() . "unit_" . $unitType . ".json");
        $this->units = [];
        foreach ($data as $key => $value) {
            $unit = new Unit($key, $value);
            array_push($this->units, $unit);
        }

        $this->unitCategories = [];
        foreach ($this->getUnits() as $unit) {
            foreach ($unit->getCategories() as $categorie) {
                if (!array_key_exists($categorie, $this->unitCategories)) {
                    $this->unitCategories[$categorie] = new UnitCategorie($categorie);
                }
                $this->unitCategories[$categorie]->addUnit($unit);
            }
            if ($unit->getCategories() == []) {
                $unitCategorie = new UnitCategorie($unit->getName());
                $unitCategorie->addUnit($unit);
                foreach ($this->getUnits() as $checkUnit) {
                    if ($checkUnit->isImportant() && $checkUnit->getSize() < $unit->getSize()) {
                        $unitCategorie->addUnit($checkUnit);
                    }
                }
                $this->unitCategories[$unit->getName()] = $unitCategorie;
            }
        }
    }

    function getGameSpecificWords()
    {
        $firstUnit = $this->getRightDefaultUnit()->getUnits()[0];
        $secondUnit = $this->getLeftDefaultUnit()->getUnits()[0];
        return [
            "first_unit_name" => $firstUnit->getName(),
            "first_unit_size" => $firstUnit->getSize(),
            "second_unit_name" => $secondUnit->getName(),
        ];
    }

    public function getUnitByName(String $name): Unit
    {
        foreach ($this->units as $unit) {
            if ($unit->getName() == $name) {
                return $unit;
            }
        }
        throw new Exception('Unit with name ' . $name . ' not found.');
    }

    public function getUnitCategorieByName(String $name): UnitCategorie
    {
        foreach ($this->unitCategories as $unitCategorie) {
            if ($unitCategorie->getName() == $name) {
                return $unitCategorie;
            }
        }
        throw new Exception('Unit categorie with name ' . $name . ' not found.');
    }

    public function getLeftDefaultUnit(): UnitCategorie
    {
        foreach ($this->getUnitCategories() as $unitCategorie) {
            if ($unitCategorie->isStarting()) {
                return $unitCategorie;
            }
        }
        throw new Exception('Didn\' find first starting unit categorie');
    }

    public function getRightDefaultUnit(): UnitCategorie
    {
        $found_one = false;
        foreach ($this->getUnitCategories() as $unitCategorie) {
            if ($unitCategorie->isStarting()) {
                if ($found_one) {
                    return $unitCategorie;
                }
                $found_one = true;
            }
        }
        throw new Exception('Didn\' find second starting unit categorie');
    }

    /**
     * @return Unit[]
     */
    public function getUnits(): array
    {
        return $this->units;
    }

    /**
     * @return UnitCategorie[]
     */
    public function getUnitCategories(): array
    {
        return $this->unitCategories;
    }
}

class Unit
{
    private string $name;
    private array $categories;
    private int $size;
    private bool $important;
    private bool $start;

    function __construct(String $name, array $data)
    {
        $this->name = $name;
        $this->size = $data["size"];
        if (array_key_exists("important", $data)) {
            $this->important = $data["important"];
        } else {
            $this->important = false;
        }
        if (array_key_exists("start", $data)) {
            $this->start = $data["start"];
        } else {
            $this->start = false;
        }
        if (array_key_exists("categories", $data)) {
            $this->categories = $data["categories"];
        } else {
            $this->categories = [];
        }
    }

    public function getName()
    {
        return $this->name;
    }

    public function getSize()
    {
        return $this->size;
    }

    public function getCategories()
    {
        return $this->categories;
    }

    public function isImportant()
    {
        return $this->important;
    }

    public function isStarting(){
        return $this->start;
    }
}

class UnitCategorie
{
    private string $name;
    private array $units;

    function __construct(string $name)
    {
        $this->name = $name;
        $this->units = [];
    }

    function addUnit(Unit $unit)
    {
        array_push($this->units, $unit);
        usort($this->units, fn($a, $b) => $b->getSize() - $a->getSize());
    }

    function getName(): string
    {
        return $this->name;
    }

    function isStarting(): bool
    {
        return $this->units[0]->isStarting();
    }

    /**
     * @return Unit[]
     */
    function getUnits(): array
    {
        return $this->units;
    }
}
