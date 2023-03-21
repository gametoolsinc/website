let mobileMenu = document.querySelector('.mobile-menu');
let overlay = document.querySelector('.overlay');

function toggleNavBarMenu() {
    mobileMenu.classList.toggle("menu-open");
    overlay.classList.toggle("overlay-visible");
};
document.addEventListener('DOMContentLoaded', function() {
    var userAgent = navigator.userAgent;

    var botPattern = "(googlebot\/|bot|Googlebot-Mobile|Googlebot-Image|Google favicon|Mediapartners-Google|bingbot|slurp|java|wget|curl|Commons-HttpClient|Python-urllib|libwww|httpunit|nutch|phpcrawl|msnbot|jyxobot|FAST-WebCrawler|FAST Enterprise Crawler|biglotron|teoma|convera|seekbot|gigablast|exabot|ngbot|ia_archiver|GingerCrawler|webmon |httrack|webcrawler|grub.org|UsineNouvelleCrawler|antibot|netresearchserver|speedy|fluffy|bibnum.bnf|findlink|msrbot|panscient|yacybot|AISearchBot|IOI|ips-agent|tagoobot|MJ12bot|dotbot|woriobot|yanga|buzzbot|mlbot|yandexbot|purebot|Linguee Bot|Voyager|CyberPatrol|voilabot|baiduspider|citeseerxbot|spbot|twengabot|postrank|turnitinbot|scribdbot|page2rss|sitebot|linkdex|Adidxbot|blekkobot|ezooms|dotbot|Mail.RU_Bot|discobot|heritrix|findthatfile|europarchive.org|NerdByNature.Bot|sistrix crawler|ahrefsbot|Aboundex|domaincrawler|wbsearchbot|summify|ccbot|edisterbot|seznambot|ec2linkfinder|gslfbot|aihitbot|intelium_bot|facebookexternalhit|yeti|RetrevoPageAnalyzer|lb-spider|sogou|lssbot|careerbot|wotbox|wocbot|ichiro|DuckDuckBot|lssrocketcrawler|drupact|webcompanycrawler|acoonbot|openindexspider|gnam gnam spider|web-archive-net.com.bot|backlinkcrawler|coccoc|integromedb|content crawler spider|toplistbot|seokicks-robot|it2media-domain-crawler|ip-web-crawler.com|siteexplorer.info|elisabot|proximic|changedetection|blexbot|arabot|WeSEE:Search|niki-bot|CrystalSemanticsBot|rogerbot|360Spider|psbot|InterfaxScanBot|Lipperhey SEO Service|CC Metadata Scaper|g00g1e.net|GrapeshotCrawler|urlappendbot|brainobot|fr-crawler|binlar|SimpleCrawler|Livelapbot|Twitterbot|cXensebot|smtbot|bnf.fr_bot|A6-Indexer|ADmantX|Facebot|Twitterbot|OrangeBot|memorybot|AdvBot|MegaIndex|SemanticScholarBot|ltx71|nerdybot|xovibot|BUbiNG|Qwantify|archive.org_bot|Applebot|TweetmemeBot|crawler4j|findxbot|SemrushBot|yoozBot|lipperhey|y!j-asr|Domain Re-Animator Bot|AddThis)";
    var re = new RegExp(botPattern, 'i');
    var userAgent = navigator.userAgent; 
    if (re.test(userAgent)) {
        return;
    }

    var url = window.location.href;
    if (!url.includes('/debug') && !url.includes('views.js')) {
        var xhr = new XMLHttpRequest();
        xhr.open("POST", "/module/analytics/addView.php");
        xhr.onreadystatechange = function () {
            if (xhr.readyState == 4 && xhr.status == 200) {
            }
        }
        xhr.send(JSON.stringify({
            url: url
        }));
    }
}, false);
;
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

replace_templates();
//Custom tooltip
//© Gamertools 2021


//*************************************************\\
//  Note: for data-tooltip-onload to work you must 
//  include the tooltip.js file in the body tag!

//  Available attributes:
//  data-tooltip -> set string or a html file as display

//  If specified a html file as data-tooltip:
//  data-tooltip-giveinfo -> sets value of attribute of first element in html file.
//  data-tooltip-onload -> call a globally defined function when the content of the html is loaded.

//*************************************************\\


//Add tooltip css
// var cssPath = '/library/tooltip/tooltip.css';
// if (!document.getElementById(cssPath)) {
//     var head  = document.getElementsByTagName('head')[0];
//     var link  = document.createElement('link');
//     link.rel  = 'stylesheet';
//     link.type = 'text/css';
//     link.href = cssPath;
//     link.media = 'all';
//     head.appendChild(link);
// }

//Create tooltip div
var tooltip = document.createElement('div');
var update_tooltip = false;
tooltip.classList.add("tooltip");

//Window sizes
var windowWidth = window.innerWidth || document.documentElement.clientWidth || document.body.clientWidth;
var windowHeight = window.innerHeight || document.documentElement.clientHeight || document.body.clientHeight;

window.addEventListener("resize", function() {
    windowWidth = window.innerWidth || document.documentElement.clientWidth || document.body.clientWidth;
    windowHeight = window.innerHeight || document.documentElement.clientHeight || document.body.clientHeight;
});

//Append tooltip div to body when page is loaded
window.addEventListener('load', function () {
    document.querySelector('body').appendChild(tooltip);

    //Get all div's with tooltip attributes and set event functions
    var allDivs = document.querySelectorAll('[data-tooltip]');
    for (var i = 0; i < allDivs.length; i++) {
        allDivs[i].setAttribute("onmouseover", "tooltipDataCheck(this)");
        allDivs[i].setAttribute("onmouseout", "tooltipHide()");

        //Make sure pointer events is set to none to stop flickering
        var children = allDivs[i].querySelectorAll('*');
        for (var a = 0; a < children.length; a++) {
            children[a].style.pointerEvents = "none";
        }
    }
});

//Set tooltip div to mouse position
document.onmousemove = function(event) {
    if (update_tooltip) {
        //Tooltip to mouse position
        tooltip.style.top = 10 + event.pageY + "px";

        if (windowWidth - event.clientX <= tooltip.clientWidth + 5) {
            //Flip tooltip
            tooltip.style.transform = "translateX(-100%)";

            //Tooltip to mouse position + flipped offset
            tooltip.style.left = -5 + event.clientX + "px";
        } else {
            tooltip.style.transform = "translateX(0%)";

            //Tooltip to mouse position + offset
            tooltip.style.left = 5 + event.clientX + "px";
        }
    }
}

//When mouse hovers over element with 'data-tooltip' attribute
function tooltipDataCheck(element) {
    if (element.getAttribute("data-tooltip").includes(".html")) {
        //Remove padding
        tooltip.style.padding = "0px";

        //Get contents from linked html file and set it as innerHTML of the tooltip
        var txtFile = new XMLHttpRequest();
        var fileLocation = '/library/tooltip/tooltip-html/' + element.getAttribute("data-tooltip") + '?' + Math.round(Math.random() * 10000); //Random numbers for cache clearing
        txtFile.open("GET", fileLocation, true);
        txtFile.onreadystatechange = function() {
            if (txtFile.readyState === 4) {  // Makes sure the document is ready to parse.
                if (txtFile.status === 200) {  // Makes sure it's found the file.
                    allText = txtFile.responseText; 
                    tooltip.innerHTML = txtFile.responseText;

                    //Add functionality to pass additional data to html file
                    if (element.getAttribute("data-tooltip-giveinfo")) {
                        tooltip.firstChild.setAttribute("data-tooltip-giveninfo", element.getAttribute("data-tooltip-giveinfo"));
                    }

                    //Check if onload attribute is added to one (or more) of the items
                    var onLoadAttrElements = tooltip.querySelectorAll('[data-tooltip-onload]');
                    for (var i = 0; i < onLoadAttrElements.length; i++) {
                        eval(onLoadAttrElements[i].getAttribute("data-tooltip-onload"));
                    }
                }
            }
        }
        txtFile.send(null);
    } else {
        //Add padding
        tooltip.style.padding = "10px";

        //Set content
        tooltip.innerHTML = element.getAttribute("data-tooltip");
    }
    if (windowWidth > 768) {
        update_tooltip = true;
        tooltip.style.display = "block";
    }
}

//Hide tooltip when mouse leaves the element
function tooltipHide() {
    update_tooltip = false;
    tooltip.style.display = "none";
    tooltip.innerHTML = "";
    tooltip.style.transition = "none";
};
