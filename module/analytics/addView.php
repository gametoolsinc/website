<?php
try {
    $data = json_decode(stripslashes(file_get_contents("php://input")), true);
    $url = $data['url'];

    // Remove 'https://gamertools.net/' from string
    $url = explode("/", $url, 4);
    $url = $url[3];

    // Remove query's from string
    $url = explode("?", $url)[0];
    $url = explode("#", $url)[0];

    $today = date("Y-m-d");

    $page_views_path = $_SERVER['DOCUMENT_ROOT'] . '/resources/views.json';

    $file = fopen($page_views_path, 'r+');
    flock($file, LOCK_EX);

    $page_views = json_decode(fread($file, filesize($page_views_path)), true);

    if (!array_key_exists($today, $page_views)) {
        $page_views[$today] = [];
    }
    if (!array_key_exists($url, $page_views[$today])) {
        $page_views[$today][$url] = 0;
    }

    $page_views[$today][$url] += 1;
    $data = json_encode($page_views);

    ftruncate($file, 0);
    rewind($file);
    fwrite($file, $data);
    fflush($file);
    
    flock($file, LOCK_UN);
    fclose($file);

    http_response_code(200);
} catch (Exception $e) {
    echo $e->getMessage();
    http_response_code(500);
}