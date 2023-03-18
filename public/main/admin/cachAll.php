<?php
include_once $_SERVER['DOCUMENT_ROOT'] . "/library/server/sitemap/sitemap.php";


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
        // Generate get request for every page
        foreach ($xml->url as $item) {
            $url = parse_url($item->loc);
            if (array_key_exists("path", $url)){
                $url["path"] = "/cach" . $url["path"];
            } else {
                $url["path"] = "/cach";
            }
            $url = build_url($url);
            echo "cachUrl('$url', 'manual');\n";
            // echo "await sleep(500);\n";
        }

        function build_url(array $parts)
        {
            return (isset($parts['scheme']) ? "{$parts['scheme']}:" : '') .
                ((isset($parts['user']) || isset($parts['host'])) ? '//' : '') .
                (isset($parts['user']) ? "{$parts['user']}" : '') .
                (isset($parts['pass']) ? ":{$parts['pass']}" : '') .
                (isset($parts['user']) ? '@' : '') .
                (isset($parts['host']) ? "{$parts['host']}" : '') .
                (isset($parts['port']) ? ":{$parts['port']}" : '') .
                (isset($parts['path']) ? "{$parts['path']}" : '') .
                (isset($parts['query']) ? "?{$parts['query']}" : '') .
                (isset($parts['fragment']) ? "#{$parts['fragment']}" : '');
        }
        ?>
    }
    cachAll();
</script>

