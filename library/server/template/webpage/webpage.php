<?php
include_once($_SERVER['DOCUMENT_ROOT'] . "/module/wave/wave.php");
include_once($_SERVER['DOCUMENT_ROOT'] . "/module/footer/footer.php");
include_once($_SERVER['DOCUMENT_ROOT'] . "/module/navbar/navbar.php");
include_once($_SERVER['DOCUMENT_ROOT'] . "/module/consent/consent.php");

class Webpage
{
    private array $stylesheetLinks;
    private array $javascriptLinks;
    private array $metaTags;
    private array $schemadata;
    private array $colours;
    private string $title;
    private string $style;
    private string $bodyHtml;
    private bool $ads;
    private bool $advanced;

    private Footer $footer;
    private Navbar $navbar;
    // private Consent $consent;
    private Waves $waves;

    function __construct(array $colours=["#8bcdd9", "#6dadcf"])
    {
        $this->advanced = true;
        $this->stylesheetLinks = [];
        $this->javascriptLinks = [];
        $this->metaTags = [];
        $this->schemadata = [];
        $this->title = "";
        $this->style = "";
        $this->colours = $colours;

        $this->waves = new Waves($this);
        $this->footer = new Footer($this);
        $this->navbar = new Navbar($this);
        // $this->consent = new Consent($this);

        $this->addStylesheet("/module/wave/stylesheet.css");
        $this->addStylesheet("/library/server/template/webpage/stylesheet.css");
        $this->addJavascript("/library/client/view/views.js");

        $this->addStyle("
        :root {
            --background: {$colours[0]};
            --accent: {$colours[1]};
        }");
    }

    public function getColours(){
        return $this->colours;
    }

    public function setTitle(string $title)
    {
        $this->title = $title;
    }

    public function setDescription(string $description)
    {
        array_push($this->metaTags, ["description", $description]);
    }

    public function setKeywords(array $keywords)
    {
        array_push($this->metaTags, ["keywords", implode(", ",$keywords)]);
    }

    public function addMetaTag(string $tag, string $content)
    {
        array_push($this->metaTags, [$tag, $content]);
    }

    public function addStyle(string $style)
    {
        $this->style .= $style;
    }

    public function addSchemaData(array $json)
    {
        array_push($this->schemadata, $json);
    }

    public function addStylesheet(string $link)
    {
        if (!in_array($link, $this->stylesheetLinks)) {
            array_push($this->stylesheetLinks, $link);
        }
    }

    public function addJavascript(string $link)
    {
        if (!in_array($link, $this->javascriptLinks)) {
            array_push($this->javascriptLinks, $link);
        }
    }

    public function setAdvanced($advanced){
        $this->advanced = $advanced;
    }

    function placeWave(): void
    {
        $this->waves->place();
    }

    function placeAd(): void
    {
        echo '<div class="ad">
            <ins class="adsbygoogle"
                style="display:block; text-align:center;"
                data-ad-layout="in-article"
                data-ad-format="fluid"
                data-ad-client="ca-pub-6054825053913976"
                data-ad-slot="2146280405">
            </ins>
        </div>';
    }

    public function startContent()
    {
        ob_start();
        $this->waves->placeStart();
    }

    public function endContent()
    {
        $this->bodyHtml = ob_get_contents();
        ob_end_clean();
    }

    public function generateWebpage()
    {
        $html = file_get_contents($_SERVER['DOCUMENT_ROOT'] . "/library/server/template/webpage/webpage.html");
        $html = str_replace("{title}", $this->title, $html);
        $html = str_replace("{meta}", $this->getMetaTags(), $html);
        $html = str_replace("{stylesheets}", $this->getStylesheetLinks().$this->getImportFonts(), $html);
        $html = str_replace("{style}", $this->getStyle(), $html);
        $html = str_replace("{content}", $this->bodyHtml, $html);
        $html = str_replace("{scripts}", $this->getJavascriptLinks(), $html);
        $html = str_replace("{schema_data}", $this->getSchemaData(), $html);
        if ($this->advanced){
            // $html = str_replace("{consent}", $this->getConsent(), $html);
            $html = str_replace("{navbar}", $this->getNavbar(), $html);
            $html = str_replace("{footer}", $this->getFooter(), $html);
        }
        echo $html;
    }

    private function getMetaTags()
    {
        $html = "";
        foreach ($this->metaTags as $metaTag) {
            $html .= "<meta name='$metaTag[0]' content='{$metaTag[1]}'>";
        }
        return $html;
    }

    private function getStyle()
    {
        return "<style>$this->style</style>";
    }

    private function getStylesheetLinks(): string
    {
        $html = "";
        foreach ($this->stylesheetLinks as $stylesheetLink) {
            $lastCssUpdate = filemtime($_SERVER['DOCUMENT_ROOT'] . $stylesheetLink);
            $html .= "<link rel='stylesheet' href='{$stylesheetLink}?v={$lastCssUpdate}'>";
        }
        return $html;
    }

    private function getJavascriptLinks(): string
    {
        $html = "";
        foreach ($this->javascriptLinks as $javascriptLink) {
            if (strpos($javascriptLink, 'https://') !== false || strpos($javascriptLink, 'http://') !== false){
                $html .= "<script src='{$javascriptLink}' defer></script>";
            } else {
                $lastScriptUpdate = filemtime($_SERVER['DOCUMENT_ROOT'] . $javascriptLink);
                $html .= "<script src='{$javascriptLink}?v={$lastScriptUpdate}' defer></script>";
            }
        }
        return $html;
    }

    private function getSchemaData(){
        $html = "<script type='application/ld+json'>";
        $html .= json_encode($this->schemadata);
        $html .= "</script>";
        return $html;
    }

    private function getNavbar(): string
    {
        ob_start();
        $this->navbar->place();
        $html = ob_get_contents();
        ob_end_clean();
        return $html;
    }

    private function getFooter(): string
    {
        ob_start();
        $this->waves->place();
        $this->footer->place();
        $this->waves->placeEnd();
        $html = ob_get_contents();
        ob_end_clean();
        return $html;
    }

    private function getImportFonts(): string
    {
        $html = "";

        // Find fonts
        $noImport = ["Arial", "Sans-serif", "Inherit"];
        $fonts = [];
        foreach ($this->stylesheetLinks as $stylesheet) {
            $webpage = file_get_contents($_SERVER['DOCUMENT_ROOT'] . $stylesheet);
            $splitted = explode("font-family:", $webpage);
            array_shift($splitted); // Remove first item with no font-family inside
            foreach ($splitted as $style) {
                $fontNames = explode(",", explode(";", $style, 2)[0]);
                foreach ($fontNames as $font) {
                    $font = str_replace("'", "", $font);
                    $font = str_replace('"', "", $font);
                    $font = trim($font);
                    $font = ucwords($font);
                    if (!in_array($font, $fonts) && !in_array($font, $noImport)) {
                        array_push($fonts, $font);
                    }
                }
            }
        }

        // Connect to google fonts
        if (count($fonts) != 0) {
            $html .= '<link rel="preconnect" href="https://fonts.googleapis.com">
                <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>';
        }

        // import fonts
        foreach ($fonts as $font) {
            $font = str_replace(" ", "+", $font);
            $html .= "<link href='https://fonts.googleapis.com/css2?family={$font}&display=swap' rel='stylesheet' type='text/css'>";
        }

        //import google icons
        $html .= "<link rel='stylesheet' href='https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@48,700,1,0'/>";

        return $html;
    }

    static function show404($message="404"): void
    {
        // Error
        http_response_code(404);
        $_GET["errormessage"] = $message;
        include($_SERVER['DOCUMENT_ROOT'] . "/public/main/error/webpage.php");
        exit();
    }

    static function upgradeUrl(string $url): string
    {
        $start = explode("/", "$_SERVER[REQUEST_URI]")[1];
        if ($start == "debug") {
            $url = "/debug" . $url;
        }
        return $url;
    }

}
