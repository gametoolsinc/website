// Get the name of the other box where values should be displayed
Element.prototype.getOtherDivName = function() {
    var className = this.className;
    if (className === "left_unit") {
        className = "right_unit";
    } else if (className === "right_unit") {
        className = "left_unit";
    }
    return className;
}

function calcItems(element) {
    // Get the amount of items needed
    var amount = 0;
    var allInputs = element.parentElement.parentElement.querySelectorAll('input');
    for (var i = 0; i < allInputs.length; i++) {
        amount += allInputs[i].value * allInputs[i].getAttribute("unit-size");
    }
    
    // Getting the name of the other box where values should be displayed
    var className = element.parentElement.parentElement.parentElement.getOtherDivName();
    
    // Calculating and display amounts for the given amount of items
    var allOtherInputs = document.querySelectorAll('.' + className + ' input');
    for (var i = 0; i < allOtherInputs.length; i++){
        var input = allOtherInputs[i]
        var unitSize = input.getAttribute("unit-size")
        var decimals = input.getAttribute("unit-decimals")
        var unitAmount = Math.floor(amount/unitSize*10**decimals)/10**decimals
        input.value = unitAmount;
        amount -= unitSize * unitAmount;
        amount = Math.max(0, amount);
    }
}

var defaultUnit, defaultUnitSize;

function tabSwitch(select, firstCall) {

    // Get units
    var units = [];
    var container = select.options[select.selectedIndex];
    for (var i = 0; true; i++){
        var unit = container.getAttribute("unit-"+i)
        var defaultUnitSize = container.getAttribute("size-"+i)
        var defaultUnitDecimals = container.getAttribute("decimals-"+i)
        if (unit == null){
            break;
        } else {
            units.push({"unit":unit, "size":defaultUnitSize, "decimals":defaultUnitDecimals})
        }
    }

    var content = select.parentElement.querySelector('.content');
    content.innerHTML = "";

    // Generate content inside of content boxes
    var sampleInput = `<label><input type="number" unit-name="{name}" unit-size="{size}" unit-decimals="{decimals}" oninput="calcItems(this)"> {name}</label>`;

    // Generate units
    for (var i = 0; i < units.length; i++){
        var unit = units[i]
        var unitHTML = sampleInput.replace(/{name}/g, unit["unit"]);
        unitHTML = unitHTML.replace(/{size}/g, unit["size"]);
        unitHTML = unitHTML.replace(/{decimals}/g, unit["decimals"]);
        content.innerHTML += unitHTML
    }

    // Update values if new value is selected
    if (!firstCall) {
        var className = select.parentElement.getOtherDivName();
        calcItems(document.querySelector('.' + className + ' input'));
    }
}

var allSelect = document.querySelectorAll('.unit_container select');
for (var i = 0; i < allSelect.length; i++) {
    tabSwitch(allSelect[i], true);
}

function switchUnits(element) {
    //Store the left and right unit boxes select fields in variables
    var leftSelect = element.parentElement.parentElement.querySelector('.left_unit select');
    var rightSelect = element.parentElement.parentElement.querySelector('.right_unit select');

    //Get the index of the currently selected option
    var leftSelectIndex = leftSelect.selectedIndex;
    var rightSelectIndex = rightSelect.selectedIndex;

    //Swap the index of the options
    rightSelect.selectedIndex = leftSelectIndex;
    leftSelect.selectedIndex = rightSelectIndex;

    //Update the content of the boxes
    tabSwitch(leftSelect, false);
    tabSwitch(rightSelect, false);
}
