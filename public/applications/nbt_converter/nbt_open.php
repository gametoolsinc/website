<?php 

include($_SERVER['DOCUMENT_ROOT'] . '/library/server/nbt/nbtReadWrite.php');

// $map_file = [
//     "type" => "compound",
//     "name" => "",
//     "value" => [
//         [
//             "type" => "compound",
//             "name" => "data",
//             "value" => [
//                 [
//                     "type" => "byte",
//                     "name" => "scale",
//                     "value" => 0
//                 ],
//                 [
//                     "type" => "byte",
//                     "name" => "dimension",
//                     "value" => 0
//                 ],
//                 [
//                     "type" => "byte",
//                     "name" => "trackingPosition",
//                     "value" => 0
//                 ],
//                 [
//                     "type" => "byte",
//                     "name" => "locked",
//                     "value" => 1
//                 ],
//                 [
//                     "type" => "short",
//                     "name" => "height",
//                     "value" => 128
//                 ],
//                 [
//                     "type" => "short",
//                     "name" => "width",
//                     "value" => 128
//                 ],
//                 [
//                     "type" => "int array",
//                     "name" => "xCenter",
//                     "value" => [1,2,3,4,5,6,7,8,9]
//                 ],
//                 [
//                     "type" => "int",
//                     "name" => "zCenter",
//                     "value" => 0
//                 ]
//             ]
//         ],
//         [
//             "type" => "compound",
//             "name" => "Test",
//             "value" => [
//                 [
//                     "type" => "string",
//                     "name" => "testString",
//                     "value" => "Can you read this? "
//                 ]
//             ]
//         ]
//     ]
// ];

// $writer = new NbtWriter();
// $binary = $writer -> writeObject($map_file);
// writeFile($binary);

$reader = new NbtReader();

$filename = $_FILES['fileblob']['tmp_name'];
$handle = fopen($filename, "r");
$file_data = fread($handle, filesize($filename));
$file_data = mb_convert_encoding($file_data, 'ISO-8859-1');
fclose($handle);

// echo $file_data;

//Check if file is gzipped by checking first byte
if (substr($file_data, 0, 1) === chr(0x1F)) {
    $file_data = gzdecode($file_data);   
}

$data = $reader -> readData($file_data);
echo json_encode($data, JSON_PRETTY_PRINT);

?>