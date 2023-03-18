<?php
include_once($_SERVER['DOCUMENT_ROOT'] . "/library/server/template/tool/toolWebpage.php");
include_once($_SERVER['DOCUMENT_ROOT'] . "/library/server/caching/minifier.php");
include_once($_SERVER['DOCUMENT_ROOT'] . "/library/server/dataManagement/games/items.php");

$tool_webpage = new ToolWebpage($_GET["id"]);
$tool_webpage->startContent();


?>


<style>
    <?php
    $items = new Items($tool_webpage->getTool());
 
    $items->placeIconsCss();

    // styles
    $sampleHtmlMethode = '
        .{methode} .obtaining {
            background-image: url({url});
            width: 100%;
            padding-top: {relation}%;
        }
        ';
    $sampleHtmlItem = '
        .{methode} .obtaining .{type}-{number} {
            left: {x}%;
            top: {y}%;
            width: {width}%;
            padding-top: {height}%;
        }
        ';

    foreach ($items->getObtainingMethodes() as $methode) {
        // add methode
        $html = $sampleHtmlMethode;
        $html = str_replace("{methode}", $methode->getId(), $html);
        $html = str_replace("{url}", $methode->getImage(), $html);
        $imageSize = getimagesize($_SERVER['DOCUMENT_ROOT'] . $methode->getImage());
        $html = str_replace("{relation}", ($imageSize[1] / $imageSize[0] * 100), $html);
        echo $html;

        // add input items
        foreach ($methode->getInput() as $index=>$item) {
            placeSubItem($methode, "input", $index, $item);
        }

        // add option items
        foreach ($methode->getOption() as $index=>$item) {
            placeSubItem($methode, "option", $index, $item);
        }

        // add output items
        foreach ($methode->getOuput() as $index=>$item) {
            placeSubItem($methode, "output", $index, $item);
        }
    }

    function placeSubItem(ObtainingMethode $methode, string $type, int $index, array $item){
        $html = '
        .{methode} .obtaining .{type}-{number} {
            left: {x}%;
            top: {y}%;
            width: {width}%;
            padding-top: {height}%;
        }
        ';
        $html = str_replace("{methode}", $methode->getId(), $html);
        $html = str_replace("{type}", $type, $html);
        $html = str_replace("{number}", $index, $html);
        $html = str_replace("{x}", $item["x"], $html);
        $html = str_replace("{y}", $item["y"], $html);
        $html = str_replace("{width}", $item["size"], $html);
        $html = str_replace("{height}", $item["size"], $html);
        echo $html;
    }
    ?>
</style>

<div class="items">
    <div class="header">
        <h2>Items</h2>
        <label class="searchbar">
            <input type="text" placeholder="Search..." name="searchbar">
        </label>
        <div class = "buttons">
            <div class="button green" onclick="show_selected()">
                <p>Show selected</p>
            </div>
            <div class="button red" onclick="reset_inputs()">
                <p>Reset all</p>
            </div>
        </div>
    </div>
    <div class="categories">
        <?php
        $tabsAmount = 8;

        // sample html
        $sampleStartTab = '<div class="tab"><div class="sub-categories">';
        $sampleEndTab = '</div></div>';
        $sampleHeader = '<h4>{categorie name}</h4>';
        $sampleItem =  '
            <div class="item">
                <label>
                    <div class="gameItem" style="background-position:{location image}" title="{name}"></div>
                    <input type="number" oninput="update_item(this)" name="{id}" required data-tooltip="{name}">
                    <p>{name}</p>
                </label>
            </div>';
        $sampleItem = Minifier::minify_html($sampleItem);
        $startTable = '<div class="itemsTable" id="items">';
        $endTable = '</div>';

        // Calculate all categories
        $categories = [];
        foreach ($items->getItems() as &$item) {
            foreach ($item->getCategories() as $categorie) {
                if (array_key_exists($categorie, $categories)) {
                    $categories[$categorie]["value"] += $item->getValue();
                    $categories[$categorie]["amount new"] += 1;
                    array_push($categories[$categorie]["items"], $item);
                } else {
                    $categories[$categorie] = ["value" => $item->getValue(), "amount new" => 1, "items" => [$item], "tab" => false];
                }
            }
        }

        // Generate tabs
        $tabs = [];
        $tabsCategories = [];
        $minAmountTab = min(20, count($items->getItems())/($tabsAmount * 2));
        for ($tabNumber = 0; $tabNumber < $tabsAmount; $tabNumber++) {

            // Find tab categorie
            $tab = ["value" => false];
            foreach ($categories as $name => &$info) {
                $amount = $info["amount new"];
                if ($amount > 0) {
                    $value = ($info["value"] / $amount * 0.1) + ($amount / count($items->getItems()) * 5);
                    if ($info["tab"] === false && $amount > $minAmountTab && ($tab["value"] < $value || $tab["value"] === false)) {
                        $tab = ["value" => $value, "name" => $name, "info" => &$info];
                    }
                }
            }
            if ($tab["value"] == false) {
                break;
            }

            // Update categories information
            foreach ($tab["info"]["items"] as $item) {
                if (count(array_intersect($item->getCategories(), $tabsCategories)) === 0) {
                    foreach ($item->getCategories() as $categorie) {
                        $categories[$categorie]["amount new"] -= 1;
                        $categories[$categorie]["value"] -= $item->getvalue();
                    }
                }
            }
            array_push($tabs, $tab);
            array_push($tabsCategories, $tab["name"]);
            $tab["info"]["tab"] = true;
        }

        foreach ($tabs as &$tab) {
            $itemsShown = [];

            echo $sampleStartTab;

            // Calculate Subcategories
            $subCategories = [];
            foreach ($tab["info"]["items"] as &$item) {
                foreach ($item->getCategories() as $categorie) {
                    if (array_key_exists($categorie, $subCategories)) {
                        $subCategories[$categorie]["value"] += $item->getvalue();
                        $subCategories[$categorie]["amount new"] += 1;
                        array_push($subCategories[$categorie]["items"], $item);
                    } else {
                        $subCategories[$categorie] = ["value" => $item->getvalue(), "amount new" => 1, "items" => [$item]];
                    }
                }
            }

            // Generate subcategories
            $done = false;
            $itemsOfCategoriePlaced = 0;
            while ($done === false) {
                // Get best subcategorie
                $subCategorie = ["value" => 0];
                foreach ($subCategories as $name => &$info) {
                    $amount = count($info["items"]);
                    if ($info["amount new"] > 7 && !in_array($name, $tabsCategories) && $amount < 80) {
                        $value = $info["value"] / $amount + strlen($name) * 0.0001 + $amount * 0.0001;
                        if ($amount > 20) {
                            $value -= ($amount - 20) * 0.0001;
                        }
                        if ($subCategorie["value"] < $value) {
                            $subCategorie = ["value" => $value, "name" => $name, "info" => &$info];
                        }
                    }
                }

                // Display nothing/everyting/left-over when no good subcategories left
                if ($subCategorie["value"] < 0.0001) {
                    $done = true;
                    if ($itemsOfCategoriePlaced === sizeof($tab["info"]["items"])) {
                        break;
                    } else {
                        if ($itemsOfCategoriePlaced === 0) {
                            $subCategorie = ["name" => ""];
                        } else {
                            $subCategorie = ["name" => "other"];
                        }
                        $subCategorie["info"] = ["items" => []];
                        foreach ($tab["info"]["items"] as &$item) {
                            if (!in_array($item->getid(), $itemsShown)) {
                                array_push($subCategorie["info"]["items"], $item);
                            }
                        }
                    }
                } else if ($subCategorie["info"]["amount new"] === sizeof($tab["info"]["items"]) - $itemsOfCategoriePlaced) {
                    $subCategorie["name"] = "";
                }

                // Place subcategorie
                $html = $sampleHeader;
                $html = str_replace("{categorie name}", ucfirst($subCategorie["name"]), $html);
                echo $html;
                echo $startTable;
                foreach ($subCategorie["info"]["items"] as &$item) {

                    // Update value subcategories
                    if (!in_array($item->getid(), $itemsShown)) {
                        array_push($itemsShown, $item->getid());
                        $itemsOfCategoriePlaced++;
                        foreach ($item->getCategories() as $categorie) {
                            $subCategories[$categorie]["value"] -= $item->getValue();
                            $subCategories[$categorie]["amount new"] -= 1;
                        }
                    }

                    // place item
                    $html = $sampleItem;
                    $html = str_replace("{name}", $item->getName(), $html);
                    $html = str_replace("{id}", $item->getId(), $html);
                    $html = str_replace("{location image}", $item->getLocationImage(), $html);
                    echo $html;
                }
                echo $endTable;
            }

            echo $sampleEndTab;

        }
        ?>
        <div class="searchtab">
            <div class="sub-categories">
                <div class="itemsTable" id="items">
                    <?php
                    foreach ($items->getItems() as $index=>$item) {
                        // place item
                        $html = $sampleItem;
                        $html = str_replace("{name}", $item->getName(), $html);
                        $html = str_replace("{id}", $item->getId(), $html);
                        $html = str_replace("{location image}", $item->getLocationImage(), $html);
                        echo $html;
                    }
                    ?>
                </div>
            </div>
        </div>
    </div>
    <div class="navbar-tabs">
        <?php
        // place tabsbar
        $sampleTab = '<div class="tab-button" onclick="select_tab({number})"><h3>{categorie}</h3></div>';
        for ($number = 0; $number < count($tabs); $number++) {
            $html = $sampleTab;
            $html = str_replace("{categorie}", ucfirst($tabs[$number]["name"]), $html);
            $html = str_replace("{number}", $number, $html);
            echo $html;
        }
        ?>
    </div>
</div>
<div class="resources">
    <div class="header">
        <h2>Resources</h2>
    </div>
    <ul id="resourcesList">
    </ul>
</div>
<div class="guide">
    <div class="header">
        <h2>Guide</h2>
    </div>
    <ul id="guideList">
    </ul>
</div>


<?php

$tool_webpage->endContent();