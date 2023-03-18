<?php 

include($_SERVER['DOCUMENT_ROOT'] . '/library/nbt/nbtReadWrite.php');

$writer = new NbtWriter();

header("Content-Type: application/json");
$data = json_decode(stripslashes(file_get_contents("php://input")), true);

$binary = $writer -> writeObject($data);


$gzipped = gzencode($binary);

echo $gzipped;

?>