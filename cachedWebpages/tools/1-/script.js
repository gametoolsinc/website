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
        xhr.open("POST", "/library/client/view/views.php");
        xhr.onreadystatechange = function () {
            if (xhr.readyState == 4 && xhr.status == 200) {
                console.log(xhr.response);
            }
        }
        xhr.send(JSON.stringify({
            url: url
        }));
    }
}, false);
;
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
;
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
