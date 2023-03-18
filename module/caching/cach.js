
function sleep(ms) {
    return new Promise(resolve => setTimeout(resolve, ms));
};

function callback(information, url) {
    var outputDiv = document.getElementById("output")
    var output = document.createElement("p");
    output.textContent += url +": "

    // Valid json
    try {
        information = JSON.parse(information);
    } catch (e) {
        output.textContent += "❌Something failed: " + information;
        outputDiv.append(output)
        return;
    }

    // Protype failed
    if (information["prototype"] == false) {
        output.textContent += "❌Prototype failed";
        outputDiv.append(output)
        return;
    }

    // Pushing failed
    if (information["pushed"] == false) {
        output.textContent += "❌Pushing failed";
        outputDiv.append(output)
        return;
    }

    // Everyting when good
    output.textContent += "✔️Generating new webpage succeeded";
    outputDiv.append(output)
}

function cachUrl(url, reason) {
    var outputDiv = document.getElementById("output")
    var output = document.createElement("p");
    output.textContent = "Reason for generating new webpage for '"+url+"' is " + reason;
    outputDiv.append(output)

    var xmlHttp = new XMLHttpRequest();
    xmlHttp.onreadystatechange = function () {
        if (xmlHttp.readyState == 4 && xmlHttp.status == 200)
            callback(xmlHttp.responseText, url);
    }
    xmlHttp.open('GET', url, true);
    xmlHttp.send(null);
}