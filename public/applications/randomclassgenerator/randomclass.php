<?php
// Start time
$start = microtime(true);

//Display errors
ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);

//Get data from request
header("Content-Type: application/json");
$send = json_decode(stripslashes(file_get_contents("php://input")), true);

$string = file_get_contents($_SERVER["DOCUMENT_ROOT"] . "/resources/games/codcoldwar/itemsInformation.json");
$data = json_decode($string, true);

//Get and set counter
$counter_file = $_SERVER["DOCUMENT_ROOT"] . "/resources/games/codcoldwar/classCounter.json";
$counter_string = file_get_contents($counter_file);
$counter = json_decode($counter_string, true);
$counter["counter"] += 1;
file_put_contents($counter_file, json_encode($counter));

//Set variables for data
$equipment = $data["equipment"];
$wildcards = $data["wildcards"];
$perks = $data["perks"];
$scorestreaks = $data["scorestreaks"];
$weapons = $data["weapons"];
$return = [];

//Pick random wildcard
$wildcard_name = array_rand($wildcards);
$wildcard = $wildcards[$wildcard_name];

//Set class info based on wildcard
$attachment_amount = $wildcard["attachment amount"];
$type_of_weapons = $wildcard["type of weapons"];
$perk_amount = $wildcard["perk amount"];

//Gamesatlas
// $standard_allowed_weapons_main = ["Assault Rifles", "SMGs", "Tactical Rifles", "LMGs", "Sniper Rifles"];
// $standard_allowed_weapons_secondary = ["Handguns", "Shotguns", "Launchers", "Melee", "Special Weapons"];

//Call of Duty wiki
$standard_allowed_weapons_main = ["Assault Rifle", "Submachine Gun", "Tactical Rifle", "Light Machine Gun", "Sniper Rifle"];
$standard_allowed_weapons_secondary = ["Pistol", "Shotgun", "Launcher", "Melee", "Special"];

$primary_weapon = $secondary_weapon = '';
$first_perk = $second_perk = $third_perk = [];

//Reorder perks inside seperate arrays
$first_perks = $second_perks = $third_perks = [];
foreach($perks as $perk) {
    if ($perk["perk slot"] === "1") {
        array_push($first_perks, $perk);
    } else if ($perk["perk slot"] === "2") {
        array_push($second_perks, $perk);
    } else if ($perk["perk slot"] === "3") {
        array_push($third_perks, $perk);
    }
}

//Pick perks
if ($perk_amount === 6) {
    //Perk 1
    $first_perk = array($first_perks[array_rand($first_perks)], $first_perks[array_rand($first_perks)]);
    while ($first_perk[0] === $first_perk[1]) {
        $first_perk = array($first_perks[array_rand($first_perks)], $first_perks[array_rand($first_perks)]);
    }
    //Perk 2
    $second_perk = array($second_perks[array_rand($second_perks)], $second_perks[array_rand($second_perks)]);
    while ($second_perk[0] === $second_perk[1]) {
        $second_perk = array($second_perks[array_rand($second_perks)], $second_perks[array_rand($second_perks)]);
    }
    // Perk 3
    $third_perk = array($third_perks[array_rand($third_perks)], $third_perks[array_rand($third_perks)]);
    while ($third_perk[0] === $third_perk[1]) {
        $third_perk = array($third_perks[array_rand($third_perks)], $third_perks[array_rand($third_perks)]);
    }
} else if ($perk_amount === 3) {
    $first_perk = [$first_perks[array_rand($first_perks)]];
    $second_perk = [$second_perks[array_rand($second_perks)]];
    $third_perk = [$third_perks[array_rand($third_perks)]];
}

//Pick random scorestreaks
$scorestreaks_random = array_rand($scorestreaks, 3);
$scorestreaks_info = [];
foreach($scorestreaks_random as $random) {
    $scorestreaks_info[$random] = $scorestreaks[$random];
}

//Pick random equipment
$tactical = $equipment["Tactical"][array_rand($equipment["Tactical"])];
$lethal = $equipment["Lethal"][array_rand($equipment["Lethal"])];
$field_upgrades = $equipment["Field Upgrades"][array_rand($equipment["Field Upgrades"])];

//Pick primary & secondary weapon
randomWeapons($weapons, $type_of_weapons, $standard_allowed_weapons_main, $standard_allowed_weapons_secondary);

$class_weapons = [];

//Pick attachments primary weapon
if (isset($weapons[$primary_weapon]["attachments"])) {
    $class_weapons[$primary_weapon] = randomAttachments($weapons[$primary_weapon]);
} else {
    $class_weapons[$primary_weapon] = [];
}
if (isset($weapons[$secondary_weapon]["attachments"])) {
    $class_weapons[$secondary_weapon] = randomAttachments($weapons[$secondary_weapon]);
} else {
    $class_weapons[$secondary_weapon] = [];
}

//Set order for primary and secondary weapon in object
$weapons[$primary_weapon]["class_type"] = "Primary";
$weapons[$secondary_weapon]["class_type"] = "Secondary";

//Order return object
$return["class"]["weapons"] = $class_weapons;
$return["class"]["perks"] = array("1st" => $first_perk, "2nd" => $second_perk, "3rd" => $third_perk); 
$return["class"]["wildcard"] = $wildcard;
$return["class"]["scorestreaks"] = $scorestreaks_random;
$return["class"]["equipment"]["Tactical"] = $tactical;
$return["class"]["equipment"]["Lethal"] = $lethal;
$return["class"]["equipment"]["Field Upgrade"] = $field_upgrades;

$return["information"]["weapons"] = array($primary_weapon => $weapons[$primary_weapon], $secondary_weapon => $weapons[$secondary_weapon]);
$return["information"]["scorestreaks"] = $scorestreaks_info;
$return["information"]["counter"] = $counter["counter"];

$time_elapsed_secs = microtime(true) - $start;
$return["information"]["elapsed time"] = $time_elapsed_secs.'s';

echo json_encode($return, true);

function randomAttachments($array) {
    $return = [];
    foreach($array["attachments"] as $type => $key) {
        if (count($key) !== 0) {
            $return[$type] = $key[array_rand($key)];
        }
    }
    return $return;
}

function randomWeapons($array, $mode, $allowed_main, $allowed_second) {
    global $primary_weapon, $secondary_weapon;
    if ($mode === "all") {
        $primary_weapon = array_rand($array);
        $secondary_weapon = array_rand($array);
        while ($primary_weapon === $secondary_weapon) {
            $primary_weapon = array_rand($array);
            $secondary_weapon = array_rand($array);
        }
    } else {
        $primary_weapon = array_rand($array);
        while (in_array($array[$primary_weapon]["type"], $allowed_main) === false) {
            $primary_weapon = array_rand($array);
        }
        $secondary_weapon = array_rand($array);
        while (in_array($array[$secondary_weapon]["type"], $allowed_second) === false) {
            $secondary_weapon = array_rand($array);
        }
    }
    return;
}
