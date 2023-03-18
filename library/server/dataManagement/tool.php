<?php
include_once($_SERVER['DOCUMENT_ROOT'] . "/library/server/dataManagement/game.php");
include_once($_SERVER['DOCUMENT_ROOT'] . "/library/server/dataManagement/application.php");
include_once($_SERVER['DOCUMENT_ROOT'] . "/library/server/template/webpage/webpage.php");

class Tool
{
    private Game $game;
    private Application $application;
    private int $id;
    private string $subject;

    static function getAllToolIds()
    {
        $ids = [];
        $db = new SQLite3($_SERVER['DOCUMENT_ROOT'] . '/resources/tools/tools.sqlite');
        $sql = "SELECT id FROM Tool";
        $ret = $db->query($sql);
        while ($row = $ret->fetchArray(SQLITE3_ASSOC)) {
            array_push($ids, $row["id"]);
        }
        $db->close();
        return $ids;
    }

    function __construct(int $id)
    {
        $db = new SQLite3($_SERVER['DOCUMENT_ROOT'] . '/resources/tools/tools.sqlite');
        $sql = "SELECT * FROM Tool WHERE id=$id";
        $ret = $db->query($sql);
        $row = $ret->fetchArray(SQLITE3_ASSOC);
        if ($row == false) {
            Webpage::show404("Tool doesn't exists");
        }
        $this->id = $row["id"];
        $this->released = ($row["released"] == 1) ? true : false;
        $this->game = new Game($row["game"]);
        $this->application = new Application($row["application"]);
        $this->subject = $row["subject"];
        $db->close();
    }

    public function getId()
    {
        return $this->id;
    }

    public function getGame(): Game
    {
        return $this->game;
    }

    public function getApplication(): Application
    {
        return $this->application;
    }

    public function getSubject(): String
    {
        return $this->subject;
    }

    public function getName(): string
    {
        if ($this->getApplication()->getVersion() <= 1) {
            return $this->getGame()->getName() . " " . $this->getApplication()->getName();
        } else {
            return $this->replacePlaceholders($this->getApplication()->getToolData()["name"], true);
        }
    }

    public function getDescription(): string
    {
        if ($this->getApplication()->getVersion() <= 1) {
            return $this->getApplication()->getDescription();
        } else {
            return $this->replacePlaceholders($this->getApplication()->getToolData()["description"]);
        }
    }

    public function getKeyWords(): array
    {
        if ($this->getApplication()->getVersion() <= 1) {
            return $this->getApplication()->getKeyWords();
        } else {
            $keywords = [];
            foreach ($this->getApplication()->getToolData()["keywords"] as $keyword) {
                array_push($keywords, $this->replacePlaceholders($keyword));
            }
            return $keywords;
        }
    }

    public function getUrl(): string
    {
        return Webpage::upgradeUrl("/tools/$this->id");
    }

    /**
     * @deprecated 
     * */
    public function getDataUrls(): array
    {
        $data = [];
        foreach ($this->application->getDataLocations() as $location) {
            $path = "/resources/games/{$this->game->getId()}/" . $location;
            $name = explode(".", $location)[0];
            $data[$name] = $path;
        }
        return $data;
    }

    /**
     * @deprecated 
     * */
    public function getData(): array
    {
        $data = [];
        foreach ($this->getDataUrls() as $name => $location) {
            $string = file_get_contents($_SERVER['DOCUMENT_ROOT'] . $location);
            $data[$name] = json_decode($string, true);
        }

        if (count($data) == 1) {
            $data = $data[array_key_first($data)];
        }

        return $data;
    }

    function getIntroduction(): string {
        if ($this->getApplication()->getVersion() <= 3){
            return "";
        } else {
            return $this->replacePlaceholders($this->getApplication()->getToolData()["introduction"]);
        }
    }

    function getQuestions(): array
    {
        $faq = [];

        if ($this->getApplication()->getVersion() <= 1) {
            foreach ($this->getApplication()->getExplanation() as $question) {
                array_push(
                    $faq,
                    [
                        "title" => $this->replacePlaceholders($question["title"]),
                        "text" => $this->replacePlaceholders($question["text"])
                    ]
                );
            }
        } else if ($this->getApplication()->getVersion() <= 3){
            foreach ($this->getApplication()->getToolData()["explanation"] as $question) {
                array_push(
                    $faq,
                    [
                        "title" => $this->replacePlaceholders($question["title"]),
                        "text" => $this->replacePlaceholders($question["text"])
                    ]
                );
            }
        } else {
            foreach ($this->getApplication()->getToolData()["questions"] as $question) {
                array_push(
                    $faq,
                    [
                        "title" => $this->replacePlaceholders($question["title"]),
                        "text" => $this->replacePlaceholders($question["text"])
                    ]
                );
            }
        }

        return $faq;
    }

    function getExamples(): array
    {
        if ($this->application->getVersion() < 3) {
            return [];
        } else {
            $examples = [];
            foreach ($this->getApplication()->getToolData()["examples"] as $example) {
                array_push(
                    $examples,
                    [
                        "title" => $this->replacePlaceholders($example["title"]),
                        "text" => $this->replacePlaceholders($example["text"])
                    ]
                );
            }
            return $examples;
        }
    }

    private function replacePlaceholders(string $text, $special = false): string
    {
        $placeholders = $this->getPlaceholders($special);
        foreach ($placeholders as $search => $replace) {
            $text = str_replace($search, $replace, $text);
        }
        return $text;
    }

    private function getPlaceholders($special = false): array
    {
        if ($this->getApplication()->getVersion() < 3) {
            $gameName = $this->getGame()->getName();

            $placeholders = [
                "{game}" => $gameName,
            ];

            if (!$special) {
                $placeholders = array_merge($placeholders, ["{name}" => $this->getName()]);
            }
        } else {
            $placeholders = [
                "{game_name}" => $this->getGame()->getName(),
                "{application_name}" => $this->getApplication()->getName()
            ];

            if (!$special) {
                $placeholders = array_merge($placeholders, ["{tool_name}" => $this->getName()]);
            }
        }

        // Add game specific words
        if ($this->getApplication()->getVersion() <= 1) {
            $gameSpecificWords = $this->getApplication()->getGameSpecificWords();
            $gameId = $this->getGame()->getId();
            if (isset($gameSpecificWords)) {
                if (array_key_exists($gameId, $gameSpecificWords)) {
                    $gameSpecificWords = $gameSpecificWords[$gameId];
                } else {
                    $gameSpecificWords = $gameSpecificWords["normal"];
                }
            } else {
                $gameSpecificWords = [""];
            }
            foreach ($gameSpecificWords as $number => $gameSpecificWord) {
                $placeholders["gameSpecificWords[$number]"] = $gameSpecificWord;
                $placeholders["GameSpecificWords[$number]"] = ucfirst($gameSpecificWord);
            }
        } else if ($this->getApplication()->getVersion() < 3) {
            $dataManagements = $this->getApplication()->getToolData()["game_specific_words"];
            foreach ($dataManagements as $dataManagement) {
                include_once($_SERVER['DOCUMENT_ROOT'] . "/library/server/dataManagement/games/" . $dataManagement . ".php");
                $class = new $dataManagement($this);
                $placeholders = array_merge($placeholders, $class->getGameSpecificWords());
            }
        } else {
            $dataManagements = $this->getApplication()->getToolData()["game_specific_words"];
            foreach ($dataManagements as $dataManagement) {
                include_once($_SERVER['DOCUMENT_ROOT'] . "/library/server/dataManagement/games/" . $dataManagement . ".php");
                $class = new $dataManagement($this);
                $gameSpecificWords = $class->getGameSpecificWords();
                foreach ($gameSpecificWords as $key=>$value){
                    $placeholders["{".$dataManagement.".".$key."}"] = $value;
                }
            }
        }

        return $placeholders;
    }

    public function isVisible(): bool
    {
        return $this->released;
    }
}
