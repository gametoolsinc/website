<?php

function createFileBase($colors_array, $version) {
    $types = [
        "end" => 0,
        "byte" => 1,
        "short" => 2,
        "int" => 3,
        "long" => 4,
        "float" => 5,
        "double" => 6,
        "byte array" => 7,
        "string" => 8,
        "list" => 9,
        "compound" => 10,
        "int array" => 11
    ];
    
    $map_file = [
        "type" => $types["compound"],
        "name" => "",
        "value" => [
            [
                "type" => $types["compound"],
                "name" => "data",
                "value" => [
                    [
                        "type" => $types["byte"],
                        "name" => "scale",
                        "value" => 0
                    ],
                    [
                        "type" => $types["byte"],
                        "name" => "dimension",
                        "value" => 0
                    ],
                    [
                        "type" => $types["byte"],
                        "name" => "trackingPosition",
                        "value" => 0
                    ],
                    [
                        "type" => $types["byte"],
                        "name" => "locked",
                        "value" => 1
                    ],
                    [
                        "type" => $types["short"],
                        "name" => "height",
                        "value" => 128
                    ],
                    [
                        "type" => $types["short"],
                        "name" => "width",
                        "value" => 128
                    ],
                    [
                        "type" => $types["int"],
                        "name" => "xCenter",
                        "value" => 0
                    ],
                    [
                        "type" => $types["int"],
                        "name" => "zCenter",
                        "value" => 0
                    ],
                    [
                        "type" => $types["byte array"],
                        "name" => "colors",
                        "value" => $colors_array
                    ]
                ]
            ]
        ]
    ];
    if ($version !== null) {
        $dataVersion = [
            "type" => $types["int"],
            "name" => "DataVersion",
            "value" => $version
        ];
        array_push($map_file["value"], $dataVersion);
    }
    return $map_file;
}

function sendDataFormat($str) {
    $split = str_split($str);
    for($i = 0; $i < count($split); $i += 1) {
        $split[$i] = bin2hex($split[$i]);
    }
    return implode(' ',$split);
}

//***********************************\\
//          HANDLE REQUESTS          \\
//***********************************\\

//include nbt reader/writer
include($_SERVER['DOCUMENT_ROOT'] . '/library/server/nbt/nbtReadWrite.php');

// start time
$start = microtime(true);

//display errors
ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');

//get item from request
header("Content-Type: application/json");
$send = json_decode(stripslashes(file_get_contents("php://input")), true);

//get data
if (array_key_exists("getdata", $send)) {
    $data = file_get_contents($_SERVER['DOCUMENT_ROOT'] . '/resources/games/minecraft/colorInformation.json');
    echo $data;
    exit;
} else {
    $colors = $send["colors"];
    $latest_map_amount = $send["latest map number"];
    if ($send["version"] === "1.7.2") {
        $version = 4;
    } else {
        $version = null;
    }
    // $dimension = $send["dimension"];
}


//Write to binary
$writer = new NbtWriter();

$file = tmpfile();
$file_location = stream_get_meta_data($file)['uri'];

if (count($colors) > 1) {
    //ZIP FORMAT
    $zip = new ZipArchive();
    $zip->open($file_location, ZipArchive::OVERWRITE);
    $i = ($latest_map_amount + 1) - count($colors);

    foreach($colors as $array) {
        $map_file = createFileBase($array, $version);
        $binary = $writer -> writeObject($map_file);
        $gzipped = gzencode($binary);
        $zip->addFromString('map_' . $i . '.dat', $gzipped);
        $i++;
    }
    $zip->close();

    header('Content-type: application/zip');
    header('Content-Disposition: attachment; filename="maps.zip"');

} else {
    //SINGLE FILE
    $map_file = createFileBase($colors[0], $version);
    $binary = $writer -> writeObject($map_file);
    $gzipped = gzencode($binary);
    file_put_contents($file_location, $gzipped);
}

echo file_get_contents($file_location);