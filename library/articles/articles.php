<?php
include_once($_SERVER['DOCUMENT_ROOT'] . "/library/dataManagement/game.php");
include_once($_SERVER['DOCUMENT_ROOT'] . "/library/articles/article.php");

class Articles
{
    private static $databaseLocation = '/resources/articles/articles.sqlite';

    public static function setup(){
        $sql = "DROP TABLE article;
        DROP TABLE article_part";
        Articles::executeSql($sql);
        $sql = "CREATE TABLE article (
            id INTEGER PRIMARY KEY,
            title TEXT NOT NULL
          );";
        Articles::executeSql($sql);
        $sql = "CREATE TABLE article_part (
            article_id INTEGER NOT NULL,
            position INTEGER NOT NULL,
            type text NOT NULL,
            content text NOT NULL,
            PRIMARY KEY (article_id, position)
          );";
        Articles::executeSql($sql);
        $sql = "INSERT INTO article VALUES (1, 'The first ever article (again)');
        INSERT INTO article_part VALUES (1, 0, 'text', 'hello world');";
        Articles::executeSql($sql);
    }

    public static function executeSql($sql)
    {
        $db = new SQLite3($_SERVER['DOCUMENT_ROOT'] . Articles::$databaseLocation);
        $db->exec($sql);
        $db->close();
    }

    public static function querySql($sql): SQLite3Result
    {
        $db = new SQLite3($_SERVER['DOCUMENT_ROOT'] . Articles::$databaseLocation);
        $result = $db->query($sql);
        return $result;
    }

    public function __construct()
    {
    }

    /**
     * @return Article[]
     */
    static function getAllArticles(): array
    {
        $sql = "SELECT id FROM article ORDER BY id DESC";
        $result = Articles::querySql($sql);
        $articles = [];
        while ($row = $result->fetchArray(SQLITE3_ASSOC)) {
            $article = new Article($row["id"]);
            array_push($articles, $article);
        }
        return $articles;
    }

    static function saveArticle(array $content, int $id = null): int    
    {
        if ($id == null){
            $id = time();
        }
        $title = "";
        $articleParts = [];

        // read content
        foreach ($content as $type => $item) {
            if (trim($item) == "") {
                continue;
            }

            if ($type == "title") {
                $title = $item;
            } else if (strpos($type, "type") !== false) {
                $position = explode("-", $type)[1];
                if (!array_key_exists($position, $articleParts)) {
                    $articleParts[$position] = [];
                }
                $articleParts[$position]["type"] = $item;
            } else if (strpos($type, "content") !== false) {
                $position = explode("-", $type)[1];
                if (!array_key_exists($position, $articleParts)) {
                    $articleParts[$position] = [];
                }
                $articleParts[$position]["content"] = $item;
                
            }
        }

        // Save data
        $sql = "INSERT INTO article (id, title) VALUES ({$id}, '{$title}')";
        Articles::executeSql($sql);
        foreach ($articleParts as $position => $articlePart) {
            $type = $articlePart['type'];
            $content = str_replace("'", "''", $articlePart["content"]);
            $sql = "INSERT INTO article_part (article_id, position, type, content) VALUES ({$id}, {$position}, '{$type}', '{$content}')";
            Articles::executeSql($sql);
        }

        return $id;
    }

    static function deleteArticle(int $id){
        $sql = "DELETE FROM article WHERE id='{$id}';";
        Articles::executeSql($sql);
        $sql = "DELETE FROM article_part WHERE article_id='{$id}';";
        Articles::executeSql($sql);
    }
}
