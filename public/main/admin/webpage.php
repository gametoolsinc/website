<?php
include_once $_SERVER['DOCUMENT_ROOT'] . "/library/server/sitemap/sitemap.php";


// Get all webpages
$sitemap = new Sitemap();
$output = $sitemap->getXml();
$xml = simplexml_load_string($output);
?>

<!DOCTYPE html>

<html>

<head>
    <title>Admin Controls</title>
    <link rel='stylesheet' type='text/css' href='/public/main/admin/stylesheet.css'>
    <script src="/public/main/admin/javascript.js"></script>
</head>

<body>
    <h1>Admin Controls</h1>
    <h2>Page views</h2>
    <a href="/public/main/pageViews/webpage.php">Webpage</a>
    <h2>Articles</h2>
    <input type='button' value='New article' onclick='window.open("/public/articles/post/newArticle.php")' />
    <input type='button' value='Edit article' onclick='window.open("/public/articles/editArticle.php?id="+window.prompt("Enter article id"))' />
    <input type='button' value='Delete article' onclick='window.open("/public/articles/post/deleteArticle.php?id="+window.prompt("Enter article id"))' />
    <h2>Caching Controls</h2>
    <input type='button' value='Recach all webpages' onclick='window.open("/public/main/admin/cachAll.php")' />
    <h3>Cached webpages</h3>
    <table>
        <thead>
            <tr>
                <th>Title</th>
                <th>Last update</th>
                <th>Location</th>
                <th>Refresh cach</th>
            </tr>
        </thead>
        <tbody>

            <?php
            // Templates
            $template = "
            <tr>
                <td>{title}</td>
                <td>{last update}</td>
                <td><a href='{location}' target='_blank' rel='noopener noreferrer'>Open webpage</a></td>
                <td><a href='{refrechCache}' target='_blank' rel='noopener noreferrer'>Refresh</a></td>
            </tr>";

            // Display webpages
            $root = $_SERVER['DOCUMENT_ROOT'] . "/cachedWebpages/";
            $scanned_directory = getDirContents($root);
            $host = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]";
            foreach ($scanned_directory as $cachedWebpageLocation) {
                $html = $template;

                $path = rtrim(explode($root, $cachedWebpageLocation)[1], "-");
                $url = $host . $path;
                $lastUpdate = filemtime($cachedWebpageLocation);

                $html = str_replace("{title}", $path, $html);
                $html = str_replace("{location}", $url, $html);
                $html = str_replace("{last update}", date("d-m-Y H:i:s", $lastUpdate), $html);
                $html = str_replace("{refrechCache}", $host . "/cach" . $path, $html);
                echo $html;
            }
            ?>

        </tbody>
    </table>
</body>

</html>

<?php

function getDirContents($dir, &$results = array()) {
    $files = scandir($dir);

    $subfolders = false;
    foreach ($files as $key => $value) {
        $path = $dir . "/" . $value;
        if (is_dir($path) && $value != "." && $value != "..") {
            getDirContents($path, $results);
            $subfolders = true;
        }
    }
    if (!$subfolders){
        $results[] = $dir;
    }

    return $results;
}
