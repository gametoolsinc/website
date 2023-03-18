<?php
include_once($_SERVER['DOCUMENT_ROOT'] . "/module/module.php");

abstract class LinkType
{
    const game = 0;
    const application = 1;
    const tool = 2;
    const article = 3;
}

class LinkGrid extends Module {
    private array $links;
    private int $amountOfRows;

    private string $listTemplate = "
    <div class='big_list' style='{amountOfRows}'>  
        {content}
    </div>
    ";

    function __construct(Webpage $webpage) {
        $this->amountOfRows = -1;
        $webpage->addStylesheet("/module/link/stylesheet.css");
        $this->links = [];
    }

    function setAmountOfRows($amount){
        $this->amountOfRows = $amount;
    }

    function place(){
        $content = "";
        foreach ($this->links as $link) {
            $content .= $link->getHTML();
        }

        $template = str_replace("{content}", $content, $this->listTemplate);
        if ($this->amountOfRows == -1){
            $template = str_replace("{amountOfRows}", "grid-auto-flow: row;grid-template-columns: repeat(auto-fill, minmax(min(max(300px, 26%),70%), 1fr));", $template);
        } else {
            $template = str_replace("{amountOfRows}", "grid-template-rows: repeat({$this->amountOfRows}, auto);", $template);
        }
        echo $template;
    }

    public function newLink($type, $name, $text, $image, $link) {
        $link = new Link($type, $name, $text, $image, $link, );
        array_push($this->links, $link);
    }
}

class Link {
    private string $type;
    private string $name;
    private string $text;
    private string $link;
    private array $images;
    
    function __construct($type, $name, $text, $image, $link)
    {
        $this->name = $name;
        $this->text = $text;
        $this->link = $link;
        if (is_array($image)){
            $this->images = $image;
        } else {
            $this->images = [$image];
        }

        if ($type == LinkType::game) {
            $this->type = "stadia_controller";
        } else if ($type == LinkType::application) {
            $this->type = "calculate";
        } else if ($type == LinkType::tool) {
            $this->type = "build";
        } else if ($type == LinkType::article) {
            $this->type = "feed";
        } else {
            $this->type = "";
        }
    }
    
    public function getHTML(){
        $html = "<a href='{$this->link}'><div class='link_images'>";
        foreach ($this->images as $image){
            $html .= "<img loading='lazy' src='{$image}' alt='Banner {$this->name}'>";
        }
        $html .= "
            </div>
            <span class='material-symbols-outlined'>{$this->type}</span>
            <div class='gradient'>                    
                <h2 class=title>{$this->name}</h2>
                <p class=description>{$this->text}</p>
            </div>
        </a>
        ";

        return $html;
    }
}