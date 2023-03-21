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
//Call function for random class
var data;
function randomClass() {
    var xhr = new XMLHttpRequest();
    xhr.open("POST", "/public/applications/randomclassgenerator/randomclass.php");
    xhr.setRequestHeader("Content-Type", "application/json;charset=UTF-8");
    xhr.send("data hier");
    xhr.onreadystatechange = function () {
        if (xhr.readyState == 4 && xhr.status == 200) {
            var response = xhr.responseText;
            
            try {
                data = JSON.parse(response);
                console.log(data);
            } catch {
                console.log(response);
                return;
            }
            displayClass(data);
        }
    }
}


function displayClass(classData) {
    //HTML markup
    var weaponHTML = `
    <div class="{number}">
        <div class="title">
            <h2>{type}</h2>
            <div class="attachment_count">
                {dots}
            </div>
        </div>
        <div class="content">
            <div class="info">
                <img src="{img url}" alt="" data-tooltip="weaponHover.html" data-tooltip-giveinfo="{weaponName}" {events}>
                <h2 data-tooltip="weaponHover.html" data-tooltip-giveinfo="{weaponName}" {events}>{weaponName}</h2>
            </div>
            <div class="attachments">
                {attachments}
            </div>
        </div>
    </div>`;

    var attachmentHTML = `
    <div class="attachment">
        <h3>{type}</h3>
        <div class="attachment_content">
            <img src="{img src}" onerror="this.onerror=null; this.src='/resources/games/codcoldwar/user_interface/attachments/not_found.png'">
            <p>{name}</p>
        </div>
    </div>`;

    var attachmentNoneHTML = `
    <div class="attachment">
        <h3>{type}</h3>
        <p>None</p>
    </div>`;

    var equipmentHTML = `
    <div class="equipment">
        <div class="title">
            <h2>Equipment</h2>
        </div>
        <div class="content">
            <div class="equipments">
                {items}
            </div>
        </div>  
    </div>`;

    var equipmentItemHTML = `
    <div class="item">
        <h3>{type}</h3>
        <p>{name}</p>
        <img draggable="false" src="{img url}" alt="">
    </div>`;

    var otherHTML = `
    <div class="other">
        <div class="title">
            <h2>Other</h2>
        </div>
        <div class="content">
            <div class="perk wildcard">
                <h2>Wildcard</h2>
                <p>{name}</p>
                <img height="100px" draggable="false" src="{img url}" alt="">           
            </div>
            <div class="perk">
                <h3>Perk 1</h3>
                {perk1img}
            </div>
            <div class="perk">
                <h3>Perk 2</h3>
                {perk2img}
            </div> 
            <div class="perk">
                <h3>Perk 3</h3>
                {perk3img}
            </div>
        </div>
    </div>`;

    var generatedContent = document.querySelector('.generated_class');

    //---------------------------//
    //Get random attachments from list
    var attachmentCount = classData["class"]["wildcard"]["attachment amount"];
    var attachments = {};
    var i = 0;
    for (var weapon in classData["class"]["weapons"]) {
        if (classData["class"]["weapons"][weapon] !== []) {
            if (attachmentCount > 5 && i === 0) {
                attachments[weapon] = Object.keys(classData["class"]["weapons"][weapon]).sort(() => .5 - Math.random()).slice(0,attachmentCount);
            } else {
                attachments[weapon] = Object.keys(classData["class"]["weapons"][weapon]).sort(() => .5 - Math.random()).slice(0,5);
            }
        }
        i++;
    }

    //Get primary/secondary weapon
    var firstWeaponName, secondWeaponName;
    for (var weapon in classData["information"]["weapons"]) {
        if (classData["information"]["weapons"][weapon]["class_type"] === "Primary") {
            firstWeaponName = weapon;
        } else if (classData["information"]["weapons"][weapon]["class_type"] === "Secondary") {
            secondWeaponName = weapon;
        }
    }

    //First weapon
    var firstWeaponData = classData["class"]["weapons"][firstWeaponName];
    var firstWeaponHTML = weaponHTML.replace(/{weaponName}/g, firstWeaponName).replace(/{number}/g, 'primary_weapon').replace(/{type}/g, 'Primary Weapon');
    console.log(firstWeaponName);
    firstWeaponHTML = firstWeaponHTML.replace(/{img url}/g, '/resources/games/codcoldwar/user_interface/weapons/' + firstWeaponName.format());
    firstWeaponHTML = firstWeaponHTML.replace(/{events}/g, 'onmouseover="tooltipDataCheck(this)" onmouseout="tooltipHide()"');

    //Attachments
    var spanString = '';
    for (var attachment in attachments[firstWeaponName]) {
        spanString += '<span></span>';
    }
    var attachmentString = getAttachmentHTML(firstWeaponData, attachments[firstWeaponName], attachmentHTML, attachmentNoneHTML);

    //Update HTML
    firstWeaponHTML = firstWeaponHTML.replace(/{dots}/g, spanString).replace(/{attachments}/g, attachmentString);

    //---------------------------//
    //Second Weapon
    var secondWeaponData = classData["class"]["weapons"][secondWeaponName];
    var secondWeaponHTML = weaponHTML.replace(/{weaponName}/g, secondWeaponName).replace(/{number}/g, 'secondary_weapon').replace(/{type}/g, 'Secondary Weapon');
    secondWeaponHTML = secondWeaponHTML.replace(/{img url}/g, '/resources/games/codcoldwar/user_interface/weapons/' + secondWeaponName.format());
    secondWeaponHTML = secondWeaponHTML.replace(/{events}/g, 'onmouseover="tooltipDataCheck(this)" onmouseout="tooltipHide()"');

    //Attachments
    spanString = '';
    for (var attachment in attachments[secondWeaponName]) {
        spanString += '<span></span>';
    }
    attachmentString = getAttachmentHTML(secondWeaponData, attachments[secondWeaponName], attachmentHTML, attachmentNoneHTML);

    //Update HTML
    secondWeaponHTML = secondWeaponHTML.replace(/{dots}/g, spanString).replace(/{attachments}/g, attachmentString);

    //---------------------------//
    //Equipment
    var equipmentString = '';
    for (var type in classData["class"]["equipment"]) {
        var name = classData["class"]["equipment"][type];
        var string = equipmentItemHTML.replace(/{type}/g, type).replace(/{name}/g, name);
        string = string.replace(/{img url}/g, '/resources/games/codcoldwar/user_interface/equipment/' + classData["class"]["equipment"][type].format());
        equipmentString += string;
    }
    equipmentHTML = equipmentHTML.replace(/{items}/g, equipmentString);

    //---------------------------//
    //Wildcard & Perks
    var other = otherHTML.replace(/{name}/g, classData["class"]["wildcard"]["name"]);
    other = other.replace(/{img url}/g, '/resources/games/codcoldwar/user_interface/wildcards/' + classData["class"]["wildcard"]["name"].format());
    other = other.replace(/{perk1img}/g, getPerks(classData["class"]["perks"]["1st"]));
    other = other.replace(/{perk2img}/g, getPerks(classData["class"]["perks"]["2nd"]));
    other = other.replace(/{perk3img}/g, getPerks(classData["class"]["perks"]["3rd"]));

    //Update HTML
    generatedContent.innerHTML = firstWeaponHTML + secondWeaponHTML + equipmentHTML + other;

    //Update counter
    document.querySelector('.counter').textContent = classData["information"]["counter"] + " classes rolled";

    //Animations
    document.querySelector('.primary_weapon').style.animationName = "primaryWeapon";
    document.querySelector('.secondary_weapon').style.animationName = "secondaryWeapon";
    document.querySelector('.equipment').style.animationName = "equipment";
    document.querySelector('.other').style.animationName = "other";
}

function getAttachmentHTML(weaponData, types, attachmentHTML, attachmentNoneHTML) {
    var string = '';
    var standardTypes = ["Optic", "Muzzle", "Barrel", "Body", "Underbarrel", "Magazine", "Handle", "Stock"];
    for (var number in standardTypes) {
        var type = standardTypes[number];
        if (types.includes(type)) {
            //data
            var attachment = weaponData[type];
            var currentAttachmentHTML = attachmentHTML.replace(/{type}/g, type);
            currentAttachmentHTML = currentAttachmentHTML.replace(/{name}/g, weaponData[type]);

            //img
            var imgSrc = '/resources/games/codcoldwar/user_interface/attachments/' + type.toLowerCase() + '/' + weaponData[type].format();
            currentAttachmentHTML = currentAttachmentHTML.replace(/{img src}/g, imgSrc);

            //update
            string += currentAttachmentHTML;
        } else {
            string += attachmentNoneHTML.replace(/{type}/g, type);
        }
    }
    return string;
}

function getPerks(array) {
    var string = '';
    for (var number in array) {
        //Get img
        string += '<div class="perk_item">'
        string += '<img draggable="false" src="{src}" alt="">'.replace(/{src}/g, '/resources/games/codcoldwar/user_interface/perks/' + array[number]["name"].format());
        string += '<p>{name}</p>'.replace(/{name}/g, array[number]["name"]);
        string += '</div>'
    }
    return string;
}

String.prototype.format = function() {
    return this.toLowerCase().replace(/ /g, '_').replace('"', 'i') + '.png';
}

function hoverInfo() {
    var classData = data;
    var tooltip = document.querySelector('.tooltip');
    var weaponName = tooltip.firstChild.getAttribute("data-tooltip-giveninfo");
    tooltip.querySelector('h2').textContent = weaponName;

    //format table
    var string = '<div class="grid_table">';
    tooltip.firstChild.innerHTML += '<div class="grid_table">';
    for (var info in classData["information"]["weapons"][weaponName]) {
        if (info !== "attachments") {
            var key = (info[0].toUpperCase() + info.substring(1)).replace(/_/g, ' ');
            var value = classData["information"]["weapons"][weaponName][info];
            // tooltip.firstChild.innerHTML += '<div class="table-row"><p>{key}</p><p>{value}</p></div>'.replace(/{key}/g, key).replace(/{value}/g, value);
            string += '<p>{key}</p><p>{value}</p>'.replace(/{key}/g, key).replace(/{value}/g, value);
        }
    }
    string += '</div>';
    tooltip.firstChild.innerHTML += string;
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
