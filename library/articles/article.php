<?php
include_once($_SERVER['DOCUMENT_ROOT'] . "/library/articles/articles.php");
include_once($_SERVER['DOCUMENT_ROOT'] . "/library/template/webpage/webpage.php");

class Article
{
    private int $id;
    private string $title;
    private array $articleParts;

    function __construct(int $id)
    {
        $this->id = $id;
        $sql = "SELECT title FROM article WHERE id={$id}";
        $result = Articles::querySql($sql);
        $items = [];
        while ($row = $result->fetchArray(SQLITE3_ASSOC)) {
            array_push($items, $row);
        }

        if (count($items) != 1) {
            trigger_error("'{$id}' id not found!", E_USER_WARNING);
        }

        $item = $items[0];
        $this->title = $item["title"];

        // Get content of article
        $sql = "SELECT position, type, content FROM article_part WHERE article_id={$id}";
        $result = Articles::querySql($sql);
        $content["content"] = [];
        $this->articleParts = [];
        while ($row = $result->fetchArray(SQLITE3_ASSOC)) {
            if (ctype_digit($row["type"])) {
                $type = $row["type"];
            } else {
                $type = constant('ArticlePartTypes::' . $row["type"]); // Old system
            }
            $this->articleParts[$row["position"]] = new ArticlePart($row["position"], $type, $row["content"]);
        }
    }

    function getId(): int
    {
        return $this->id;
    }

    function getTitle(): string
    {
        return $this->title;
    }

    function getUrl(): string
    {
        return Webpage::upgradeUrl("/articles/{$this->getId()}");
    }

    function getImage(): string
    {
        return ImageHost::getBetterUrl("/resources/articles/article.png");
    }

    function getIntroduction(): string
    {
        $introduction = "";
        foreach ($this->articleParts as $articelPart) {
            if ($articelPart->getType() == ArticlePartTypes::text) {
                $introduction = $articelPart->getContent();
                break;
            }
        }
        $short_introduction = tokenTruncate($introduction, 150);
        if ($short_introduction != $introduction) {
            $short_introduction .= "...";
        }
        return $short_introduction;
    }

    /**
     * @return ArticlePart[]
     */
    function getArticleParts(): array
    {
        return $this->articleParts;
    }

    function placeArticleParts()
    {
        foreach ($this->articleParts as $articelPart) {
            $articelPart->place();
        }
    }

    function getAmountOfWords(): int
    {
        $total = 0;
        foreach ($this->articleParts as $articelPart) {
            $total += $articelPart->getAmountOfWords();
        }
        return $total;
    }
}

abstract class ArticlePartTypes
{
    const text = 0;
    const title = 1;
    const image = 2;
}

class ArticlePart
{
    private int $type;
    private string $content;
    private int $position;

    function __construct(int $position, int $type, string $content)
    {
        $this->position = $position;
        $this->type = $type;
        $this->content = $content;
    }

    function getType(): int
    {
        return $this->type;
    }

    function getContent(): string
    {
        return $this->content;
    }

    function getPosition(): int
    {
        return $this->position;
    }

    function place()
    {
        if ($this->type == ArticlePartTypes::title) {
            echo "<h2>{$this->content}</h2>";
        } else if ($this->type == ArticlePartTypes::text) {
            echo "<p>{$this->content}</p>";
        } else if ($this->type == ArticlePartTypes::image) {
            echo "<img src={$this->content}></img>";
        } else {
            trigger_error("'{$this->type}' type is not defined!", E_USER_WARNING);
        }
    }

    function getAmountOfWords(): int
    {
        $total = 0;
        if ($this->type == ArticlePartTypes::text) {
            $total += str_word_count($this->content);
        }
        return $total;
    }
}

function tokenTruncate($string, $your_desired_width)
{
    $parts = preg_split('/([\s\n\r]+)/u', $string, -1, PREG_SPLIT_DELIM_CAPTURE);
    $parts_count = count($parts);

    $length = 0;
    $last_part = 0;
    for (; $last_part < $parts_count; ++$last_part) {
        $length += strlen($parts[$last_part]);
        if ($length > $your_desired_width) {
            break;
        }
    }

    return implode(array_slice($parts, 0, $last_part));
}
