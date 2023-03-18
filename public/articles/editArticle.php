<?php
include_once($_SERVER['DOCUMENT_ROOT'] . "/library/articles/article.php");

$id = $_GET["id"];
$article = new Article($id);
?>

<h1>Edit article '<?= $article->getTitle() ?>'</h1>
<form method='post' id='form' action='/public/articles/post/updateArticle.php'>
    <input type='number' name='id' placeholder='id' value=<?=$id?>>
    <input type='text' name='title' placeholder='title' value=<?=$article->getTitle()?>>
    <input type='submit'>
    <hr>
    <div id="content">


        <?php

        $template = "
<div style='display: {display};' {id}>
    <h2>{number}</h2>
    <select name='type-{number}' selected='{selected}'>{options}</select>
    <br>
    <textarea oninput='auto_grow(this)' type='text' class='content' style='width: 100%;' name='content-{number}' placeholder='content' />{text}</textarea>
</div>
";
        $refl = new ReflectionClass('ArticlePartTypes');
        $options = $refl->getConstants();
        $html_options = "";
        foreach ($options as $name => $value) {
            $html_options .= "<option value='{$value}'>{$name}</option>";
        }
        $template = str_replace("{options}", $html_options, $template);

        foreach ($article->getArticleParts() as $article_part) {
            $article = str_replace("{number}", $article_part->getPosition(), $template);
            $article = str_replace("{text}", $article_part->getContent(), $article);
            $article = str_replace("{display}", "block", $article);
            $article = str_replace("{id}", "", $article);
            $article = str_replace("value='{$article_part->getType()}'", "value='{$article_part->getType()}' selected", $article);
            echo $article;
        }

        

        ?>
    </div>
</form>
<p>New part:
    <button onclick='newPart()'>Insert</button>
</p>

<?php
$javascript_html_template = str_replace("{text}", "", $template);
$javascript_html_template = str_replace("{display}", "none", $javascript_html_template);
$javascript_html_template = str_replace("{id}", "id='template'", $javascript_html_template);
echo $javascript_html_template;
?>

<script src="/public/articles/javascript.js"></script>