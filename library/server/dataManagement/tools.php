<?php
// Open /library/server/dataManagement/tools.php
class Tools
{
    private static function executeSql($sql)
    {
        $db = new SQLite3($_SERVER['DOCUMENT_ROOT'] . '/resources/tools/tools.sqlite');
        $db->exec($sql);
        $db->close();
    }

    static public function createDatabase()
    {
        $sql = "CREATE TABLE Tool (
            id INTEGER PRIMARY KEY,
  			released BIT NOT NULL,
            game TEXT NOT NULL,
            application TEXT NOT NULL,
            subject TEXT NOT NULL
        );";
        Tools::executeSql($sql);
    }

    static public function deleteDatabase()
    {
        $sql = "DROP TABLE Tool;";
        Tools::executeSql($sql);
    }

    static public function addAllTools()
    {
        Tools::addTool(1, 1, 'minecraft', 'unit', 'items');
        Tools::addTool(2, 1, 'minecraft', 'resources', '-');
        Tools::addTool(3, 1, 'minecraft', 'nether', '-');
        Tools::addTool(4, 1, 'minecraft', 'mapmaker', '-');
        Tools::addTool(5, 1, 'minecraft', 'smelting', '-');
        Tools::addTool(6, 1, 'codcoldwar', 'randomclassgenerator', '-');
        Tools::addTool(7, 1, 'general', 'unit', 'time');
        Tools::addTool(8, 1, 'gtaV', 'cheatcodes', '-');
        Tools::addTool(9, 1, 'mindustry', 'resources', '-');
        Tools::addTool(10, 1, 'minecraft', 'direction', '-');
        Tools::addTool(11, 0, 'minecraft', 'voxelizer', '-');
        Tools::addTool(12, 0, 'minecraft', 'commands', '-');
        Tools::addTool(13, 1, 'minecraft', 'nbt_converter', '-');
        Tools::addTool(14, 1, 'astroneer', 'resources', '-');
        Tools::addTool(15, 1, 'general', 'unit', 'letters');
        Tools::addTool(16, 1, 'yugioh', 'scoreboard', '');
        Tools::addTool(17, 1, 'minecraft', 'unit', 'transportation');
        Tools::addTool(18, 1, 'general', 'HBT', '');
        Tools::addTool(19, 1, 'general', 'sorting', '');
        Tools::addTool(20, 1, 'general', 'colourPicker', '');
    }

    static function addTool($id, $released, $game, $application, $subject)
    {
        $sql = "INSERT INTO Tool VALUES ($id, $released, '$game', '$application', '$subject')";
        Tools::executeSql($sql);
    }

    static public function executeAll()
    {
        Tools::deleteDatabase();
        Tools::createDatabase();
        Tools::addAllTools();
    }
}


// Testing
if (basename(__FILE__) == basename($_SERVER["SCRIPT_FILENAME"])) {
    Tools::executeAll();
}