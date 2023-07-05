<?php
include_once($_SERVER['DOCUMENT_ROOT'] . "/library/imageHost/imageHost.php");
include_once($_SERVER['DOCUMENT_ROOT'] . "/library/template/webpage/webpage.php");
include_once($_SERVER['DOCUMENT_ROOT'] . "/module/module.php");

class Information extends Module
{
    private array $cells_types;

    function __construct(Webpage $webpage)
    {
        $webpage->addStylesheet("/module/information/stylesheet.css");
        $this->cells_types = [[], [], []];
    }

    function addIntroduction(string $title, string $content)
    {
        $cell = new Cell("Introduction", $title, $content);
        $cell->setImportant();
        $cell->setIcon("/module/information/icons/info.svg");
        array_push($this->cells_types[0], $cell);
    }

    function addQuestions(array $questions)
    {
        foreach ($questions as $question) {
            $cell = new Cell("Question", $question["title"], $question["text"]);
            array_push($this->cells_types[1], $cell);
        }
    }

    function addExamples(array $examples)
    {
        foreach ($examples as $example) {
            $cell = new Cell("Example", $example["title"], $example["text"]);
            $cell->setIcon("/module/information/icons/star.svg");
            array_push($this->cells_types[2], $cell);
        }
    }

    function place()
    {
        $cells = $this->getAllCells();
        echo "<div class='information'>";
        foreach ($cells as $cell) {
            $cell->place();
        }
        echo "</div>";
    }

    private function getAllCells(): array
    {
        $cell_scores = [];
        foreach ($this->cells_types as $key => $value) {
            $count = count($value);
            foreach ($value as $i => $cell) {
                array_push($cell_scores, ["score" => $i / $count, "cell" => $cell]);
            }
        }

        usort($cell_scores, fn ($a, $b) => strcmp($a["score"], $b["score"]));

        $cells = [];
        foreach ($cell_scores as $key => $value) {
            array_push($cells, $value["cell"]);
        }

        return $cells;
    }
}

class Cell
{
    private string $type;
    private string $title;
    private string $content;
    private string $icon;
    private array $classes;

    function __construct(string $type, string $title, string $content)
    {
        $this->type = $type;
        $this->title = $title;
        $this->content = $content;
        $this->icon = $this->getIcon();
        $this->classes = [strtolower($type)];
    }

    function setImportant()
    {
        array_push($this->classes, "important");
    }

    function setIcon(string $icon)
    {
        $this->icon = $icon;
    }

    function place()
    {
        $classas_string = join(" ", $this->classes);
        $html = "
        <div class='cell $classas_string'>
            <p class='tag'>$this->type</p>
            <div class='header'>
                <img class='icon' alt='icon' src='$this->icon'>
                <h3 class='title'>$this->title</h3>
            </div>
            <p class='content'>$this->content</p>
        </div>";
        echo $html;
    }

    private function getIcon(): string
    {
        $pathResources = "/module/information/icons/";
        $text = strtolower($this->title);
        $first_word = strtok($text, " ");
        if ($this->str_includes("bug", $text)) {
            $file = "bug.svg";
        } else if ($first_word == "how") {
            $file = "how.png";
        } else if ($first_word == "what") {
            $file = "what.png";
        } else if ($first_word == "why") {
            $file = "why.png";
        } else if ($first_word == "did") {
            $file = "did.png";
        } else if ($first_word == "do") {
            $file = "do.png";
        } else if ($first_word == "?") {
            $file = "other.png";
        } else {
            $file = "";
        }
        $url = $pathResources . $file;
        $url = ImageHost::getBetterUrl($url);
        return $url;
    }

    private function str_includes($needle, $haystack)
    {
        return strpos($haystack, $needle) !== false;
    }
}


if (basename(__FILE__) == basename($_SERVER["SCRIPT_FILENAME"])) {
    $webpage = new Webpage();
    $webpage->startContent();
    $information = new information($webpage);
    $information->addIntroduction("test", "This is a test");
    $information->place();
    $webpage->endContent();
    $webpage->generateWebpage();
}
