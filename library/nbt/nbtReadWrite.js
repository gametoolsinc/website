//Custom NBT reader / writer designed for GamerTools

//Import Pako
// import pako from '/library/nbtReadWrite/pako.js';

//Tags, but named types for easier recognition
var TYPES = {}

TYPES.End = 0;
TYPES.Byte = 1;
TYPES.Short = 2;
TYPES.Int = 3;
TYPES.Long = 4;
TYPES.Float = 5;
TYPES.Double = 6;
TYPES.Byte_Array = 7;
TYPES.String = 8;
TYPES.List = 9;
TYPES.Compound = 10;
TYPES.Int_Array = 11;

var NbtReader = {
    readByte: function (value) {

    },
    readShort: function (value) {
        
    },
    readInt: function (value) {
        
    },
    readFloat: function (value) {
        
    },
    readDouble: function (value) {
        
    },
    readByteArray: function (value) {
        
    },
    readString: function (value) {
        
    },
    readList: function (value) {
        
    },
    readCompound: function (value) {
        
    },
    readIntArray: function (value) {
        
    },
    toJSON: function (data) {

    }
}
var NbtWriter = {
    writeByte: function (value) {
        var buffer = new ArrayBuffer(1);
        var bufferView = new Uint8Array(buffer);
        bufferView[0] = new Uint8Array([value]).buffer;
        return bufferView;
    },
    writeShort: function (value) {
        var buffer = new ArrayBuffer(2);
        var bufferView = new Uint8Array(buffer);
        bufferView[0] = new Uint8Array([value]).buffer;
        console.log(value, bufferView);
        return bufferView;
    },
    writeInt: function (value) {
        var buffer = new ArrayBuffer(4);
        var bufferView = new Uint8Array(buffer);
        bufferView[0] = new Uint8Array([value]).buffer;
        return bufferView;
    },
    writeFloat: function (value) {
        var buffer = new ArrayBuffer(4);
        var bufferView = new Uint8Array(buffer);
        bufferView[0] = new Uint8Array([value]).buffer;
        return bufferView;
    },
    writeDouble: function (value) {
        var buffer = new ArrayBuffer(8);
        var bufferView = new Uint8Array(buffer);
        bufferView[0] = new Uint8Array([value]).buffer;
        return bufferView;
    },
    writeByteArray: function (value) {
        var array = [];
        array.push(this.writeByte(value.length));
        for (var i = 0; i < value.length; i++) {
            array.push(this.writeByte(value[i]));
        }
        return new Uint8Array(array);
    },
    writeString: function (value) {
        
    },
    writeList: function (value) {
        
    },
    writeIntArray: function (value) {
        var arr = new Int32Array();
    },
    toNBT: function (data) {
        //Parse data to JSON
        if (typeof data === "string") {
            try {
                data = JSON.parse(data);
            } catch (e) {
                console.error("No valid JSON string given");
            }
        }
        //Handle data
        var elements = [];
        if (typeof data === "object") {
            var type = data.type;
            if (type === TYPES.Byte) {
                elements.push(this.writeByte(data.value));
            } else if (type === TYPES.Short) {
                elements.push(this.writeShort(data.value));
            } else if (type === TYPES.Int) {
                elements.push(this.writeInt(data.value));
            } else if (type === TYPES.Long) {
                console.error("Could not convert longs!");
                return;
            } else if (type === TYPES.Float) {
                elements.push(this.writeFloat(data.value));
            } else if (type === TYPES.Double) {
                elements.push(this.writeDouble(data.value));
            } else if (type === TYPES.Byte_Array) {
                elements.push(this.writeByteArray(data.value));
            } else if (type === TYPES.Int_Array) {
                elements.push(this.writeInt(data.value));
            } else if (type === TYPES.String) {
                elements.push(this.writeString(data.value));
            } else if (type === TYPES.List) {
                elements.push(this.writeByte(type));
                elements.push(this.writeInt(data.value.length));
                for (var i = 0; i < data.value.length; i++) {
                    elements.push(this.toNBT(data.value[i]));
                }
            } else if (type === TYPES.Compound) {
                for (var i = 0; i < data.value.length; i++) {
                    elements.push(this.toNBT(data.value[i]));
                }
                elements.push(this.toNBT({type: TYPES.End}));
            }
        }
        var mergedArray = concat(elements);
        return mergedArray;
    }
}
function writeToFile(data) {
    //Compress data to gzip format
    var compressed = pako.gzip(test,{ to: 'string' });
    console.log(compressed);

    //Encode data to Base64
    // var base64 = btoa(compressed);

    var xhr = new XMLHttpRequest();
    xhr.open("POST", "/library/nbtReadWrite/writeFile.php");
    xhr.setRequestHeader("Content-Type", "application/json;charset=UTF-8");
    xhr.send(base64);
    xhr.onreadystatechange = function () {
        if (xhr.readyState == 4 && xhr.status == 200) {
            var response = xhr.responseText;
            console.log('done');
            // try {
            //     // var data = JSON.parse(response);
            //     console.log(response);
            // } catch {
            //     console.log(response);
            //     return;
            // }
        }
    }
}
function readFile() {
    var rawFile = new XMLHttpRequest();
    rawFile.open("GET", "/library/nbtReadWrite/map_27.dat", true);
    rawFile.responseType = 'blob';
    rawFile.onreadystatechange = function() {
        if (rawFile.readyState === 4) {
            var fileReader = new FileReader();
            fileReader.onload = function(e) {
                let arrayBuffer = new Uint8Array(fileReader.result);
                console.log(arrayBuffer);
            }
            fileReader.readAsArrayBuffer(rawFile.response);
        }
    }
    rawFile.send();
}
function concat(arrays) {
    // sum of individual array lengths
    let totalLength = arrays.reduce((acc, value) => acc + value.length, 0);

    if (!arrays.length && arrays.length !== 0) return null;

    let result = new Uint8Array(totalLength);

    // for each array - copy it over result
    // next array is copied right after the previous one
    let length = 0;
    for(let array of arrays) {
        result.set(array, length);
        length += array.length;
    }

    return result;
}

//Volgende keer:
//Probeer de typedarrays to concatten in 1 grote file
//Maak de rest van de methodes af zodat alles alvast geconstrueerd kan worden
//Probeer de uiteindelijke buffer (concatted) data te writen naar een file
//Check deze file in de NBT editor
//Check wat er veranderd kan worden om het goed te laten werken :)

var bufs = [];
bufs.push(new Int16Array([12,51,91,42,69].buffer));
var test = new Int8Array([0,50,21,75,9]);
// writeToFile(test);

var map_file = {
    type: TYPES.Compound,
    name: '',
    value: [
        {
            type: TYPES.Compound,
            name: 'data',
            value: [
                {
                    type: TYPES.Byte,
                    name: 'scale',
                    value: 0
                },
                {
                    type: TYPES.Byte,
                    name: 'dimension',
                    value: 0
                },
                {
                    type: TYPES.Byte,
                    name: 'trackingPosition',
                    value: 0
                },
                {
                    type: TYPES.Byte,
                    name: 'locked',
                    value: 1
                },
                {
                    type: TYPES.Short,
                    name: 'height',
                    value: 128
                },
                {
                    type: TYPES.Short,
                    name: 'width',
                    value: 128
                },
                {
                    type: TYPES.Int,
                    name: 'xCenter',
                    value: 0
                },
                {
                    type: TYPES.Int,
                    name: 'zCenter',
                    value: 0
                },
                {
                    type: TYPES.Byte_Array,
                    name: 'colors',
                    value: [0,0,3,5,1,9,8]
                }
            ]
        }
    ]
}
// console.log(NbtWriter.toNBT(map_file));
readFile();
var buf = new Buffer(23);
console.log(Buffer);
