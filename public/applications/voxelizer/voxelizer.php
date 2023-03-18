<?php
function createFileBase($block_array, $id_collection, $width, $height, $length) {
    $types = [
        "end" => 0,
        "byte" => 1,
        "short" => 2,
        "int" => 3,
        "long" => 4,
        "float" => 5,
        "double" => 6,
        "byte array" => 7,
        "varint array" => 7,
        "string" => 8,
        "list" => 9,
        "compound" => 10,
        "int array" => 11
    ];

    $palette = [];
    foreach($id_collection as $block_id => $index) {
        $temp_palette = [
            "type" => $types["int"],
            "name" => $block_id,
            "value" => $index
        ];
        array_push($palette, $temp_palette);
    }
    
    $map_file = [
        "type" => $types["compound"],
        "name" => "Schematic",
        "value" => [
            [
                "type" => $types["int"],
                "name" => "Version",
                "value" => 2
            ],
            [
                "type" => "varint array",
                "name" => "BlockData",
                "value" => $block_array
            ],
            [
                "type" => $types["compound"],
                "name" => "Palette",
                "value" => $palette
            ],
            [
                "type" => $types["int"],
                "name" => "PaletteMax",
                "value" => count($palette)
            ],
            [
                "type" => $types["short"],
                "name" => "Width",
                "value" => $width
            ],
            [
                "type" => $types["short"],
                "name" => "Height",
                "value" => $height
            ],
            [
                "type" => $types["short"],
                "name" => "Length",
                "value" => $length
            ],
            // [
            //     "type" => $types["string"],
            //     "name" => "Materials",
            //     "value" => "Alpha"
            // ],
            [
                "type" => $types["int"],
                "name" => "DataVersion",
                "value" => 2865
            ]
        ]
    ];
    return $map_file;
}

//***********************************\\
//          HANDLE REQUESTS          \\
//***********************************\\

//include nbt reader/writer
include($_SERVER['DOCUMENT_ROOT'] . '/library/nbt/nbtReadWrite.php');

// start time
$start = microtime(true);

//display errors
ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');

//get item from request
header("Content-Type: application/json");
$send = json_decode(stripslashes(file_get_contents("php://input")), true);

if (array_key_exists("getdata", $send)) {
    $data = [];
    $data["colors"] = json_decode(file_get_contents($_SERVER['DOCUMENT_ROOT'] . '/resources/games/minecraft/colorInformation.json'));
    $data["blocks"] = json_decode(file_get_contents($_SERVER['DOCUMENT_ROOT'] . '/resources/games/minecraft/blocksInformation.json'));
    echo json_encode($data);
    exit;
}

//get data
$dimensions = $send["data"]["dimensions"]["dimensions"];
$width = $dimensions["width"];
$height = $dimensions["height"];
$length = $dimensions["depth"];

$voxels = $send["data"]["voxels"];

//Get all blocks
$id_collection = ["air" => 0];
foreach($voxels as $key => $voxel) {
    $id = $voxel["voxel"]["id"];
    if (!array_key_exists($id, $id_collection)) {
        $id_collection[$id] = count($id_collection);
    }
}

$block_array = array_fill(0, $width * $height * $length, 0);
foreach($voxels as $key => $voxel) {
    $position = $voxel["voxel"]["position"];
    $voxel_x = $position["x"];
    $voxel_y = $position["y"];
    $voxel_z = $position["z"];
    $index = ($voxel_y*$length + $voxel_z) * $width + $voxel_x;

    $id = $voxel["voxel"]["id"];
    $block_array[$index] = $id_collection[$id]; //from to palette
}

$writer = new NbtWriter();

//SINGLE FILE
$map_file = createFileBase($block_array, $id_collection, $width, $height, $length);
$binary = $writer -> writeObject($map_file);
$gzipped = gzencode($binary);
echo $gzipped;