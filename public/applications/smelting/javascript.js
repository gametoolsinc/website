function calcTime(element, time) {
    var output = element.parentElement.parentElement.querySelector('#output');
    if (!(element.value == "" || element.value == "e" || element.value == "0")) {
        element.parentElement.parentElement.parentElement.querySelector('#smelting').classList.add("lit");
        output.textContent = convertTime(parseFloat(element.value) * time);
    } else {
        element.parentElement.parentElement.parentElement.querySelector('#smelting').classList.remove("lit");
        output.textContent = "";
    }
}

//Pretty time formatting
function convertTime(time) {//Time is in seconds
    //Gather data
    var times = {};
    if (time < 60) { //Seconds
        times.second = time;
    } else if (time >= 60 && time < 3600) { //Minutes
        times.minute = Math.floor(time / 60);
        times.second = time - times.minute * 60;
    } else if (time >= 3600 && time < 86400) { //Hours
        times.hour = Math.floor(time / 3600);
        times.minute = Math.floor((time - times.hour * 3600) / 60);
        times.second = time - times.hour * 3600 - times.minute * 60;
    } else if (time >= 86400) { //Days
        times.day = Math.floor(time / 86400);
        times.hour = Math.floor((time - times.day * 86400) / 3600);
        times.minute = Math.floor((time - times.day * 86400 - times.hour * 3600) / 60);
        times.second = time - times.day * 86400 - times.hour * 3600 - times.minute * 60;
    }
    //Format string
    var string = "";
    var entries = Object.entries(times);
    for (var i = 0; i < entries.length; i++) {
        var name = entries[i][0];
        var value = entries[i][1];
        if (value !== 0) {
            if (i == 0) {
                string += value + " " + name.plural(value);
            } else if (i == entries.length - 1) {
                string += " and " + value + " " + name.plural(value);
            } else {
                string += ", " + value + " " + name.plural(value);
            }
        }
    }
    return string;
}

function calcFurnace(element, smeltTime) {
    var parentElement = element.parentElement.parentElement.parentElement;
    var days = parseFloat(parentElement.querySelector('#days').value) || 0;
    var hours = parseFloat(parentElement.querySelector('#hours').value) || 0;
    var minutes = parseFloat(parentElement.querySelector('#minutes').value) || 0;
    var seconds = parseFloat(parentElement.querySelector('#seconds').value) || 0;
    var itemAmount = parseFloat(parentElement.querySelector('#amount').value) || 0;
    var timeSeconds = days * 86400 + hours * 3600 + minutes * 60 + seconds;
    var furnaceAmount = Math.ceil(smeltTime / timeSeconds * itemAmount);
    
    if (itemAmount !== 0) { //Don't divide by 0
        parentElement.querySelector('#furnace_output').innerHTML = furnaceAmount + " furnace".plural(furnaceAmount);
    } else {
        parentElement.querySelector('#furnace_output').innerHTML = "0 furnaces";
    }
}

//Completely unnecessary function, yet useful :)
String.prototype.plural = function(number) {
    if (number !== 1) {
        return this + "s";
    }
    return this;
}