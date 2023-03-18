//Copy function
function copy() {
    element = document.querySelector('.output textarea');
    element.select();
    element.setSelectionRange(0, 99999);
    document.execCommand("copy");
    element.selectionEnd = element.selectionStart;
}




// *********************** \\
//          INIT           \\
// *********************** \\

async function setSelect(element) {
    await selectToggle(element);
    await initSelects();
    readValues();
}

async function selectToggle(selectElement) {
    var parent = selectElement.parentElement;
    var textContent = selectElement.options[selectElement.selectedIndex].text;
    var targets = parent.querySelectorAll('div[data-corresponding-value="' + textContent + '"]');

    //Hide all toggle divs
    var allToggleDivs = selectElement.parentElement.querySelectorAll('div[data-corresponding-value]');
    hideAll(allToggleDivs);

    //Show only target div
    if (targets.length !== 0) {
        for (var i = 0; i < targets.length; i++) {
            // console.log(targets[i]);
            // console.log('div[data-corresponding-value="' + textContent + '"]');

            targets[i].style.display = "block";
        }
    }
}

async function initSelects() {
    var allSelect = document.querySelectorAll('.select');
    for (var i = 0; i < allSelect.length; i++) {
        var select = allSelect[i].querySelector('select');
        selectToggle(select);

        //Make select items flex (currently not possible in CSS)
        if (allSelect[i].querySelector('.range') === null) {
            allSelect[i].style.display = "flex!important";
        } else {
            allSelect[i].style.display = "block!important";
        }
    }
    var allSelect = document.querySelectorAll('select');
    for (var i = 0; i < allSelect.length; i++) {
        selectToggle(allSelect[i]);
    }
}

function hideAll(elements) {
    for (var i = 0; i < elements.length; i++) {
        elements[i].style.display = "none";
    }
}






// *********************** \\
//        COMMANDS         \\
// *********************** \\

function readValues() {
    var arguments = [];
    var mainValue;

    //Getting visible argument elements and their corresponding base (main) elements (also reading arguments out)
    var parentElements = document.querySelectorAll('.parent-div:not(.select), .parent-div > .toggle-div');
    var visibleElements = getVisibleElements(parentElements);
    for (var i = 0; i < visibleElements.length; i++) {
        if (visibleElements[i].hasAttribute("setvalue")) {
            var setvalue = visibleElements[i].getAttribute("setvalue");
            arguments.push({"main value": setvalue, "arguments": []});
        }
        var allChildren = visibleElements[i].children;
        for (var a = 0; a < allChildren.length; a++) {
            var element = allChildren[a];
            //Select all elements with attribute data-main or elements with children which have attribute data-main but aren't a argument
            if (element.getAttribute("data-main") === "true" || element.querySelectorAll('*[data-main=true]:not(*[data-argument-of])').length !== 0) {
                //Get main name
                var mainName = element.querySelector('p').textContent;

                //Get main element
                var mainElement = element.getAttribute("data-main") === "true" ? element : element.querySelector('*[data-main=true]');
                mainValue = Object.values(readArgument(mainElement))[0];
                arguments.push({"main value": mainValue, "arguments": []});

                //Get the elements of all arguments of the main
                var allArguments = visibleElements[i].querySelectorAll('*[data-argument-of=' + mainName + '][data-argument-name], *[data-argument-of=' + mainName + '] *[data-argument-name]');
                if (allArguments.length !== 0) {
                    var allVisibleInputs = getVisibleElements(allArguments);
                    // console.log('argument elements for ' + mainName + ': ');
                    // console.log(allVisibleInputs);
                    for (var b = 0; b < allVisibleInputs.length; b++) {
                        var info = readArgument(allVisibleInputs[b]);
                        if (Object.keys(info).length !== 0) {
                            arguments[i]["arguments"].push(info);
                        }
                    }
                }
            }
        }
    }
    //Constructing the command
    constructCommand(arguments);
}

//Constructs the command based on object
function constructCommand(arguments) {
    var outputField = document.querySelector('.output-field');
    var commandBegin = command.split('{')[0].trim();
    var output = commandBegin;
    console.log(arguments);
    for (var item in arguments) {
        if (arguments[item]["main value"] === undefined) {
            var currentPart = "<empty>";
        } else {
            var currentPart = arguments[item]["main value"];
        }
        var argumentsArray = arguments[item]["arguments"];  
        if (argumentsArray.length !== 0) {
            currentPart += '['
            for (var i = 0; i < argumentsArray.length; i++) {
                if (i > 0) { currentPart += ","}
                var object = argumentsArray[i];
                var argumentName = Object.keys(object)[0];
                var argumentValue = Object.values(object)[0];
                currentPart += argumentName + '=' + argumentValue;
            }
            currentPart += ']';
        }
        output += ' ' + currentPart;
    }
    outputField.value = output;
    document.querySelector('#command-length').textContent = output.length + " characters";
}

function readArgument(element) {
    var result = {};

    //Get argument name
    var argumentName = element.getAttribute("data-argument-name") === null ? "" : element.getAttribute("data-argument-name");

    //If element is a input field
    if (element.nodeName === "INPUT") {
        if (element.value !== "") {
            result[argumentName] = element.value;
            if (element.hasAttribute("data-prefix")) {
                result[argumentName] = element.getAttribute("data-prefix") + element.value;
            } else if (element.hasAttribute("data-suffix")) {
                result[argumentName] += element.value;
            } else if (element.hasAttribute("data-string")) {
                result[argumentName] = '"' + element.value + '"';
            }
        } else if (element.hasAttribute("data-default-value") && !element.hasAttribute("data-argument-of")) {
            result[argumentName] = element.getAttribute("data-default-value");
        }
    } else if (element.nodeName === "DIV") {
        //Select
        if (element.classList.contains("select")) {
            var toggleInputs = element.querySelectorAll('.toggle-div input');
            var visibleInputs = getVisibleElements(toggleInputs);
            var selectValue = element.querySelector('select').value;
            if (visibleInputs.length !== 0 && selectValue === "") { //If a input is currently visible
                var visibleInput = visibleInputs[0];
                
                //Using temp argument name (most efficient fix)
                visibleInput.setAttribute("data-argument-name", argumentName);
                result = readArgument(visibleInput);
                visibleInput.removeAttribute("data-argument-name");
            } else if (selectValue !== "") { //If a value is selected in the select input field
                result[argumentName] = element.querySelector('select').value;
            }
        } 
        //Range
        else if (element.classList.contains("range")) {
            var inputs = element.querySelectorAll('input');
            var min = inputs[0];
            var max = inputs[1];
            if (min.value !== '' || max.value !== '')  {
                result[argumentName] = min.value + '..' + max.value;
            }
        }
    }
    return result;
}

function isVisible(element) {
    return (element.offsetWidth !== 0 && element.offsetHeight !== 0);
}

function getVisibleElements(nodeList) {
    var elements = [];
    if (nodeList.length !== 0) {
        for (var i = 0; i < nodeList.length; i++) {
            if (isVisible(nodeList[i])) {
                elements.push(nodeList[i]);
            }
        }
    }
    return elements;
}


// *********************** \\
//       TEXTEDITOR        \\
// *********************** \\

// function fontChange(fonttype) {
//     var outputField = document.querySelector('.output-input');

//     //Get all the text (with style) and store it in array
//     var textObject = getTextObjects(outputField, {}, undefined);;

//     //Remove all styles -> reset to plain text
//     // outputField.innerHTML = outputField.textContent;

//     //Get selection
//     var selection = getSelectedText();
//     console.log(selection.toString());
//     var start = selection.anchorOffset;
//     var end = selection.focusOffset;

//     //Flip numbers if selected from right to left
//     if (end < start) {
//         start = selection.focusOffset;
//         end = selection.anchorOffset;
//     }

//     //Change font style
//     var searchIndex = 0;
//     var toBeChanged = [];
//     for (var i = 0; i < textObject.length; i++) {
//         var textLength = textObject[i]["text"].length;
//         searchIndex += textLength;
//         console.log(searchIndex, start, end);
//         if (searchIndex >= start) {
//             toBeChanged.push(textObject[i]);
//         } else if (searchIndex - textLength > end) {
//             break;
//         }
//     }
//     console.log(toBeChanged);
// }

// function getTextObjects(textElement, givenProperties, lastProperty) {
//     var textObject = [];
//     var children = textElement.childNodes;
//     for (var i = 0; i < children.length; i++) {
//         var element = children[i];
//         var tempObject = {};
//         tempObject = Object.assign(tempObject, givenProperties);
//         var nodeType = element.nodeName;
//         if (nodeType === "B") { //Bold Text
//             givenProperties["bold"] = true;
//             lastProperty = "bold";
//         } else if (nodeType === "I") { //Italic Text
//             givenProperties["italic"] = true;
//             lastProperty = "italic";
//         } else if (nodeType === "U") { //Underlined Text
//             givenProperties["underlined"] = true;
//             lastProperty = "underlined";
//         } else if (nodeType === "S") { //Strikethrough Text
//             givenProperties["strikethrough"] = true;
//             lastProperty = "strikethrough";
//         } else if (nodeType === "O") { //Obfuscated Text
//             givenProperties["obfuscated"] = true;
//             lastProperty = "strikethrough";
//         } else if (nodeType === "span" && element.hasAttribute("color")) { //Colored Text
//             tempObject["color"] = element.getAttribute("color");
//         } else {
//             delete givenProperties[lastProperty];
//         }
//         tempObject = Object.assign(tempObject, givenProperties);
//         if (element.childNodes.length <= 1) {
//             var text = element.textContent;
//             tempObject["text"] = text;
//             textObject.push(tempObject);
//         } else { //Element has colors or mutiple styles nested
//             textObject = textObject.concat(getTextObjects(element, givenProperties));
//         }
//     }
//     return textObject;
// }

// function getSelectedText() {
//     if (window.getSelection) {
//         txt = window.getSelection();
//     } else if (window.document.getSelection) {
//         txt =window.document.getSelection();
//     } else if (window.document.selection) {
//         txt = window.document.selection.createRange().text;
//     }
//     return txt;  
// }



//Init Quill texteditor

var options = {
  modules: {
    toolbar: [
        ['bold', 'italic', 'underline', 'strike', 'obfuscated'],
        [{'color': []}]
    ]
  },
  placeholder: 'Your text...',
  theme: 'snow'
};
var editor = new Quill('.texteditor2', options);




// *********************** \\
//          MAIN           \\
// *********************** \\


window.addEventListener('DOMContentLoaded', () => {
    var allToggleDivs = document.querySelectorAll('.toggle-div');
    hideAll(allToggleDivs);

    initSelects();
    readValues();
});