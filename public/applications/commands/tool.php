<?php 
include_once $_SERVER['DOCUMENT_ROOT'] . "/library/server/dataManagement/tool.php";
include_once $_SERVER['DOCUMENT_ROOT'] . "/library/server/dataManagement/games/commands.php";

$tool = new Tool($_GET['id']);
$data = new Command($tool, "setblock");


?>

<!-- Import Text Editor -->
<script src="//cdn.quilljs.com/1.3.6/quill.min.js"></script>
<link href="//cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet">

<?php //echo '<link rel="stylesheet" href="'.$dataFrontEndLocation.'/user_interface/font/stylesheet.css">' 
?>

<div class="generator-container">
    <div class="page-menu">
        <h2>Generators</h2>

        <?php 
        $nav_item_html = '<div class="page-menu-item"><a href="/{game}/commands/{name}">{name}</a></div>';
        $commands = $data->getCommands();
        foreach($commands as $commandName => $command) {
            echo $data->getNavItem($commandName);
        } 

        ?>
    </div>

    <div class="generator">
        <div class="form">
            <h2>Make command</h2>

            <?php
            //HTML templates
            $inputHTML = '
            <div class="input-field">
                <p>{name}</p>
                <input type="{type}" {attr} oninput="readValues()"> 
            </div>';

            $selectHTML = '
            <div class="input-field select" {attr}>
                <p>{name}</p>
                <select oninput="setSelect(this)">{options}</select>
                {toggleDivs}
            </div>';

            $textEditorHTML = '
            <div class="texteditor">
                <div class="settings-bar">
                    <div class="setting"><b>B</b></div>
                    <div class="setting"><i>I</i></div>
                    <div class="setting"><u>U</u></div>
                    <div class="setting"><s>S</s></div>
                    <div class="setting"><p>O</p></div>
                </div>
                <div class="output-field">

                </div>
            </div>';

            $optionHTML = '<option {value}>{name}</option>';

            $toggleDivHTML = '<div class="toggle-div" data-corresponding-value="{value}">{content}</div>';

            $rangeHTML = '<div class="input-field range" {attr}>{content}</div>';
            
            $boxHTML = '<div class="box">{content}</div>';

            $boxTextHTML = '<div class="box"><p>{name}</p>{content}</div>';
            
            //Generating input and selection fields
            function checkContent($arr, $key) {
                //Arguments
                //$arr: the array with all the information
                //$key: the string with the name of the array which get checked
                global $data, $inputHTML, $selectHTML, $optionHTML, $toggleDivHTML, $rangeHTML, $boxHTML;

                $return = '';
                if ($key !== "information" && $key !== "type" && $key !== "set value") {

                    $type = $arr["type"];
                    //If type is an input field 
                    if ($type === "input" || $type === "number") {
                        //Check input type
                        if ($type === "input") {
                            $html = str_replace("{type}", "text", $inputHTML);
                        } else if ($type === "number") {
                            $html = str_replace("{type}", "number", $inputHTML);
                        }
                        //Check if alternative name is specified
                        if (array_key_exists("display name", $arr)) {
                            $html = str_replace("{name}", $arr["display name"], $html);
                        } else {
                            $html = str_replace("{name}", $key, $html);
                        }
                        $return = str_replace("{attr}", checkAttributes($arr), $html);
                    }                         
                    //If type is an select field
                    else if ($type === "select" && array_key_exists("options", $arr)) {
                        $options = $toggle_divs = '';
                        foreach ($arr["options"] as $name => $item) {
                            if ($name == "use file") { //Use json file content as options
                                $file_name = $item["file"];
                                $json_file_data = $data->getJSONFile($file_name);
                                foreach($json_file_data as $option_text => $option_array) {
                                    $option_value = $option_array[$item["key as value"]];
                                    $options .= checkContent(
                                        ["type" => "option", "set value" => $option_value], 
                                        $option_text
                                    );
                                }
                                continue;
                            }
                            $options .= checkContent($item, $name);
                            if ($item["type"] === "option" && array_key_exists("set display", $item) && gettype($item["set display"]) === "array") {
                                $content = checkContent($item["set display"], null);
                                $toggle_div = str_replace("{value}", $name, $toggleDivHTML);
                                $toggle_divs .= str_replace("{content}", $content, $toggle_div);
                            }
                            
                        }
                        $html = str_replace("{attr}", checkAttributes($arr), $selectHTML);
                        $html = str_replace("{name}", $key, $html);
                        $html = str_replace("{options}", $options, $html);
                        $return .= str_replace("{toggleDivs}", $toggle_divs, $html);
                    } 
                    //If type is an option field (always inside select fields)
                    else if ($type === "option" && (array_key_exists("set value", $arr) || array_key_exists("set display", $arr))) {
                        $option = str_replace("{name}", $key, $optionHTML);
                        if (array_key_exists("set value", $arr)) {
                            $return .= str_replace("{value}", 'value="'.$arr["set value"].'"', $option);
                        } else {
                            $return .= str_replace("{value}", "value", $option);
                        }
                    } 
                    //If type is a range between numbers (in Minecraft: 23..54)
                    else if ($type === "range" && array_key_exists("argument name", $arr) && array_key_exists("content", $arr)) {
                        $contents = '';
                        foreach($arr["content"] as $name => $item) {
                            $contents .= checkContent($item, $name);
                        }
                        $html = str_replace("{attr}", checkAttributes($arr), $rangeHTML);
                        $return .= str_replace("{content}", $contents, $html);
                    } 
                    //If type is a box (a group of elements which display can be toggled)
                    else if ($type === "box" && array_key_exists("content", $arr)) {
                        $contents = '';
                        foreach($arr["content"] as $name => $item) {
                            $contents .= checkContent($item, $name);
                        }
                        $return .= str_replace("{content}", $contents, $boxHTML);
                    } 
                    //If type is a list (which you can add more items with buttons)
                    else if ($type === "list") {

                    } else if ($type === "text editor") {

                    }
                    //If type is a already existing selector
                    else if (array_key_exists($type, $data->types)) {
                        foreach($data->types[$type] as $name => $item) {
                            if (array_key_exists("argument of", $arr)) {
                                $item["argument of"] = $arr["argument of"];
                            }
                            $return .= checkContent($item, $name);
                        }
                    }
                    //Check if there are arguments specified
                    if (array_key_exists("arguments", $arr)) {
                        foreach($arr["arguments"] as $name => $item) {
                            $item["argument of"] = $key;
                            $return .= checkContent($item, $name);
                        }
                    }
                }
                return $return;
            }

            function checkAttributes($arr) {
                $return = '';
                $attributes = ["prefix", "suffix", "string", "main", "argument name", "argument of", "default value"];
                foreach($attributes as $attribute) {
                    if (array_key_exists($attribute, $arr)) {
                        $formatted = str_replace(" ", "-", $attribute);
                        $return .= ' data-'.$formatted.'="'.$arr[$attribute].'"';
                    }
                }
                return $return;
            }

            //Main
            $commandString = $data->getCommand();
            $split = explode("{", $commandString);
            $split = array_splice($split, 1);
            foreach($split as $key => $value) {
                $split[$key] = preg_replace('/}.*/', "", $value);
                $split[$key] = explode(",", $split[$key]);
                foreach($split[$key] as $subkey => $subvalue) {
                    $split[$key][$subkey] = trim($split[$key][$subkey]);
                }
            }

            ///////// DIT MOET NOG KORTER GEMAAKT WORDEN (ben er mee bezig)! \\\\\\\\\\\

            foreach($split as $key => $value) {
                //If there are multiple selectors to fill a part of the command (i.e: targetselector & coordinates)
                $types = $data->types;
                if (count($value) > 1) {
                    $html = str_replace("{name}", "Choose type:", $selectHTML);
                    $html = str_replace("input-field", "parent-div", $html);
                    $html = str_replace("{attr}", "", $html);
                    $main_options = $extraDivs = '';
                    foreach($value as $type => $content) {
                        if (array_key_exists($content, $types)) {
                            $option = str_replace("{value}", 'value="'.$content.'"', $optionHTML);
                            $extra_div = $toggleDivHTML;
                            if (array_key_exists("information", $types[$content])) {
                                if (array_key_exists("type name", $types[$content]["information"])) {
                                    $option = str_replace("{name}", $types[$content]["information"]["type name"], $option);
                                    $extra_div = str_replace("{value}", $types[$content]["information"]["type name"], $extra_div);
                                }
                                if (array_key_exists("background color", $types[$content]["information"])) {
                                    $extra_div = str_replace('">', '" style="background-color: '.$types[$content]["information"]["background color"].'">', $extra_div);
                                }
                                if (array_key_exists("set value", $types[$content]["information"])) {
                                    $extra_div = str_replace('">', '" setvalue="'.$types[$content]["information"]["set value"].'">', $extra_div);
                                }
                            } else {
                                $option = str_replace("{name}", $content, $option);
                                $extra_div = str_replace("{value}", $content, $extra_div);
                            }
                            $contents = '';
                            foreach($types[$content] as $name => $item) {
                                $contents .= checkContent($item, $name)."\n";
                            }
                            $extra_div = str_replace("{content}", $contents, $extra_div);
                            $main_options .= $option;
                            $extraDivs .= $extra_div;
                        }
                    }
                    $html = str_replace("{options}", $main_options, $html);
                    $html = str_replace("{toggleDivs}", $extraDivs, $html);
                    echo $html;
                }
                //If there is only 1 selector to fill part of the command (i.e: only targetselector)
                else {
                    foreach($value as $type => $content) {
                        if (array_key_exists($content, $types)) {
                            $html = '';
                            if (array_key_exists("information", $types[$content])) {
                                if (array_key_exists("type name", $types[$content]["information"])) {
                                    $html = str_replace("{name}", $types[$content]["information"]["type name"]."*", $boxTextHTML);
                                }
                                if (array_key_exists("background color", $types[$content]["information"])) {
                                    $html = str_replace('"box">', '"box" style="background-color: '.$types[$content]["information"]["background color"].'">', $html);
                                }
                            } else {
                                $html = str_replace("{name}", $content, $boxTextHTML);
                            }
                            $contents = '';
                            foreach($types[$content] as $name => $item) {
                                $contents .= checkContent($item, $name)."\n";
                            }
                            $html = str_replace("box", "parent-div box", $html);
                            $html = str_replace("{content}", $contents, $html);
                            echo $html;
                        }
                    }
                }
            }
            ?>
        </div>
        <div class="output">
            <div class="texteditor">
                <div class="settings-bar">
                    <button class="setting" onclick="fontChange('bold')"><b>B</b></button>
                    <button class="setting" onclick="fontChange('italic')"><i>I</i></button>
                    <button class="setting" onclick="fontChange('underlined')"><u>U</u></button>
                    <button class="setting" onclick="fontChange('strikethrough')"><s>S</s></button>
                    <button class="setting" onclick="fontChange('obfuscated')"><p>O</p></button>
                </div>
                <div class="output-input" contenteditable="true">

                </div>
            </div>
            <div class="texteditor-container">
                <div class="texteditor2">
                
                </div>
            </div>
            <div class="sticky-container">
                <h2>Output</h2>
                <textarea class="output-field" placeholder="Your command should appear here" spellcheck="false"></textarea>
                <div class="output-details">
                    <div class="copy"><button id="copy" onclick="copy()">Copy</button></div>
                    <p id="command-length">0 characters</p>
                </div>
            </div>
        </div>
    </div>
</div>

<?='<script>var command = "'.$commandString.'" </script>';