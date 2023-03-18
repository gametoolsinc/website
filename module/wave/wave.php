<?php
include_once($_SERVER['DOCUMENT_ROOT'] . "/library/server/colours/colours.php");
include_once($_SERVER['DOCUMENT_ROOT'] . "/module/module.php");
include_once($_SERVER['DOCUMENT_ROOT'] . "/library/server/template/webpage/webpage.php");

class Waves extends Module
{
    private array $colours;
    private int $amount_of_waves;
    private int $width;
    private int $height;
    private int $amount_of_points;
    private int $amount_of_layers;
    private float $height_difference;
    private int $space_between_points;
    private int $height_layer;

    function __construct(Webpage $webpage)
    {
        $this->colours = $webpage->getColours();
        $this->amount_of_waves = 0;
        $this->width = 2000;
        $this->height = 200;
        $this->amount_of_points = ceil($this->width / 250);
        $this->amount_of_layers = 3;
        $this->height_difference = 0.6;
        $this->space_between_points = $this->width / $this->amount_of_points;
        $this->height_layer = $this->height / $this->amount_of_layers;

        $webpage->addStylesheet("/module/wave/stylesheet.css");
        $webpage->addStyle($this->getStyle());
    }

    private function getStyle(): string{
        $style = "";
        for ($index = 0; $index < count($this->colours); $index++){
            $previousIndex = $index - 1;
            if ($previousIndex < 0){
                $previousIndex = count($this->colours) + $previousIndex;
            }
            $previousColour = $this->colours[$previousIndex];
            $colour = $this->colours[$index];
            $style .= "
            .wave-$index {
                background-color: $previousColour;
                fill: $colour;
            }
            #wave-background-$index {
                background-color: $colour;
            }
            ";
        }
        return $style;
    }

    function placeStart(){
        $waveNumber = $this->amount_of_waves % count($this->colours);
        $this->amount_of_waves += 1;
        echo "<section class='wrapper'>";
        echo "<div class='wave-background first-wave-background' id='wave-background-{$waveNumber}'></div>";
    }

    function place()
    {
        $waveNumber = $this->amount_of_waves % count($this->colours);
        $this->amount_of_waves += 1;

        // Close wrapper
        if ($this->amount_of_waves > 0){
            echo "</section>";
        }

        echo "<section class='wrapper'>";
        echo "<div class='wave-background' id='wave-background-{$waveNumber}'></div>";
        
        // Generate
        $layers = [];
        for ($i = 0; $i < $this->amount_of_layers; $i++) {
            $layer = [];
            $previousLayer = end($layers);
            for ($j = 0; $j <= $this->amount_of_points; $j++) {
                if ($previousLayer == false) {
                    $offset = rand(0, $this->height_layer);
                } else {
                    $previousOffset = $previousLayer[$j][1] - $this->height_layer * ($i - 1);
                    $min = max(0, $previousOffset - $this->height_difference * $this->height_layer);
                    $max = min($this->height_layer, $previousOffset + $this->height_difference * $this->height_layer);
                    $offset = rand((int) round($min), (int) round($max));
                }
                $point = [$this->space_between_points * $j, $i * $this->height_layer + $offset];
                array_push($layer, $point);
            }
            array_push($layers, $layer);
        }
        
        echo "<svg xmlns='http://www.w3.org/2000/svg' preserveAspectRatio='none' height='$this->height' viewBox='0 0 $this->width $this->height' class='wave-{$waveNumber} wave' style='min-width: {$this->width}px;'>";

        for ($i = 0; $i < $this->amount_of_layers; $i++) {
            $fillOpacity = 1 / $this->amount_of_layers * ($i + 1);
            $d = "";
            for ($j = 0; $j <= $this->amount_of_points; $j++) {
                $point = $layers[$i][$j];
                $dx2 = $point[0] - rand((int) round($this->space_between_points / 3), (int) round($this->space_between_points / 3 * 2));
                $dy2 = $point[1] - rand((int) round(-$this->height_layer / 4), (int) round($this->height_layer / 4));
                if ($j == 0) {
                    $d .= "M $point[0] $point[1]";
                } else if ($j == 1) {
                    $d .= "C $dx2 $dy2 $dx2 $dy2 $point[0] $point[1]";
                } else {
                    $d .= "S $dx2 $dy2 $point[0] $point[1]";
                }
            }
            $d .= "L $this->width $this->height L 0 $this->height Z";
            echo "<path d='$d' stroke='transparent' fill='inherit' fill-opacity='$fillOpacity'/>";
        }
        echo '</svg>';
    }

    function placeEnd(){
        echo "</section>"; // End wrapper
    }
}

// Testing
if (basename(__FILE__) == basename($_SERVER["SCRIPT_FILENAME"])) {
    $webpage = new Webpage();
    $webpage->setAdvanced(false);
    $webpage->startContent();
    $waves = new Waves($webpage);
    $waves->placeStart();
    $waves->place();
    $waves->placeEnd();
    $webpage->endContent();
    $webpage->generateWebpage();
}
