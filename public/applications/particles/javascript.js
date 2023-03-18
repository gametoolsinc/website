//Get & set canvas
var canvas = document.querySelector('#canvas');
var context = canvas.getContext("2d");

var img = document.createElement("img");
img.src = 'testtext.jpg';
img.height = 10;
img.width = 10;
console.log(img);

canvas.height = 20;
canvas.width = 20;
context.scale(0.2, 0.2);
context.font = "100 100px Roboto";
context.textAlign = "center";
context.fillText("M", 50, 80);
// context.drawImage(img, 0, 0);

//Read canvas
var canvasData = context.getImageData(0, 0, canvas.width, canvas.height);
var data = canvasData.data;

var blackThreshold = 180;

var results = [];
for (let i = 0; i < data.length; i += 4) {
    const x = (i / 4) % canvas.width;
    const y = Math.ceil((i / 4) / canvas.height);
    const red = data[i];
    const green = data[i + 1];
    const blue = data[i + 2];
    const alpha = data[i + 3];
    if (alpha >= 10) {
        results.push({"x": x, "y": y, "red": red, "green": green, "blue": blue, "alpha": alpha});
    } else {
        //Make this pixel white
        data[i] = 255;
        data[i + 1] = 255;
        data[i + 2] = 255;
        data[i + 3] = 255;
    }
}
//Update changes
context.putImageData(canvasData, 0, 0);

console.log(results);