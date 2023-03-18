<?php
// Open: /library/redirect/redirect.php

class Redirect {
    private static function executeSql($sql){
        $db = new SQLite3($_SERVER['DOCUMENT_ROOT'] . '/resources/redirects.sqlite');
        $db->exec($sql);
        $db->close();
    }

    static public function createDatabase(){
        $sql = "CREATE TABLE Redirect (
            old TEXT,
            new TEXT,
            PRIMARY KEY (old, new)
        );";
        Redirect::executeSql($sql);
    }

    static public function deleteDatabase(){
        $sql = "DROP TABLE Redirect;";
        Redirect::executeSql($sql);
    }

    static public function addAllRedirects(){
        Redirect::addRedirect("/minecraft/", "/games/minecraft");
        Redirect::addRedirect("/minecraft/stacks/", "/tools/1");
        Redirect::addRedirect("/minecraft/resources/", "/tools/2");
        Redirect::addRedirect("/minecraft/nether/", "/tools/3");
        Redirect::addRedirect("/minecraft/mapmaker/", "/tools/4");
        Redirect::addRedirect("/minecraft/smelting/", "/tools/5");
        Redirect::addRedirect("/minecraft/articles/", "/articles");
        Redirect::addRedirect("/minecraftEducation/", "/games/minecraft");
        Redirect::addRedirect("/minecraftEducation/resources/", "/tools/2");
        Redirect::addRedirect("/codcoldwar/", "/games/codcoldwar");
        Redirect::addRedirect("/codcoldwar/randomclassgenerator/", "/tools/6");
        Redirect::addRedirect("/general/", "/games/general");
        Redirect::addRedirect("/general/stacks/", "/tools/7");
        Redirect::addRedirect("/general/articles/", "/articles");
        Redirect::addRedirect("/gtaV/", "/games/gtaV");
        Redirect::addRedirect("/gtaV/cheatcodes/", "/tools/8");
        Redirect::addRedirect("/mindustry/", "/games/mindustry");
        Redirect::addRedirect("/mindustry/resources/", "/tools/9");
    }

    static function addRedirect($old, $new) {
        if (substr($old, -1) == "/"){
            $other_old = substr($old, 0, -1);
        } else {
            $other_old = $old . "/";
        }
        $sql = "INSERT INTO Redirect VALUES ('$old', '$new')";
        Redirect::executeSQL($sql);
        $sql = "INSERT INTO Redirect VALUES ('$other_old', '$new')";
        Redirect::executeSQL($sql);
        
    }

    static public function updateDatabase(){
        Redirect::deleteDatabase();
        Redirect::createDatabase();
        Redirect::addAllRedirects();
    }
    
    static public function getRedirect($url){
        $sql = "SELECT new FROM Redirect WHERE old='$url';";
        $db = new SQLite3($_SERVER['DOCUMENT_ROOT'] . '/resources/redirects.sqlite');
        $ret = $db->query($sql);
        $row = $ret->fetchArray(SQLITE3_ASSOC);
        if ($row === false){
            return false;
        } else {
            return $row["new"];
        }
        $db->close();
    }

}

if (basename(__FILE__) == basename($_SERVER["SCRIPT_FILENAME"])) {
    Redirect::updateDatabase();
}
