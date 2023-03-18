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
        xhr.open("POST", "/module/analytics/views.php");
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
//Main
var resultTree = document.querySelector('.result-tree');
var upload = document.querySelector('input[type=file]');

var menu;
var target;

var result;

window.addEventListener('load', createMenu);

//*************************************************\\
//                   TEMPLATES                     \\
//*************************************************\\
var elementTemplateMain = `
<node>
    <span data-key>
        <span data-type="{type}" onclick="toggleMenu(this)">{iconContent}</span>
        <text contenteditable="true" spellcheck="false">{key}</text>
        
    </span>
    {value}
</node>`;

var valueTemplateInput = `
<span data-value>
    <text contenteditable="true" spellcheck="false">{value}</text>
</span>`;

var valueTemplateCompound = `
<node data-value>
    {value}
</node>`

var valueTemplateArr = `
<ul data-value data-array>
    [
    {list}
    <div onclick="addListElement(this)"><span data-type="new">+</span></div>
    <div onclick="removeListElement(this)"><span data-type="delete">-</span></div>
    ]
</ul>`;

var valueTemplateArrItem = `
<span contenteditable="true">{value}</span>`;

var iconContents = {
    byte: "B",
    short: "S",
    int: "Int",
    long: "L",
    float: "F",
    double: "D",
    "byte array": "&lt;&gt;",
    string: "str",
    compound: "{ }",
    "int array": "&lt;&gt;",
    list: "[ ]"
}

//*************************************************\\
//                DATA MANAGEMENT                  \\
//*************************************************\\
function openFile() {
    var formData = new FormData();

    var reader = new FileReader();
    reader.onload = function (e) {
        formData.append("fileblob", new Blob([e.target.result]));
        var xhr = new XMLHttpRequest();
        xhr.open("POST", "/public/applications/nbt_converter/nbt_open.php");
        xhr.send(formData);
        xhr.onreadystatechange = function () {
            if (xhr.readyState == 4 && xhr.status == 200) {
                var response = xhr.response;

                try {
                    result = JSON.parse(response);
                    console.log(result);
                } catch (e) {
                    console.error(e);
                    console.log(response);
                    return;
                }

                resultTree.innerHTML = getEntryHTML(result);
            }
        }
    }
    reader.readAsBinaryString(upload.files[0]);
}

function saveFile() {
    var data = readInputs();

    // console.log(data);

    var filename = upload.files > 0 ? upload.files[0].name : "nbt_file.dat";

    var xhr = new XMLHttpRequest();
    xhr.open("POST", "/public/applications/nbt_converter/nbt_save.php");
    xhr.responseType = "blob";
    xhr.send(JSON.stringify(data));
    xhr.onreadystatechange = function () {
        if (xhr.readyState == 4 && xhr.status == 200) {
            var response = xhr.response;

            // console.log("recieved " + response.length + " bytes");

            try {
                //Download data
                const link = document.createElement("a");
                link.href = URL.createObjectURL(response);
                link.download = filename;
                link.click();
                link.remove();
            } catch (e) {
                console.error("An error occured when downloading the file from the server. Error: " + e);
                return;
            }
        }
    }
}

//GENERATING
function getEntryHTML(obj) {
    var type = obj.type;

    var html = elementTemplateMain.replace("{type}", type);
    html = html.replace("{iconContent}", iconContents[type]);
    html = html.replace("{key}", obj.name);
    html = html.replace("{value}", getValueHTML(obj.value, type));

    // if (type === "byte array" || type === "int array") {
    //     html = html.replace("{length}", "<text>(" + obj.value.length + " entries)</text>");
    // } else {
    //     html = html.replace("{length}", "");
    // }

    console.log(html);
    return html;
}

function getValueHTML(value, type) {

    var html = "";
    if (type === "compound") {
        for (var item of value) {
            html += getEntryHTML(item);
        }
        html = valueTemplateCompound.replace("{value}", html);
    } else if (type === "byte array" || type === "int array") {
        var listHTML = "";
        for (var item of value) {
            listHTML += valueTemplateArrItem.replace("{value}", item);
        }
        html = valueTemplateArr.replace("{list}", listHTML);
    } else if (type === "list") {
        var listElementTypes = value["types"];

        var elements = value["elements"];
        for (var i = 0; i < elements.length; i++) {
            elements[i].name = i;
            html += getEntryHTML(elements[i]);
        }
        html = valueTemplateCompound.replace("{value}", html);
    } else {
        html = valueTemplateInput.replace("{value}", value);
    }

    return html;
}

//READING
function readInputs() {
    var result = [];

    var allValues = resultTree.querySelectorAll(':scope > node');
    allValues.forEach((element) => {
        result.push(readEntry(element));
    });

    return result[0];
}

function readEntry(element) {
    var entry = {};
    var key = element.querySelector('[data-key]');
    var valueElement = element.querySelector('[data-value]');

    //Read key
    var type = key.querySelector('[data-type]').getAttribute("data-type");
    entry.type = type;

    var name = key.querySelector('text[contenteditable]').textContent;
    entry.name = name;

    //Read value
    var value = valueElement.querySelector('text[contenteditable]');
    if (type === "byte" || type === "short" || type === "int" || type === "long") {                         //Rounded numbers
        var parsed = parseInt(value.textContent);
        if (Number.isNaN(parsed)) {
            var mesg = 'Value "' + value.textContent + '" is not a number. Please enter a valid number';
            alert(mesg);
            throw new Error(mesg);
        }
        entry.value = parsed;
    } else if (type === "float" || type === "double") {                                                     //Floating point numbers
        var parsed = parseFloat(value.textContent);
        if (Number.isNaN(parsed)) {
            var mesg = 'Value "' + value.textContent + '" is not a number. Please enter a valid number';
            alert(mesg);
            throw new Error(mesg);
        }
        entry.value = parsed;
    } else if (type === "string") {                                                                         //Strings
        entry.value = value.textContent;
    } else if (type === "byte array" || type === "int array") {                                             //Array's
        entry.value = readArray(valueElement);
    } else if (type === "compound") {                                                                       //Compound / object
        entry.value = readCompound(valueElement);
    } else if (type === "list") {
        console.log("list");
    }

    return entry;
}

function readArray(element) {
    var result = [];
    var allValues = element.querySelectorAll('span[contenteditable]');
    allValues.forEach((item) => {
        var value = parseInt(item.textContent);
        if (Number.isNaN(value)) {
            var mesg = 'Value "' + value + '" is not a number. Please enter a valid number';
            alert(mesg);
            throw new Error(mesg);
        } else if (value < -2147483648 || value > 2147483647) {
            var mesg = 'Value "' + value + '" is to big. The value must be between -2147483648 and 2147483647';
            alert(mesg);
            throw new Error(mesg);
        }
        result.push(parseInt(item.textContent));
    });

    return result;
} 

function readCompound(element) {
    var result = [];
    var allNodes = element.querySelectorAll(':scope > node');
    allNodes.forEach((node) => {
        result.push(readEntry(node));
    });

    return result;
}



//*************************************************\\
//                    EDITOR                       \\
//*************************************************\\
function createMenu() {
    menu = document.createElement("div");
    menu.classList.add("tooltip-menu");
    menu.innerHTML = `
    <b>Change type</b>
    <div onclick="selectType(this)"><span data-type="byte">B</span>Byte</div>
    <div onclick="selectType(this)"><span data-type="short">S</span>Short</div>
    <div onclick="selectType(this)"><span data-type="int">Int</span>Integer</div>
    <div onclick="selectType(this)"><span data-type="long">L</span>Long</div>
    <div onclick="selectType(this)"><span data-type="float">F</span>Float</div>
    <div onclick="selectType(this)"><span data-type="double">D</span>Double</div>
    <div onclick="selectType(this)"><span data-type="byte array">&lt;&gt;</span>Byte array</div>
    <div onclick="selectType(this)"><span data-type="string">str</span>String</div>
    <div onclick="selectType(this)"><span data-type="compound">{ }</span>Compound</div>
    <div onclick="selectType(this)"><span data-type="int array">&lt;i&gt;</span>Integer array</div>
    <b>More</b>
    <div onclick="addNewElement()"><span data-type="new">+</span>New</div>
    <div onclick="deleteElement()"><span data-type="delete">-</span>Delete</div>
    `;
    document.body.appendChild(menu);

    //Get all div's with tooltip attributes and set event functions
    var allDivs = document.querySelectorAll('.result-tree [data-type]');
    for (var i = 0; i < allDivs.length; i++) {
        allDivs[i].setAttribute("onclick", "toggleMenu(this)");
    }
}

function toggleMenu(element) {
    if (element.hasAttribute("data-type")) {
        var type = element.getAttribute("data-type");

        //Remove current type from option list
        var allOptions = menu.querySelectorAll('div');
        allOptions.forEach((option) => {
            option.style.display = "flex";

            var span = option.querySelector('[data-type="' + type + '"');
            if (span !== null) {
                option.style.display = "none";
            }
        });

        if (element.closest('node') === resultTree.children[0]) { //Is root node
            allOptions[allOptions.length - 2].style.display = "none";
            allOptions[allOptions.length - 1].style.display = "none";
        }

        //Show menu
        menu.classList.toggle("menu-open");
        var e = window.event;
        menu.style.top = e.clientY + document.documentElement.scrollTop + "px";
        menu.style.left = e.clientX + "px";

        target = element;
    }
}

function selectType(element) {
    var subject = element.querySelector('span');
    var type = subject.getAttribute("data-type");

    //Clear input fields
    var inputField = target.closest('node').querySelector('[data-value] > text')
    if (inputField !== null) {
        inputField.textContent = "0"; 
    }

    //Change value elements
    var valueElement = target.closest('node').querySelector('[data-value]');
    if (type === "compound") {
        valueElement.outerHTML = valueTemplateCompound.replace("{value}", getNewElementHTML());
    } else if (type.includes('array')) {
        valueElement.outerHTML = valueTemplateArr.replace("{list}", valueTemplateArrItem.replace("{value}", "0"));
    } else if (target.getAttribute("data-type") === "compound" || target.getAttribute("data-type").includes('array')) {
        valueElement.outerHTML = valueTemplateInput.replace("{value}", "0");
    }

    //Change type
    target.setAttribute("data-type", type);
    target.textContent = subject.textContent;

    //Close menu
    menu.classList.remove("menu-open");
}

function deleteElement() {
    //Remove element from DOM
    target.closest('node').remove();

    //Close menu
    menu.classList.remove("menu-open");
}

function addNewElement() {
    var clickedElement = target.closest('node');
    clickedElement.outerHTML = getNewElementHTML() + clickedElement.outerHTML;

    //Close Menu
    menu.classList.remove("menu-open");
}

function addListElement(button) {
    button.outerHTML = valueTemplateArrItem.replace("{value}", "0") + button.outerHTML;
}

function removeListElement(button) {
    var allElements = button.closest('ul').querySelectorAll('span[contenteditable]');

    //Remove last element
    if (allElements.length >= 1) {
        allElements[allElements.length - 1].remove();
    }
}

function getNewElementHTML() {
    //Generate new item HTML based of template
    var html = elementTemplateMain.replace("{type}", "byte").replace("{iconContent}", "B").replace("{key}", "New Element");
    var value = valueTemplateInput.replace("{value}", "0");
    html = html.replace("{value}", value);

    return html;
};
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
