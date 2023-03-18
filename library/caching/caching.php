<?php
include_once $_SERVER['DOCUMENT_ROOT'] . "/library/caching/minifier.php";


class Caching
{
    private string $url;
    private string $html;
    private string $folderLocation;
    private string $prototypeFolderLocation;
    private array $information = [];
    private bool $noErrors = true;

    function __construct($url)
    {
        $this->url = $url;
        $this->folderLocation = "/cachedWebpages/" . ltrim(explode("?", $this->url)[0], "/") . "-";
        $this->prototypeFolderLocation = $this->folderLocation . "prototype-";
    }

    public function show() {
        include $_SERVER['DOCUMENT_ROOT'] . $this->folderLocation . "/webpage.html";
    }

    public function errorOccurred($errno, $errstr, $errfile, $errline) {
        $this->noErrors = false;
        echo "<b>Custom error:</b> [$errno] $errstr<br>";
        echo " Error on line $errline in $errfile<br>";
    }

    function generateNewWebpage(){
        set_error_handler([$this, 'errorOccurred']);
        $this->makePrototype();
        restore_error_handler();
        if ($this->noErrors == true){
            $this->pushPrototype();
        } else {
            $this->information["pushed"] = false;
        }
        echo json_encode($this->information);
    }

    // Change prototype to by the new main
    private function pushPrototype()
    {
        if (!file_exists($_SERVER['DOCUMENT_ROOT'] . $this->prototypeFolderLocation)){
            $this->information["pushed"] = false;
            trigger_error("Prototype of $this->url doesn't exits", E_USER_WARNING);
            return;
        }

        // Remove folder if already exist
        if (file_exists($_SERVER['DOCUMENT_ROOT'] . $this->folderLocation)) {
            $this->deleteDir($_SERVER['DOCUMENT_ROOT'] . $this->folderLocation);
        }

        // New webpage
        $this->recurseCopy($_SERVER['DOCUMENT_ROOT'] . $this->prototypeFolderLocation, $_SERVER['DOCUMENT_ROOT'] . $this->folderLocation);
        $this->deleteDir($_SERVER['DOCUMENT_ROOT'] . $this->prototypeFolderLocation);

        $this->information["pushed"] = true;
    }

    // Cash prototype webpage at url
    function startCaching()
    {
        set_error_handler([$this, 'errorOccurred']);
        ob_start(); // Start buffer
    }

    function endCaching()
    {
        $this->html = ob_get_contents();
        ob_end_clean();
        restore_error_handler();
    }

    private function makePrototype()
    {
        $webpageFiles = $this->minifyFile($this->html, $this->prototypeFolderLocation);

        // create empty folder
        $folderLocationBackEnd = $_SERVER['DOCUMENT_ROOT'] . $this->prototypeFolderLocation;
        if (file_exists($folderLocationBackEnd)) {
            $files = glob($folderLocationBackEnd . "/*"); // get all file names
            foreach ($files as $file) { // iterate files
                if (is_file($file)) {
                    unlink($file); // delete file
                }
            }
        } else {
            mkdir($folderLocationBackEnd, 0777, true);
        }

        // Create cached files
        $files = ["webpage.html" => "html", "style.css" => "css", "script.js" => "js"];
        foreach ($files as $name => $extension) {
            $file = fopen($folderLocationBackEnd . "/" . $name, "w");
            fwrite($file, $webpageFiles[$extension]);
            fclose($file);
        }

        $this->information["prototype"] = true;
    }

    // Minify file at location
    private function minifyFile($file, $saveLocation)
    { // https://gist.github.com/Rodrigo54/93169db48194d470188f
        $outputFile = $file;

        // read html
        $doc = new DOMDocument();
        $libxml_previous_state = libxml_use_internal_errors(true); // Do not show warning of not well formed code
        $doc->loadHTML($file);
        libxml_clear_errors();
        libxml_use_internal_errors($libxml_previous_state);

        // get all css files
        $cssFiles = [];
        $domcss = $doc->getElementsByTagName('link');
        foreach ($domcss as $links) {
            if (strtolower($links->getAttribute('rel')) == "stylesheet") {
                $link = explode("?", $links->getAttribute('href'))[0];
                if (substr($link, 0, 1) == "/") {
                    $replace = $doc->saveHTML($links);
                    $outputFile = str_replace($replace, "", $outputFile);
                    $replace = str_replace('"', "'", $replace);
                    $outputFile = str_replace($replace, "", $outputFile);
                    array_push($cssFiles, $_SERVER['DOCUMENT_ROOT'] . $link);
                }
            }
        }

        // get all js files
        $jsFiles = [];
        $domcss = $doc->getElementsByTagName('script');
        foreach ($domcss as $links) {
            $link = explode("?", $links->getAttribute('src'))[0];
            if (substr($link, 0, 1) == "/") {
                $replace = $doc->saveHTML($links);
                $outputFile = str_replace($replace, "", $outputFile);
                $replace = str_replace('"', "'", $replace);
                $outputFile = str_replace($replace, "", $outputFile);
                array_push($jsFiles, $_SERVER['DOCUMENT_ROOT'] . $link);
            }
        }

        // combine and minify css
        $minifier = new Minifier("css");
        $minifier->load_files($cssFiles);
        $css = $minifier->minify();

        // insert css link
        $time = round($_SERVER["REQUEST_TIME_FLOAT"]);
        $pos = strpos($outputFile, "</head>");
        $outputFile = substr_replace($outputFile, "<link rel='stylesheet' type='text/css' href='{$saveLocation}/style.css?v={$time}'>", $pos, 0);

        // combine and minify js
        $minifier = new Minifier("js");
        $minifier->load_files($jsFiles);
        $js = $minifier->minify();

        // insert js script link
        $time = round($_SERVER["REQUEST_TIME_FLOAT"]);
        $pos = strpos($outputFile, "</head>");
        $outputFile = substr_replace($outputFile, "<script src='{$saveLocation}/script.js?v={$time}' defer></script>", $pos, 0);

        // minify html
        $minifier = new Minifier("html");
        $minifier->load_content($outputFile);
        $html = $minifier->minify();

        // display output
        return ["html" => $html, "css" => $css, "js" => $js];
    }


    // Delete folder
    private function deleteDir($dirPath)
    {
        if (!is_dir($dirPath)) {
            return;
        }
        if (substr($dirPath, strlen($dirPath) - 1, 1) != '/') {
            $dirPath .= '/';
        }
        $files = glob($dirPath . '*', GLOB_MARK);
        foreach ($files as $file) {
            if (is_dir($file)) {
                $this->deleteDir($file);
            } else {
                unlink($file);
            }
        }
        rmdir($dirPath);
    }

    // Copy folder and rename absolute pathes pointing to this folder in these files
    private function recurseCopy(
        string $sourceDirectory,
        string $destinationDirectory,
        string $childFolder = ''
    ): void {
        $directory = opendir($sourceDirectory);

        if (is_dir($destinationDirectory) === false) {
            mkdir($destinationDirectory);
        }

        if ($childFolder !== '') {
            if (is_dir("$destinationDirectory/$childFolder") === false) {
                mkdir("$destinationDirectory/$childFolder");
            }

            while (($file = readdir($directory)) !== false) {
                if ($file === '.' || $file === '..') {
                    continue;
                }

                if (is_dir("$sourceDirectory/$file") === true) {
                    $this->recurseCopy("$sourceDirectory/$file", "$destinationDirectory/$childFolder/$file");
                } else {
                    copy("$sourceDirectory/$file", "$destinationDirectory/$childFolder/$file");
                }
            }

            closedir($directory);

            return;
        }

        while (($file = readdir($directory)) !== false) {
            if ($file === '.' || $file === '..') {
                continue;
            }

            if (is_dir("$sourceDirectory/$file") === true) {
                $this->recurseCopy("$sourceDirectory/$file", "$destinationDirectory/$file");
            } else {
                $content = file_get_contents("$sourceDirectory/$file");
                $a = str_replace($_SERVER['DOCUMENT_ROOT'], "", $sourceDirectory);
                $b = str_replace($_SERVER['DOCUMENT_ROOT'], "", $destinationDirectory);
                $content = str_replace($a, $b, $content);
                file_put_contents("$destinationDirectory/$file", $content);
                // copy("$sourceDirectory/$file", "$destinationDirectory/$file");
            }
        }

        closedir($directory);
    }
}
