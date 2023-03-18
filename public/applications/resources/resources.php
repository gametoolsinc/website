<?php
// // start time
// $start = microtime(true);

// //display errors
// ini_set('display_errors', '1');
// ini_set('display_startup_errors', '1');
// error_reporting(E_ALL);

// //get item from request
// header("Content-Type: application/json");
// $send = json_decode(stripslashes(file_get_contents("php://input")), true);
// $items = $send["items"];

// //get data
// $game = $send["game"];
// // $string = file_get_contents("../../../data/".$game."/itemsInformation.json");
// $string = file_get_contents($_SERVER['DOCUMENT_ROOT']."/data/".$game."/itemsInformation.json");
// $data = json_decode($string, true);

// //setup variables
// $resources = [];
// $guide = [];
// $information = ["items"=>[], "methodes"=>[], "proces"=>[]];
// $remaining = [];
// foreach($data["information"]["obtaining"] as $item){
//     $information["methodes"][$item["methode"]] = ["methode"=>$item["methode"], "text"=>$item["text"]];
// }

// foreach($items as $item => $amount){
//     getResources($item, $amount, 0);
// }

// // end timer
// $time_elapsed_secs = microtime(true) - $start;
// $information["proces"]["elapsed time"] = $time_elapsed_secs;

// $returnItem = [];
// $returnItem["making"] = &$items;
// $returnItem["information"] = &$information;
// $returnItem["resources"] = &$resources;
// $returnItem["guide"] = &$guide;

// echo json_encode($returnItem);

// function getResources($name, $amount, $guideInsertAt){
    
//     global $data;
//     global $resources;
//     global $guide;
//     global $information;
//     global $remaining;

//     //find item
//     $name = str_replace("'", "&#39;", $name);
//     $item = $data["items"][$name];

//     // add item to information
//     if (!array_key_exists($name, $information["items"])){
//         $information["items"][$name] = ["name"=>$item["name"], "location image"=>$item["location image"]];
//     }

//     if (count($item["obtaining"]) === 0){
//         // add to resources
//         if (!array_key_exists($name, $resources)){
//             $resources[$name] = ["name"=>$name, "amount"=>0];
//         }
//         $resources[$name]["amount"] += $amount;        
//     } else {

//         // add to guide
//         $methode = $item["obtaining"][0];

//         // create/get guide and recourses
//         if (!array_key_exists($name, $guide) && !array_key_exists($name, $resources)){

//             // create data
//             $dataItem = ["type"=>$methode["type"], "input"=>[], "output"=>$methode["output"], "times"=>0];
//             if (array_key_exists("input", $methode)){
//                 foreach ($methode["input"] as $item){
//                     if (array_key_exists("name", $item)){
//                         array_push($dataItem["input"], $item);
//                     } else {
//                         array_push($dataItem["input"], $item[0]);
//                     }
//                 }
//             }

//             // setup data
//             $remaining[$name] = 0;
//             if (array_key_exists("input", $methode)){
//                 // create guide
//                 $guide = array_merge(
//                     array_slice($guide, 0, $guideInsertAt),
//                     array($name => $dataItem),
//                     array_slice($guide, $guideInsertAt, null)
//                 );
//             } else {
//                 // create resources (add guide as information of resources) LATER PROJECT
//                 $resources[$name] = ["name"=>$name, "amount"=>0];
//             }

//             if (array_key_exists("option", $methode)){
//                 $guide[$name]["option"] = [$methode["option"]];

//                 // add item to information
//                 if (!array_key_exists($methode["option"], $information["items"])){
//                     $information["items"][$methode["option"]] = ["name"=>$methode["option"], "location image"=>$data["items"][$methode["option"]]["location image"]];
//                 }
//             }
//         }
        

//         // calculate amount
//         if (array_key_exists("input", $methode)){
//             // subtract remaining
//             $guideItem = &$guide[$name];
//             $remaining[$name] -= $amount;
            
//             // make times bigger if remaining is smaller then 0
//             if ($remaining[$name] < 0){
//                 $times = ceil((-$remaining[$name])/$methode["output"][0]["amount"]);
//                 $guideItem["times"] += $times;
//                 foreach ($guideItem["output"] as $subItem){
//                     $remaining[$name] += $subItem["amount"]*$times;
//                 }
//                 foreach ($guideItem["input"] as $subItem){
//                     if ($subItem["name"] !== ""){
//                         getResources($subItem["name"], $times*$subItem["amount"], array_search($name, array_keys($guide), true));
//                     }
//                 }
//             }
//         } else {
//             // add resources
//             $resources[$name]["amount"] += $amount;
//         }
//     }
// }

// function isAssoc(array $arr){
//     if (array() === $arr) return false;
//     return array_keys($arr) !== range(0, count($arr) - 1);
// }

