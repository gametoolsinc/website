<?php
include_once($_SERVER['DOCUMENT_ROOT'] . "/library/server/template/webpage/webpage.php");
include_once($_SERVER['DOCUMENT_ROOT'] . "/module/module.php");

class Explanation extends Module
{
    private array $cell_types;

    function __construct(Webpage $webpage)
    {
        $webpage->addStylesheet("/module/explanation/stylesheet.css");
        $this->cell_types = [];
    }

    function addIntroduction(string $title, string $content)
    {
        $cells = [];
        $cell = new Cell("Introduction", $title, $content);
        $cell->setImportant();
        $cell->setIcon("/module/explanation/icons/info.svg");
        array_push($cells, $cell);
        $this->cell_types[0] = $cells;
    }

    function addExamples(array $examples)
    {
        $cells = [];
        foreach ($examples as $example) {
            $cell = new Cell("Example", $example["title"], $example["text"]);
            array_push($cells, $cell);
            $cell->setIcon("/module/explanation/icons/star.svg");
    }
        $this->cell_types[2] = $cells;
    }

    function addQuestions(array $questions)
    {
        $cells = [];
        foreach ($questions as $question) {
            $cell = new Cell("Question", $question["title"], $question["text"]);
            array_push($cells, $cell);
        }
        $this->cell_types[1] = $cells;
    }

    function place()
    {
        ksort($this->cell_types);

        echo "<div class='explanation'>";
        foreach ($this->cell_types as $cells) {
            foreach ($cells as $cell) {
                $cell->place();
            }
        }
        echo "</div>";
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

    function setIcon(string $icon){
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
        $pathResources = "/module/explanation/icons/";
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
        return $pathResources . $file;
    }

    private function str_includes($needle, $haystack)
    {
        return strpos($haystack, $needle) !== false;
    }
}


if (basename(__FILE__) == basename($_SERVER["SCRIPT_FILENAME"])) {
    $webpage = new Webpage();
    $webpage->startContent();
    $explanation = new Explanation($webpage);
    $explanation->addIntroduction("test", "This is a test");
    $explanation->place();
    $webpage->endContent();
    $webpage->generateWebpage();
}
