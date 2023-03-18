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
/**
 * 
 * @param {*} duration duration of the tone in milliseconds. Default is 500
 * @param {*} frequency frequency of the tone in hertz. default is 440
 * @param {*} volume volume of the tone. Default is 1, off is 0.
 * @param {*} type type of tone. Possible values are sine, square, sawtooth, triangle, and custom. Default is sine.
 * @param {*} callback callback to use on end of tone
 */
// var audioCtx = new (window.AudioContext || window.webkitAudioContext || window.audioContext)();
const audioCtx = new AudioContext();

function beep(duration, frequency, volume, type, callback) {
    volume = 0.1
    var oscillator = audioCtx.createOscillator();
    var gainNode = audioCtx.createGain();
    
    oscillator.connect(gainNode);
    gainNode.connect(audioCtx.destination);
    
    if (volume){gainNode.gain.value = volume;}
    if (frequency){oscillator.frequency.value = frequency;}
    if (type){oscillator.type = type;}
    if (callback){oscillator.onended = callback;}
    
    oscillator.start(audioCtx.currentTime);
    oscillator.stop(audioCtx.currentTime + ((duration || 500) / 1000));
};

function sleep(ms) {
    return new Promise(resolve => setTimeout(resolve, ms));
}

class SoundArray {
    /**
     * 
     * @param {} length 
     * @param {HTMLElement} board 
     */
    constructor(length, board) {
        this.min = 100
        this.max = 6000

        this.board = board;

        this.setSize(length)

        
        this.slowdown = 100
    }

    setSize(length) {
        this.array = []
        this.board.innerHTML = ""
        for (let i = 0; i < length; i ++){
            let div = document.createElement("div");
            div.style.height = (100 / length * (i + 1)) + "%"
            div.classList.add("bar")
            this.board.appendChild(div)
            this.array.push(i)
        }
        this.multiplier = (this.max - this.min) / length
    }

    async shuffle() {
        this.slowdown = 50
        let currentIndex = this.array.length,  randomIndex;

        // While there remain elements to shuffle.
        while (currentIndex != 0) {

            // Pick a remaining element.
            randomIndex = Math.floor(Math.random() * currentIndex);
            currentIndex--;

            // And swap it with the current element.
            await this.swap(currentIndex, randomIndex)
        }
    }

    async get(x) {
        let divs = board.childNodes;
        this.highlight(divs[x])
        return this.array[x]
    }

    async move(from, to) {
        let divs = board.childNodes;
        this.highlight(divs[from])
        divs[from].parentNode.insertBefore(divs[from], divs[to]);

        this.array.splice(to, 0, ...this.splice(from, 1))
        await this.sound(this.array[to])
    }

    async swap(index_one, index_two) {
        if (index_one > index_two) {
            let a = index_one;
            index_one = index_two
            index_two = a 
        }

        let divs = board.childNodes;
        this.highlight(divs[index_one])
        this.highlight(divs[index_two])
        
        divs[index_one].parentNode.insertBefore(divs[index_one], divs[index_two]);
        divs[index_two].parentNode.insertBefore(divs[index_two], divs[index_one]);

        [this.array[index_one], this.array[index_two]] = [
            this.array[index_two], this.array[index_one]];
        this.sound(this.array[index_two])
        await this.sound(this.array[index_one])
    }

    highlight(obj) {
        var orig = obj.style.color;
        obj.style.backgroundColor = '#f00';
        setTimeout(function(){
                obj.style.backgroundColor = orig;
        }, 200 * this.getSpeed());
    }

    length() {
        return this.array.length
    }

    getSpeed() {
        return 1 / (document.getElementById("speed").value / 10)
    }

    async sound(number) { 
        let freq =  this.min + number * this.multiplier;
        beep(0.5, freq)
        await sleep(this.slowdown * this.getSpeed())
    }
}

board = document.getElementById("sorting-visual")
sound_array = new SoundArray(100, board);
// sound_array.swap(8, 0)


/**
 * 
 * @param {SoundArray} array 
 */
async function bubbleSort(array) {
    array.slowdown = 200
    for(var i = 0; i <= array.length()-1; i++){
        // Last i elements are already in place
        for(var j = 0; j < ( array.length() - i -1); j++){

            // Comparing two adjacent numbers 
            // and see if first is greater than second
            if(await array.get(j) > await array.get(j+1)){

            // Swap them if the condition is true 
            await array.swap(j, j+1)
            }
        }
    }
}

/**
 * 
 * @param {SoundArray} array 
 */
async function selectionSort(array) {
    array.slowdown = 2
    for (let i = 0; i < array.length(); i++) {
      let lowest = i
      for (let j = i + 1; j < array.length(); j++) {
        if (await array.get(j) < await array.get(lowest)) {
          lowest = j
        }
      }
      if (lowest !== i) {
        await array.swap(i, lowest)
      }
    }
    return arr
  }

/**
 * 
 * @param {SoundArray} array 
 */
async function insertionSort(array)  
{  
    let n = array.length();
    let i, key, j;  
    for (i = 1; i < n; i++) 
    {  
        key = array.get(i);  
        j = i - 1;  
    
        /* Move elements of arr[0..i-1], that are  
        greater than key, to one position ahead  
        of their current position */
        while (j >= 0 && array.get(j) > key) 
        {  
            array.swap(j+1, j)
            j = j - 1;  
        }
        arr[j + 1] = key;  
    }  
}

/**
 * 
 * @param {SoundArray} array 
 */
function quicksort(array) {
    if (array.length() <= 1) {
      return array;
    }
  
    var pivot = array.get(0);
    
    var left = []; 
    var right = [];
  
    for (var i = 1; i < array.length(); i++) {
      array[i] < pivot ? left.push(array[i]) : right.push(array[i]);
    }
  
    return quicksort(left).concat(pivot, quicksort(right));
  };;
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
