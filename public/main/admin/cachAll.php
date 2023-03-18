<?php
include_once $_SERVER['DOCUMENT_ROOT'] . "/library/sitemap/sitemap.php";


// Get all webpages
$sitemap = new Sitemap();
$output = $sitemap->getXml();
$xml = simplexml_load_string($output);

?>
<script src="/module/caching/cach.js"></script>
<div id="output">

</div>
<script>
    async function cachAll() {
        <?php
        // Generate request for every page
        foreach ($xml->url as $item) {
            echo "cachUrl('{$item->loc}?type=cach', 'manual');\n";
            // echo "await sleep(500);\n";
        }
        ?>
    }
    cachAll();
</script>

