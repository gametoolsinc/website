<?php
include_once($_SERVER['DOCUMENT_ROOT'] . "/library/server/template/tool/toolWebpage.php");
$tool_webpage = new ToolWebpage($_GET["id"]);
$tool_webpage->startContent();
?>

<div class="converter-container">
    <div class="upload-container">
        <h2>Upload your file</h2>
        <div class="toolbar">
            <input type="file" id="upload-file" accept=".dat, .schem, .schematic" oninput="openFile()">
            <button onclick="saveFile()" id="convert">Save</button>
        </div>
    </div>
    <div class="result-container">
        <h2>Result</h2>
        <div class="result-tree">
            <p>Nothing here yet. Upload a NBT file to edit it here.</p>
        </div>
    </div>
</div>

<?php
$tool_webpage->endContent();