<?php
include_once($_SERVER['DOCUMENT_ROOT'] . "/library/dataManagement/tool.php");
include_once($_SERVER['DOCUMENT_ROOT'] . "/library/template/webpage/webpage.php");
include_once($_SERVER['DOCUMENT_ROOT'] . "/module/link/link.php");
include_once($_SERVER['DOCUMENT_ROOT'] . "/module/information/information.php");


class ToolWebpage
{
    private Tool $tool;
    private Webpage $webpage;

    function __construct(int $tool_id)
    {
        $this->tool = new Tool($tool_id);
        $this->webpage = new Webpage($this->tool->getGame()->getBackgroundColors());
        $this->webpage->setTitle($this->tool->getName());
        $this->webpage->setDescription($this->tool->getDescription());
        $this->webpage->setKeywords($this->tool->getKeyWords());
        $this->webpage->addMetaTag('tool-id', $this->tool->getId());
        $this->webpage->addMetaTag('game-id', $this->tool->getGame()->getId());
        $this->webpage->addMetaTag('application-id', $this->tool->getApplication()->getId());
        $this->webpage->addMetaTag('theme-color', $this->tool->getGame()->getMainColor());

        // get stylesheets for template, tool and tooltip
        $this->webpage->addStylesheet("/library/template/tool/stylesheet.css");
        $this->webpage->addStylesheet($this->tool->getApplication()->getStylesheetUrl());
        $this->webpage->addStylesheet("/module/tooltip/tooltip.css");

        // Javascript
        $this->webpage->addJavascript("https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js");
        $this->webpage->addJavascript($this->tool->getApplication()->getJavascriptUrl());
        $this->webpage->addJavascript("/module/tooltip/tooltip.js");

        // Colours
        $this->webpage->addStyle("
        :root {
            --main: {$this->tool->getGame()->getMainColor()};
            --secondary: {$this->tool->getGame()->getSecondaryColor()};
            --accent: {$this->tool->getGame()->getAccentColor()};
        }");

        // Schema.org
        $json = [
            "@context" => "https://schema.org",
            "@type" => "FAQPage",
            "mainEntity" => []
        ];
        foreach ($this->tool->getQuestions() as $question) {
            $jsonQuestion = [
                "@type" => "Question",
                "name" => $question["title"],
                "acceptedAnswer" => [
                    "@type" => "Answer",
                    "text" => "<p>" . $question["text"] . "</p>"
                ]
            ];
            array_push($json["mainEntity"], $jsonQuestion);
        }
        foreach ($this->tool->getExamples() as $example) {
            $jsonQuestion = [
                "@type" => "Question",
                "name" => $example["title"],
                "acceptedAnswer" => [
                    "@type" => "Answer",
                    "text" => "<p>" . $example["text"] . "</p>"
                ]
            ];
            array_push($json["mainEntity"], $jsonQuestion);
        }
        $this->webpage->addSchemaData($json);
    }

    function getTool()
    {
        return $this->tool;
    }

    function getWebpage()
    {
        return $this->webpage;
    }

    function startContent()
    {
        $this->webpage->startContent();
        $gameName = $this->tool->getGame()->getName();
        $gameUrl = $this->tool->getGame()->getUrl();
        $applicationName = $this->tool->getApplication()->getName();

        echo "
                <div class='small-nav'>
                    <a href='$gameUrl'>$gameName</a> > <a href='#'>$applicationName</a>
                </div>";

        echo "<h1>{$this->tool->getName()}</h1>";
        echo "<main class='tool'>";
    }

    function endContent()
    {
        echo "</main>"; // End tool

        $this->webpage->placeAd();
        $this->webpage->placeWave();

        echo "<h2>Information</h2>";
        $explanation = new Information($this->webpage);
        $explanation->addIntroduction($this->tool->getName(), $this->tool->getIntroduction());
        $explanation->addExamples($this->tool->getExamples());
        $explanation->addQuestions($this->tool->getQuestions());
        $explanation->place();

        $this->webpage->placeAd();
        $this->webpage->placeWave();

        echo "<h2>Similar Tools</h2>";
        $links = new LinkGrid($this->webpage);
        $links->setAmountOfRows(-1);
        $links->newLink(LinkType::game, $this->tool->getGame()->getName(), $this->tool->getGame()->getDescription(), $this->tool->getGame()->getPreviewImage(), $this->tool->getGame()->getUrl());
        $links->newLink(LinkType::application, $this->tool->getApplication()->getName(), $this->tool->getApplication()->getDescription(), $this->tool->getApplication()->getIcon(), $this->tool->getApplication()->getUrl());
        $links->place();

        $this->webpage->endContent();

        $this->webpage->generateWebpage();
    }
}
