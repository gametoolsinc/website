importScripts("/library/client/colours/colours.js");

onmessage = function(e) {
    colors = e.data.colors.colors;

    imgdata = e.data.imgdata;
    data = e.data.imgdata.data;
    if (colors !== undefined && data !== undefined) {
        console.time('execution');

        horizontalAmount = imgdata.width / 128;
        verticalAmount = imgdata.height / 128;

        //Procent to add each time a map is finished
        procentAdd = 1 / (horizontalAmount * verticalAmount * progressUpdatesPerMap);

        allMatches = [];
        matchColors();

        //Check for new part of array which can be checked
        postMessage({"matches": allMatches, "imgdata": data});
        console.timeEnd('execution');
    }
};

var colors;
var allMatches = [];
var imgdata;
var data;
var mapIndex = 0;
var transparencyThreshold = 50;
var progressUpdatesPerMap = 3;
var procentAdd;

var horizontalAmount, verticalAmount;

function matchColors() {
    //Create empty arrays
    for (var i = 0; i < horizontalAmount * verticalAmount; i++) {
        allMatches[i] = [];
    }

    for (var i = 0; i < data.length; i += 4) {
        //Determine map x and y
        var pixelIndex = i / 4;
        var pixelX = pixelIndex % imgdata.width;
        var mapX = Math.floor(pixelX / 128);

        var pixelY = Math.floor(pixelIndex / imgdata.width);
        var mapY = Math.floor(pixelY / 128);

        //Determine mapId for this pixel
        var mapId = mapX + mapY * verticalAmount;

        //Process pixel
        var red = data[i];
        var green = data[i + 1];
        var blue = data[i + 2];
        var alpha = data[i + 3];

        var match = colors.matchColor(red, green, blue, alpha);
        
        var targetArr = allMatches[mapId];
        allMatches[mapId][targetArr.length] = match.id;


        var rgba = match.rgba;
        data[i    ] = rgba[0];
        data[i + 1] = rgba[1];
        data[i + 2] = rgba[2];
        data[i + 3] = rgba[3];

        floyd_steinberg_dither(i, [red, green, blue], rgba, pixelX, pixelY);

        if (pixelIndex % Math.round( 128**2 * (1 / progressUpdatesPerMap) ) == 0) { //Determine when progress update is sent
            postMessage(procentAdd);
        }
    }

    //Add to progress bar
    postMessage(procentAdd);
}
//V2
Array.prototype.matchColor = function(r, g, b, a) {
    if (a <= transparencyThreshold) {
        return {"id": 0, "rgba": [0,0,0,a]};
    } else {
        var minNumber = Infinity;
        var minNumberId = 0;
        var bestRgb = [];
        var color = new Colour(Colour.RGBA, [r, g, b, a] );
        for (var i = 4; i < this.length; i++) {
            var rgba = this[i].rgba;
            var colorMatch = new Colour(Colour.RGBA, 
                [rgba[0], rgba[1], rgba[2], rgba[3]]
            );
            var rgbDifference = color.distanceTo(colorMatch);
            if (rgbDifference < minNumber) {
                minNumber = rgbDifference;
                minNumberId = parseInt(this[i].id);
                bestRgb = rgba;
            }
        }
        // if ((minNumberId >= 52 && minNumberId <= 55) || (minNumberId >= 100 && minNumberId <= 103) || (minNumberId >= 128 && minNumberId <= 131)) {
        //     console.log(r,g,b,a, bestRgb, minNumberId);
        // }
        return {"id": minNumberId, "rgba": bestRgb};
    }
}

//Clamp number between 2 values (min and max)
Number.prototype.clamp = function(min, max) {
    return Math.min(Math.max(this, min), max);
}

function floyd_steinberg_dither(pixelIndex, oldPixel, newPixel, x, y) {
    var width = 128 * 4;
    var height = 128 * 4;

    // var y = Math.floor(pixelIndex / height) + 1; //1-128
    // var x = pixelIndex % width; //0-508

    var quantError = [
        oldPixel[0] - newPixel[0],
        oldPixel[1] - newPixel[1],
        oldPixel[2] - newPixel[2]
    ];

    // var quantError = [
    //     (oldPixel[0] - newPixel[0]).clamp(-10, 10),
    //     (oldPixel[1] - newPixel[1]).clamp(-10, 10),
    //     (oldPixel[2] - newPixel[2]).clamp(-10, 10)
    // ];

    //Apply factors for blue and red pixels to prevent random pixels
    // quantError[0] *= 0.7; //RED
    // quantError[2] *= 0.7; //BLUE

    //[x+1] [y  ]
    if (x !== width - 4) {
        data[pixelIndex + 4] += quantError[0] * (7 / 16);
        data[pixelIndex + 5] += quantError[1] * (7 / 16);
        data[pixelIndex + 6] += quantError[2] * (7 / 16);

        // data[mapIndex][pixelIndex + 4] = data[mapIndex][pixelIndex + 4].clamp(0,255);
        // data[mapIndex][pixelIndex + 5] = data[mapIndex][pixelIndex + 5].clamp(0,255);
        // data[mapIndex][pixelIndex + 6] = data[mapIndex][pixelIndex + 6].clamp(0,255);
    }

    //[x-1] [y+1]
    if (x > 0 && y !== height / 4) {
        data[pixelIndex - 4 + width] += quantError[0] * (3 / 16);
        data[pixelIndex - 3 + width] += quantError[1] * (3 / 16);
        data[pixelIndex - 2 + width] += quantError[2] * (3 / 16);

        // data[mapIndex][pixelIndex - 4 + width] = data[mapIndex][pixelIndex - 4 + width].clamp(0,255);
        // data[mapIndex][pixelIndex - 3 + width] = data[mapIndex][pixelIndex - 3 + width].clamp(0,255);
        // data[mapIndex][pixelIndex - 2 + width] = data[mapIndex][pixelIndex - 2 + width].clamp(0,255);
    }

    //[x  ] [y+1]
    if (y !== height / 4) {
        data[pixelIndex + 0 + width] += quantError[0] * (5 / 16);
        data[pixelIndex + 1 + width] += quantError[1] * (5 / 16);
        data[pixelIndex + 2 + width] += quantError[2] * (5 / 16);

        // data[mapIndex][pixelIndex + 0 + width] = data[mapIndex][pixelIndex + 0 + width].clamp(0,255);
        // data[mapIndex][pixelIndex + 1 + width] = data[mapIndex][pixelIndex + 1 + width].clamp(0,255);
        // data[mapIndex][pixelIndex + 2 + width] = data[mapIndex][pixelIndex + 2 + width].clamp(0,255);
    }

    //[x+1] [y+1]
    if (x !== width - 4 && y !== height / 4) {
        data[pixelIndex + 4 + width] += quantError[0] * (1 / 16);
        data[pixelIndex + 5 + width] += quantError[1] * (1 / 16);
        data[pixelIndex + 6 + width] += quantError[2] * (1 / 16);

        // data[mapIndex][pixelIndex + 4 + width] = data[mapIndex][pixelIndex + 4 + width].clamp(0,255);
        // data[mapIndex][pixelIndex + 5 + width] = data[mapIndex][pixelIndex + 5 + width].clamp(0,255);
        // data[mapIndex][pixelIndex + 6 + width] = data[mapIndex][pixelIndex + 6 + width].clamp(0,255);
    }
}



//OLD FUNCTIONS
function matchColor2(colors, r, g, b, a) {
    if (a <= transparencyThreshold) {
        return {"id": 0, "rgba": [0,0,0,a]};
    } else {
        // r = r.clamp(0,255);
        // g = g.clamp(0,255);
        // b = b.clamp(0,255);
        var minNumber = Infinity;
        var minNumberId = 0;
        var bestRgb = [];
        for (var i = 4; i < colors.length; i++) {
            var rgba = colors[i].rgba;
            var rgbDifference = 
                ((rgba[0]-r)*3)**2 + 
                ((rgba[1]-g)*6)**2 + 
                ((rgba[2]-b)  )**2
            ;
            var rgbDifference = rgba.distanceTo(r,g,b);
            if (rgbDifference < minNumber) {
                minNumber = rgbDifference;
                minNumberId = colors[i].id;
                bestRgb = rgba;
            }
        }
        return {"id": minNumberId, "rgba": bestRgb};
    }
}