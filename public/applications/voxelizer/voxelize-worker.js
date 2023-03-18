importScripts("/library/client/colours/colours.js");

var colors, blocks, data;
var colorImportance = 2;

onmessage = function(e) {
    data = JSON.parse(e.data.voxels);
    colors = e.data.colors;
    console.log(colors);
    blocks = e.data.blocks;

    //Match the colors
    console.time('Matching voxel colors');
    var matched = matchColors(data);
    console.timeEnd('Matching voxel colors');
    postMessage(matched);
};

function matchColors(voxels) {
    var collection = {};
    for (var i = 0; i < voxels.length; i++) {
        var r = voxels[i].voxel.color.r;
        var g = voxels[i].voxel.color.g;
        var b = voxels[i].voxel.color.b;
        
        var match = colors.matchColor2(r,g,b,255);
        voxels[i].voxel.bestBlockName = match.name;
        if (collection.hasOwnProperty(match.name)) {
            collection[match.name] += 1;
        } else {
            collection[match.name] = 1;
        }
        voxels[i].voxel.id = match.id;
        // voxels[i].voxel.color = match.rgba;
    }
    console.log(collection);
    console.log(Object.keys(collection).length + " different blocks");
    return voxels;
}

/*
**********************************************************
COLOR/TEXTURE MATCHING ALGORITHMS
**********************************************************
*/

//V1: multiplying rgbdifference and factor
Array.prototype.matchColor = function(r, g, b, a) {
    var minNumber = Infinity;
    var minNumberName = 0;
    var bestRgb = [];
    var color = new Colour(Colour.RGBA, [r, g, b, a] );
    for (var i = 4; i < this.length; i++) {
        var rgba = this[i].average;
        var name = this[i].name;
        /* Takes a lot of time
        if (name.includes('Concrete Powder') || name.includes('Anvil') || name.includes('Shulker Box')) {continue;} */
        var factor = this[i].factor;
        var colorMatch = new Colour(Colour.RGBA, 
            [rgba[0], rgba[1], rgba[2], rgba[3]]
        );
        var rgbDifference = color.distanceTo(colorMatch);
        if (factor < 0.2) {
            var total = (rgbDifference**colorImportance) * factor;
        } else {
            continue;
        }
        if (total < minNumber) {
            minNumber = total;
            minNumberName = name;
            bestRgb = rgba;
        }
    }
    var id = blocks[minNumberName]['code'];
    // var correspondingBlock = blocks[minNumberName];
    // if (correspondingBlock.hasOwnProperty('id_1')) {
    //     id = correspondingBlock['id_1'] + ':' + correspondingBlock['id_2'];
    // } else {
    //     id = correspondingBlock['code'];
    // }
    return {"name": minNumberName, "id": id, "rgba": bestRgb};
}

var count =0;

//V2: clamping rgbdifference and texture to the same amount, then taking avarage
Array.prototype.matchColor2 = function(r, g, b, a) {
    var minNumber = Infinity;
    var minNumberName = '';
    var bestRgb = [];
    var color = new Colour(Colour.RGBA, [r, g, b, a] );
    for (var i = 4; i < this.length; i++) {
        var rgba = this[i].average;
        var name = this[i].name;
        /* Takes a lot of time
        if (name.includes('Concrete Powder') || name.includes('Anvil') || name.includes('Shulker Box')) {continue;} */
        var factor = this[i].factor;
        var colorMatch = new Colour(Colour.RGBA, 
            [rgba[0], rgba[1], rgba[2], rgba[3]]
        );
        var rgbDifference = color.distanceTo(colorMatch);
        if (factor < 0.2) {
            var total = (rgbDifference * colorImportance + factor * rgbDifference) / (1 + colorImportance);
        } else {
            continue;
        }
        if (total < minNumber) {
            minNumber = total;
            minNumberName = name;
            bestRgb = rgba;
        }
    }
    var id = blocks[minNumberName]['code'];
    // var correspondingBlock = blocks[minNumberName];
    // if (correspondingBlock.hasOwnProperty('id_1')) {
    //     id = correspondingBlock['id_1'] + ':' + correspondingBlock['id_2'];
    // } else {
    //     id = correspondingBlock['code'];
    // }
    return {"name": minNumberName, "id": id, "rgba": bestRgb};
}