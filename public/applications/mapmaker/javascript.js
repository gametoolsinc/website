//***************************************\\
//              FUNCTIONS
//***************************************\\

function stepSelect(index) {
    //Remove all active classes
    for (var i = 0; i < allStepDivs.length; i++) {
        allStepDivs[i].classList.remove("active");
        steps[i].classList.remove("active");
    }
    //Set all active classes
    allStepDivs[index].classList.add("active");
    for (var i = 0; i < steps.length; i++) {
        if (i < index) {
            steps[i].classList.add("active");
        }
    }
    if (allStepDivs[index].getAttribute("data-title") === "Result") {
        matchMap();
    } else {
        stopWorker();
        finshedConvert.style.visibility = "hidden";
        toggleImagePreview(false);
    }
}

function checkFile(e) {
    fileInput.parentElement.parentElement.parentElement.setAttribute("completed", "true");
    steps[0].classList.add("active");
    
    //Image display
    document.querySelector('.image-display').style.display = "block";
    var reader = new FileReader();
    reader.onload = function(event){
        var img = new Image();
        img.onload = function(){
            //Reset sizes
            // var url = URL.createObjectURL(e.target.files[0]);
            // imagedisplay.querySelector('img').src = url;

            canvas.height = 256;
            canvas.width = 256;
            cropCanvas.height = 256;
            cropCanvas.width = 256;

            //Clear canvas
            context.clearRect(0, 0, canvas.width, canvas.height);
            cropContext.clearRect(0, 0, canvas.width, canvas.height);

            //Adjust sizes
            var vRatio = canvas.height / img.height;
            var newHeight = img.height*vRatio;
            var newWidth = img.width*vRatio;

            canvas.height = newHeight;
            canvas.width = newWidth;
            cropCanvas.height = newHeight;
            cropCanvas.width = newWidth;

            //Draw
            context.drawImage(img,0,0, img.width, img.height, 0, 0, newWidth, newHeight);
            cropContext.drawImage(img,0,0, img.width, img.height, 0, 0, newWidth, newHeight);
            cropContext.drawImage(img,0,0, img.width, img.height, 0, 0, cropCanvas.width, cropCanvas.height);

            initCropCanvas();
            setCropCanvas();

        }
        img.src = event.target.result;
    }
    reader.readAsDataURL(event.target.files[0]);

    //Get color data
    var xhr = new XMLHttpRequest();
    xhr.open("POST", "/public/applications/mapmaker/mapmaker.php");
    xhr.setRequestHeader("Content-Type", "application/json");
    xhr.send(JSON.stringify({
        "getdata": true
    }));
    xhr.onreadystatechange = function () {
        if (xhr.readyState == 4 && xhr.status == 200) {
            var response = xhr.response;
            dataColors = JSON.parse(response);
        }
    }
}

function initCropCanvas() {
    if (cropper !== undefined) {
        cropper.destroy();
    }
    var Cropper = window.Cropper;
    cropper = new Cropper(cropCanvas, {
        aspectRatio: 1 / 1,
        dragMode: 'none',
        viewMode: 1,
        autoCropArea: 1,
        preview: '.image',
        minCropBoxWidth: 128,
        minCropBoxHeight: 128
    });
}

function setCropCanvas() {
    var width = parseFloat(document.querySelector('#map-width').value);
    var height = parseFloat(document.querySelector('#map-height').value);
    cropper.setAspectRatio(width/height);
}

function matchMap() {
    //Get cropper canvas with correct width & height
    horizontalAmount = parseFloat(document.querySelector('#map-width').value);
    verticalAmount = parseFloat(document.querySelector('#map-height').value);

    //Get other settings
    var version = document.querySelector('#mc-version').value;
    switch (version) {
        case "latest": colors = dataColors['1.16 Colors']; break;
        case "1.12": colors = dataColors['1.12 Colors']; break;
        case "1.8.1": colors = dataColors['1.8.1 Colors']; break;
        case "1.7.2": colors = dataColors['1.7.2 Colors']; break;
    }

    //Getting pixel data
    if (cropper !== undefined) {
        var tempCanvas = cropper.getCroppedCanvas({
            width: horizontalAmount * 128,
            height: verticalAmount * 128,
            imageSmoothingEnabled: false
        });

        //Separate map data
        var tempContext = tempCanvas.getContext("2d");
        var mapsData = tempContext.getImageData(0,0, horizontalAmount * 128, verticalAmount * 128); 
        // var mapsData = [];
        // for (var x = 0; x < horizontalAmount; x++) {
        //     for (var y = 0; y < verticalAmount; y++) {
        //         var tempCanvasData = tempContext.getImageData(x * 128, y * 128, 128, 128);
        //         maps[maps.length] = tempCanvasData.data;
        //     }
        // }

        
        allColorMatches = matchColors(mapsData);
    }
}

function matchColors(maps) {
    var progress = 0;
    results = [];
    progressBarContainer.style.visibility = "visible";
    finshedConvert.style.visibility = "hidden";
    progressBar.style.transition = "none";
    progressBar.style.width = "0%";

    //Worker for calculating
    if(typeof(Worker) !== "undefined") {
        if (typeof(w) !== "undefined") {
            w.terminate();
            w = undefined;
        }
        if(typeof(w) == "undefined") {
            w = new Worker("/public/applications/mapmaker/mapmaker.js?v=" + Date.now());
            w.postMessage({
                'imgdata': maps, 
                'colors': colors
            });
        }
        w.onmessage = function(event) {
            // console.log(event.data);
            if (typeof(event.data) == "number") { //Progress update
               progress += event.data;
               progressBar.style.transition = "width .1s ease";
               progressBar.style.width = Math.round(progress * 100) + "%";
            } else if (typeof(event.data) == "object") {
                results = event.data.matches;
                
                //Update webpage
                progressBarContainer.style.visibility = "hidden";
                finshedConvert.style.visibility = "visible";
                var allSpanAmounts = document.querySelectorAll('#map-amount');
                for (var i = 0; i < allSpanAmounts.length; i++) {
                    allSpanAmounts[i].textContent = results.length;
                }

                var allPlural = document.querySelectorAll('plural');
                for (var i = 0; i < allPlural.length; i++) {
                    if (results.length > 1) {
                        allPlural[i].textContent = "s";
                    } else {
                        allPlural[i].textContent = "";
                    }
                }

                //Create converted image
                var imgdata = event.data.imgdata;

                //Create temp canvas to extract data from
                var tempCanvas = document.createElement("canvas");
                tempCanvas.width = horizontalAmount * 128;
                tempCanvas.height = verticalAmount * 128;
                var resultContext = tempCanvas.getContext("2d");

                //Draw the image map by map on the canvas
                var imageData = new ImageData(imgdata, horizontalAmount * 128);
                resultContext.putImageData(imageData, 0, 0);

                //Set converted colors as the data for the preview image
                targetImage.src = tempCanvas.toDataURL();

                //Same size for converted color image
                targetImage.style.width = previewImage.style.width;
                targetImage.style.height = previewImage.style.height;

                toggleImagePreview(true);

                return results;
            }
        };
    } else {
        alert("Your browser does not support web workers. Your image could not be converted");
    }
}

function toggleImagePreview(visible) {
    if (visible) {
        targetImage.style.display = "block";
        previewImage.style.display = "none";
    } else {
        targetImage.style.display = "none";
        previewImage.style.display = "block";
    }
}

function stopWorker() {
    if (typeof(w) !== "undefined") {
        w.terminate();
    }
}

function createMapFile() {
    if (results.length > 2000) {
        alert("Maximum amount of maps to download is reached. Max is 2000");
        return;
    }
    if (results.length >= 0 && mapAmountInput.value !== "") {
        mapAmount = mapAmountInput.value;
        var xhr = new XMLHttpRequest();
        xhr.open("POST", "/public/applications/mapmaker/mapmaker.php");
        xhr.setRequestHeader("Content-Type", "application/json");
        xhr.responseType = "blob";
        xhr.send(JSON.stringify({
            "colors": results,
            "latest map number": mapAmount,
            "version": colors["version"]
        }));
        xhr.onreadystatechange = function () {
            if (xhr.readyState == 4 && xhr.status == 200) {
                var response = xhr.response;
                // console.log(response);
                
                try {
                    //Download data
                    const link = document.createElement("a");
                    link.href = URL.createObjectURL(response);
                    if (results.length > 1) {
                        link.download = "maps.zip";
                    } else {
                        link.download = "map_" + mapAmount + ".dat";
                    }
                    link.click();
                    link.remove();
                } catch (e) {
                    console.error("An error occured when downloading the zip file from the server. Error: " + e);
                    return;
                }
            }
        }
    } else if (!(mapAmountInput.value !== "")) {
        alert("Please enter your latest map_#.dat number (step 4).");
    }
}

function tabSelect(clickedElement, index) {
    var parent = clickedElement.parentElement.parentElement;
    var allSelectors = parent.querySelectorAll('.tab-selector > *');
    var allElements = parent.querySelectorAll('.tab-item');
    allSelectors.forEach((element) => {
        element.classList.remove("selected"); 
    });
    allElements.forEach((element) => {
        element.classList.remove("active"); 
    });
    allElements[index].classList.add("active");
    clickedElement.classList.add("selected");
}

//****************************************\\
//                  MAIN
//****************************************\\
var stepSelector = document.querySelector('.step-selector');
var allStepDivs = document.querySelectorAll('.step-content');
var stepsDiv = document.querySelector('.steps');
var steps = stepSelector.children;
for (var i = 0; i < allStepDivs.length; i++) {
    stepSelector.innerHTML += '<div class="step" onclick="stepSelect(' + i + ')"><span>' + (i + 1) + '</span><p>' + allStepDivs[i].getAttribute("data-title") + '</p></div>';
}
var onclickAdd = 'onclick="stepSelect(' + i + ')"';

var fileInput = document.querySelector('#upload-file');
fileInput.addEventListener('change', checkFile, false);

//Img upload display canvas
var canvas = document.querySelector('#image');
var context = canvas.getContext("2d");

//Cropping canvas
var cropCanvas = document.querySelector('#crop-canvas');
var cropContext = cropCanvas.getContext("2d");

var cropper;

//Result canvas
var previewImage = document.querySelector('.image');
var targetImage = document.querySelector("#image_converted");

//Matching colors
var progressBarContainer = document.querySelector('#progress-bar');
var finshedConvert = document.querySelector('#finished-convert');
var progressBar = document.querySelector('#progress-bar #slider');
var w;
var v = 0;
var results = [];
var dataColors;

//Other elements
var mapAmount = 0;
var mapAmountInput = document.querySelector('#latest-map-amount');

var horizontalAmount;
var verticalAmount;