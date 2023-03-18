// make all inputs have the same input value
let $inputs = $(":input");
$inputs.on('input', function () {
    $('input[name="' + this.name + '"]').val($(this).val());
});

// Get the name of the game
let game_name = document.querySelector('meta[name="game-id"]').content

// Get data about items
let items_data = undefined;
let xmlHttp = new XMLHttpRequest();
xmlHttp.onreadystatechange = function() { 
    if (xmlHttp.readyState == 4 && xmlHttp.status == 200){
        items_data = JSON.parse(xmlHttp.responseText);
        update_resources()
    }
}
xmlHttp.open("GET", "/resources/games/" + game_name + "/items.json", true); // true for asynchronous 
xmlHttp.send(null);

// All selected items
let selected_items = {}

// get new resources
let $itemInput = $(".item input");

// Triggers when user types amount in item
function update_item(item) {
    // Add to allItems
    let amount = item.value
    if (amount == 0) {
        delete selected_items[item.name]
    } else {
        selected_items[item.name] = amount;
    }

    // Nothing selected
    if (Object.keys(selected_items).length == 0) {
        for (let i = 0; i < locations.length; i++) {
            document.getElementById(locations[i]).innerHTML = ""
        }
        return
    }

    // Show load icon
    htmlLoader = '<div class="loaderContainer"><div class="loader"></div></div>'
    locations = ["guideList", "resourcesList"]
    for (let i = 0; i < locations.length; i++) {
        if (document.getElementById(locations[i]).innerHTML != htmlLoader) {
            document.getElementById(locations[i]).innerHTML = htmlLoader
        }
    }

    // Send request
    update_resources()
}

// Update shown resources
function update_resources() {
    // Wait until data has arived
    if (items_data == undefined){
        return;
    }
    
    let resources = {}
    let guide = []
    let remaining = {}
    let information = { "resources": {}, "guide": {}, "remaining": {}}
    for (let item_name in selected_items){
        amount = selected_items[item_name]
        information = get_resources(item_name, amount, resources, guide, remaining)
    }
    show_information(information)
}

function show_information(information) {
    console.log(information)
    
    // place resources required on the page
    let sampleHtmlResources = `<li class="resourceItem"><div class="gameItem" style="background-position:{location image}"></div><p class="tekst">{amount} {name}</p></li>`;
    document.getElementById("resourcesList").innerHTML = "";

    for (let id in information["resources"]) {
        let item = items_data["items"][id];
        let html = sampleHtmlResources;
        html = html.replace("{name}", item["name"]);
        html = html.replace("{amount}", information["resources"][id]);
        html = html.replace("{location image}", items_data["items"][id]["location image"]);
        document.getElementById("resourcesList").innerHTML += html;
    }

    // place the guide on the page
    let sampleHtmlGuide = `<li class="guideItem {type}" onclick="guide_clicked(this)"><h3>{guide text}</h3><div class="obtaining">{input}{option}{output}</div></li>`;
    let sampleHtmlGuideInput = `<div class="gameItem inputs input-{number}" style="background-position:{location image}"></div>`;
    let sampleHtmlGuideOutput = `<div class="gameItem outputs output-{number}" style="background-position:{location image}"></div>`;
    let sampleHtmlGuideOption = `<div class="gameItem options option-{number}" style="background-position:{location image}"></div>`;
    document.getElementById("guideList").innerHTML = "";
    for (let index in information["guide"]) {
        let item = information["guide"][index];

        // generate main html
        let html = sampleHtmlGuide;

        let text = "With {input} make {output}";
        if (item["type"] in items_data["obtaining"]) {
            text = items_data["obtaining"][item["type"]]["text"];
        } else {
            item["type"] = "general";
        }
        html = html.replace("{type}", item["type"].replace(" ", "_"));
        text = text.replace("{input}", items_to_text(item["input"], item["times"]))
        text = text.replace("{output}", items_to_text(item["output"], item["times"]))
        html = html.replace("{guide text}", text);

        // generate input html
        let inputHtml = ""
        if (item.hasOwnProperty("input")) {
            for (let nummer in item["input"]) {
                if (item["input"][nummer] != "") {
                    let itemInputHtml = sampleHtmlGuideInput
                    itemInputHtml = itemInputHtml.replace("{number}", nummer);
                    itemInputHtml = itemInputHtml.replace("{location image}", items_data["items"][item["input"][nummer]["id"]]["location image"])
                    inputHtml += itemInputHtml
                }
            }
        }
        html = html.replace("{input}", inputHtml)

        // generate options html
        let optionHtml = ""
        if (item.hasOwnProperty("option")) {
            for (let nummer in item["option"]) {
                if (!item["input"][nummer] == "") {
                    let itemOptionHtml = sampleHtmlGuideOption
                    itemOptionHtml = itemOptionHtml.replace("{number}", nummer);
                    itemOptionHtml = itemOptionHtml.replace("{location image}", items_data["items"][item["option"][nummer]["id"]]["location image"])
                    optionHtml += itemOptionHtml
                }
            }
        }
        html = html.replace("{option}", optionHtml)

        // generate output html
        let outputHtml = ""
        if (item.hasOwnProperty("output")) {
            for (let nummer in item["output"]) {
                if (!item["output"][nummer] == "") {
                    let itemOutputHtml = sampleHtmlGuideOutput
                    itemOutputHtml = itemOutputHtml.replace("{number}", nummer);
                    itemOutputHtml = itemOutputHtml.replace("{location image}", items_data["items"][item["output"][nummer]["id"]]["location image"])
                    outputHtml += itemOutputHtml
                }
            }
        }
        html = html.replace("{output}", outputHtml);

        // place in document
        document.getElementById("guideList").innerHTML += html;
    }
}

// make a nice text
function items_to_text(items, times) {
    // filter and simplefy
    items = items.filter(function (item) {return item != ""});
    let counts = {};
    items.forEach(function (item) { counts[item["id"]] = (counts[item["id"]] || 0) + times * item["amount"]; });

    // generate text
    let text = []
    for (let item in counts) {
        text.push(counts[item] + " " + items_data["items"][item]["name"])
    }
    return text.length == 1 ? text[0] : [text.slice(0, text.length - 1).join(", "), text[text.length - 1]].join(" and ");
}

// make guide green
function guide_clicked(element) {
    if (element.className.split(' ').includes("finished")) {
        element.classList.remove("finished")
    } else {
        element.classList.add("finished")
    }
}

// select and deselect tabs
let tabs = document.querySelectorAll('.tab-button');
let categories = document.querySelectorAll('.tab');
function select_tab(number) {
    deselect_all_tabs()
    tabs[number].classList.add('active');
    categories[number].classList.add('active');
}

function deselect_all_tabs() {
    document.querySelector('.searchtab').style.display = "none";
    tabs.forEach(function (element) {
        element.classList.remove('active');
    });
    categories.forEach(function (element) {
        element.classList.remove('active');
    });
}

select_tab(0)


// Search item in searchbar
let searchTypingTimer;
let searchDelay = 300;
let searchbar = $("input[name='searchbar']");
let allSearchItems = document.querySelectorAll('.searchtab .item');
let allSearchNames = document.querySelectorAll('.searchtab .item input')

// on keyup, start the countdown
searchbar.on('keyup', function () {
    clearTimeout(searchTypingTimer);
    let delay = searchDelay / this.value.length
    searchTypingTimer = setTimeout(search_item.bind(null, this.value), delay);
});

// on keydown, clear the countdown 
searchbar.on('keydown', function () {
    clearTimeout(searchTypingTimer);
});

// Search
function search_item(inputValue) {
    if (inputValue === "") {
        select_tab(0)
    } else {
        deselect_all_tabs()
        document.querySelector('.searchtab').style.display = "block";
        inputValue = inputValue.toLowerCase();
        for (let i = 0; i < allSearchItems.length; i++) {
            if (allSearchNames[i].name.toLowerCase().includes(inputValue)) {
                allSearchItems[i].style.display = "block";
            } else {
                allSearchItems[i].style.display = "none";
            }
        }
    }
}


// Buttons on the side

// Show selected items
function show_selected() {
    deselect_all_tabs();
    document.querySelector('.searchtab').style.display = "block";
    for (let i = 0; i < allSearchItems.length; i++) {
        if (Object.keys(selected_items).includes(allSearchNames[i].name)) {
            allSearchItems[i].style.display = "block";
        } else {
            allSearchItems[i].style.display = "none";
        }
    }
}

// Reset inputs
function reset_inputs() {
    for (let item in selected_items) {
        let els = document.getElementsByName(item);
        for (let i = 0; i < els.length; i++) {
            els[i].value = "";
        }
    }
    selected_items = {}
    update_resources()
}

function get_resources(id, amount, resources, guide, remaining) {
    id = id.replace("'", "&#39;")

    // find item
    let item = items_data["items"][id]

    // Create remaining
    if (!(id in remaining)){
        remaining[id] = 0
    }
    remaining[id] -= amount;

    if (remaining[id] >= 0){

    } else if (item["obtaining"].length == 0) {
        // Add to resources
        if (!(id in resources)) {
            resources[id] = 0
        }
        resources[id] += amount;
        remaining[id] += amount;
    } else {
        let methode = item["obtaining"][0];

        // Create guide, if input in methode
        let guide_item = false
        for (let i in guide){
            let item = guide[i]
            if (item["output"][0]["id"] == id){
                guide_item = item
            }
        }
        if (guide_item == false && "input" in methode && methode["input"].length != 0) {
            guide_item = { "type": methode["type"], "input": [], "output": methode["output"], "times": 0 }
            for (let input_item of methode["input"]) {
                if (input_item == "") {
                    guide_item["input"].push(input_item)
                } else if ("id" in input_item) {
                    guide_item["input"].push(input_item)
                } else {
                    guide_item["input"].push(input_item[0])
                }
            }
            if ("option" in methode) {
                guide_item["option"] = methode["option"];
            }
            guide.unshift(guide_item)
        }

        // Calculate amount of resources
        if ("input" in methode && methode["input"].length != 0) {
            // make times bigger if remaining is smaller then 0
            let times = Math.ceil((-remaining[id]) / methode["output"][0]["amount"]);
            guide_item["times"] += times;
            for (let sub_item of guide_item["output"]) {
                let id = sub_item['id']
                if (!id in remaining){
                    remaining[id] = sub_item["amount"] * times;
                } else {
                    remaining[id] += sub_item["amount"] * times;
                }
            }
            for (let sub_item of guide_item["input"]) {
                if (sub_item != ""){
                    get_resources(sub_item["id"], times * sub_item["amount"], resources, guide, remaining);
                }
            }
        } else {
            if (!(id in resources)) {
                resources[id] = 0
            }
            resources[id] += parseInt(amount);
            remaining[id] += parseInt(amount);
        }
    }

    return { "resources": resources, "guide": guide, "remaining": remaining}
}

async function get_text_base_resources(id) {
    amount = 1
    while (items_data == undefined){
        await new Promise(r => setTimeout(r, 100));
    }
    information = get_resources(id, amount, [], [], [])
    let items = []
    for (let id in information["resources"]){
        items.push({"id": id, "amount": information["resources"][id]})
    }
    text = items_to_text(items, amount)
    return text
}


// examples
async function replace_templates(){
    let elements = document.getElementsByClassName("base-resources")
    for (let i = 0; i < elements.length; i++){
        let element = elements[i]
        let text = await get_text_base_resources(element.getAttribute("item-id"))
        element.innerHTML = text
    }
}

replace_templates()