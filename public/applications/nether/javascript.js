function calculateDistance(a,b,c) {
    return Math.sqrt(a**2+b**2+c**2);
}

var overworld = {};
var nether = {};

function gatherValues() {
    overworld.x = parseFloat(document.querySelector('#overworld_x').value);
    overworld.y = parseFloat(document.querySelector('#overworld_y').value);
    overworld.z = parseFloat(document.querySelector('#overworld_z').value);
    nether.x = parseFloat(document.querySelector('#nether_x').value);
    nether.y = parseFloat(document.querySelector('#nether_y').value);
    nether.z = parseFloat(document.querySelector('#nether_z').value);
}

function calculateCoordinates(element) {
    var box = element.parentElement.parentElement.parentElement;
    gatherValues();

    if (box.classList.contains('overworld')) {
        document.querySelector('#nether_x').value = Math.floor(overworld.x / 8);
        document.querySelector('#nether_y').value = Math.floor(overworld.y);
        document.querySelector('#nether_z').value = Math.floor(overworld.z / 8);
    } else if (box.classList.contains('nether')) {
        document.querySelector('#overworld_x').value = Math.floor(nether.x * 8);
        document.querySelector('#overworld_y').value = Math.floor(nether.y);
        document.querySelector('#overworld_z').value = Math.floor(nether.z * 8);
    }

    gatherValues();

    var allSpanCoordinates = document.querySelectorAll('.coordinates');
    for (var i = 0; i < allSpanCoordinates.length; i++) {
        allSpanCoordinates[i].textContent = '(' + Math.floor(overworld.x / 8) + ', ' + Math.floor(overworld.y) + ', ' + Math.floor(overworld.z / 8) + ')';
    }
}

calculateCoordinates(document.querySelector('#overworld_x'));