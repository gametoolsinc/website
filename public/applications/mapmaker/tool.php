<?php
include_once($_SERVER['DOCUMENT_ROOT'] . "/library/server/template/tool/toolWebpage.php");
$tool_webpage = new ToolWebpage($_GET["id"]);
$tool_webpage->startContent();
?>


<link href="/library/client/cropper/cropper.css" rel="stylesheet">
<script src="/library/client/cropper/cropper.js">
    var cropper;
    window.addEventListener('DOMContentLoaded', function() {
        cropper = new Cropper(cropCanvas, {
            aspectRatio: 1 / 1,
            dragMode: 'none',
            autoCropArea: 1,
            preview: resultCanvas
        });
    }, false);
</script>

<div class="map-generate">
    <div class="step-selector">

    </div>
    <div class="steps">


        <div class="step-content active" data-title="Upload">
            <div class="upload">
                <div>
                    <h2>Step 1: Upload your image</h2>
                    <input type="file" id="upload-file" oninput="checkFile(this)" accept="image/*">
                </div>
            </div>
            <div class="button next" onclick="stepSelect(1)">Next</div>
        </div>


        <div class="step-content" data-title="Crop" completed="true">
            <div>
                <h2>Step 2: Crop & divide your image</h2>
                <h3 data-tooltip="By splitting your image into multiple parts, you can create a bigger map (instead of 1x1)">Amount of horizontal and vertical maps</h3>
                <span class="input-field">Height: <input type="number" id="map-height" min="1" value="1" oninput="setCropCanvas()"></span>
                <span class="input-field">Width: <input type="number" id="map-width" min="1" value="1" oninput="setCropCanvas()"></span>
            </div>

            <div class="crop-container">

                <div style="display:block;"><canvas id="crop-canvas" width="256" style="max-width: 100%"></canvas></div>
            </div>

            <div class="button next" onclick="stepSelect(2)">Next</div>
            <div class="button back" onclick="stepSelect(0)">Back</div>
        </div>



        <div class="step-content settings" data-title="Settings" completed="true">
            <h2>Step 3: Choose settings</h2>

            <h3>Color settings</h3>
            <span class="input-field">
                Minecraft Version: 
                <select data-tooltip="To get the correct colors, choose your Minecraft version" id="mc-version">
                    <option value="latest">1.17.1 (latest)</option>
                    <option value="latest">1.16</option>
                    <option value="1.12">1.12</option>
                    <option value="1.8.1">1.8.1</option>
                </select>
            </span>
            <!--<span class="input-field">
                Map surface type:
                <select data-tooltip="Staircasing offers 3x more colors, but it's is harder to build (3D shape)">
                    <option value="3d">3D (staircasing)</option>
                    <option value="2d">2D (flat map)</option>
                </select>
            </span>-->

            <h3>Map settings</h3>
            <span class="input-field">
                Dimension
                <select>
                    <option value="0">Overworld</option>
                    <option value="-1">Nether</option>
                    <option value="1">End</option>
                </select>
            </span>

            <div class="button next" onclick="stepSelect(3)">Next</div>
            <div class="button back" onclick="stepSelect(1)">Back</div>
        </div>



        <div class="step-content" data-title="Result">
            <h2>Step 4: Result</h2>

            <div class="progress-container">
                <div id="progress-bar">
                    <span id="slider"></span>
                </div>
                <div id="finished-convert">
                    <div class="tab-container">
                        <div class="tab-selector">
                            <span onclick="tabSelect(this, 0)" class="selected">Download map (map.dat)</span>
                            <!--<span onclick="tabSelect(this, 1)">Download .schematic</span>
                            <span onclick="tabSelect(this, 2)">View build instructions</span>-->
                        </div>
                        <div class="tabs">
                            <div class="tab-item active">
                                <ol>
                                    <li>Create <span id="map-amount"></span> regular map<plural></plural> in Minecraft (they will later be replaced with your own custom image).</li>
                                    <li>Save & exit your world</li>
                                    <li>Go to your Minecraft world save folder (click <a href="https://www.howtogeek.com/207484/how-to-find-your-minecraft-saved-games-folder/" target="_blank">here</a> for instructions).</li>
                                    <li>Navigate to your <i>data</i> folder. Check your latest map_#.dat file<plural></plural>.</li>
                                    <li>Enter the number of your latest map_#.dat file (i.e. enter <i>56</i> for <i>map_56.dat</i>).</li>
                                    <div class="input-field">
                                        Latest map_#.dat number:
                                        <input type="number" id="latest-map-amount" min="0">
                                    </div>
                                    <li>Download the file (unzip if necessary) and replace the map_#.dat file<plural></plural> in your save folder with the new one<plural></plural>.</li>
                                    <li>Open Minecraft and enjoy your new map<plural></plural>!</li>
                                    <li style="list-style:none"><div class="result-option" onclick="createMapFile()">Download file (map.dat / maps.zip)</div></li>
                                </ol>
                            </div>
                            <!--<div class="tab-item">
                                <div class="result-option">Download schematic (.schematic)</div>
                            </div>
                            <div class="tab-item">
                                <div class="result-option">Build instructions</div>
                            </div>-->
                        </div>
                    </div>
                </div>
            </div>

            <div class="button back" onclick="stepSelect(2)">Back</div>
        </div>
    </div>
    <div class="image-display">
        <h2>Your image</h2>
        <div class="image">
            <canvas id="image" width="256"></canvas>
        </div>
        <div>
            <img id="image_converted" width="256">
        </div>
    </div>
</div>

<?php
$tool_webpage->endContent();