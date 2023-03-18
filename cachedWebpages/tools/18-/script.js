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
let TIME_INTERVAL = 10 // ms
let DISTANCE = 1000 // TIME_INTERVAL

let time = 0;
let start_timer = undefined;

let running = true
class Simulation {
    constructor(main_element, source_type) {
        this.time = 0
        this.main_element = main_element
        this.time_differences = []
        this.source_type = source_type
        this.chart = undefined
        this.timer = this.main_element.getElementsByClassName("timer")[0];
        this.main_container = this.main_element.getElementsByClassName("main-source")[0];
        this.stream1 = this.main_element.getElementsByClassName("split-1")[0];
        this.stream2 = this.main_element.getElementsByClassName("split-2")[0];
        this.total = this.main_element.getElementsByClassName("total")[0];
        this.setup()
        var t = this
        this.simulation = window.setInterval(function () {
            t.simulate()
        }, TIME_INTERVAL);
        this.graph = window.setInterval(function () {
            t.show_information()
        }, 1000);
    }

    setup() {
        document.getElementById('style').innerHTML += '.photon{animation: ' + DISTANCE + 'ms linear 0s slide;}';
        this.show_information()
    }

    simulate() {
        this.time += TIME_INTERVAL
        this.source()
    }

    source() {
        let amount = this.source_type()
        const element = this.get_photon(amount)
        this.main_container.appendChild(element)
        let t = this
        setTimeout(function () {
            element.remove();
            t.splitter(amount)
        }, DISTANCE - 10);
    }

    splitter(amount) {
        
        let split_one = Math.floor(Math.random() * (amount + 1))
        let split_two = amount - split_one

        let t = this

        const element_one = this.get_photon(split_one)
        this.stream1.appendChild(element_one)
        setTimeout(function () {
            element_one.remove();
            t.detector(split_one, -1)
        }, DISTANCE - 10);

        const element_two = this.get_photon(split_two)
        this.stream2.appendChild(element_two)
        setTimeout(function () {
            element_two.remove();
            t.detector(split_two, 1)
        }, DISTANCE - 10);
    }

    detector(amount, side) {
        if (amount > 0) {
            // if (side == 1) {
            //     this.start_timer = this.time
            // }
            // if (side == -1) {
                let time_difference = this.time - this.start_timer;
                this.time_differences.push(time_difference)
                this.timer.innerHTML = time_difference
                this.start_timer = this.time
            // }
        }
    }

    get_photon(amount) {
        const element = document.createElement("div");
        if (amount > 0) {
            element.className += "photon";
        } else {
            element.className += "no-photon"
        }
        const text = document.createTextNode(amount);
        element.appendChild(text)
        let light = (255 * Math.pow(0.5, amount))
        element.style.backgroundColor = "rgb(" + light + "," + light + "," + light + ")";
        return element
    }

    sleep(ms) {
        return new Promise(resolve => setTimeout(resolve, ms));
    }

    show_information() {
        this.total.innerHTML = this.time_differences.length
        const counts = {};
        this.time_differences.forEach(function (x) { counts[x] = (counts[x] || 0) + 1; });

        let values = []
        for (let count in counts) {
            values.push({ x: count, y: counts[count] })
        }

        if (this.chart != undefined) {
            this.chart.destroy()
        }

        this.chart = new Chart(this.main_element.getElementsByClassName('chart-canvas')[0], {
            type: "scatter",
            data: {
                datasets: [{
                    pointRadius: 4,
                    pointBackgroundColor: "rgba(0,0,255,1)",
                    data: values
                }],
            },
            options: {
                animation: {
                    duration: 0
                },
                scales: {
                    x: {
                        display: true,
                        title: {
                            display: true,
                            text: 'time difference'
                        },
                        suggestedMin: 0,
                    },
                    y: {
                        display: true,
                        title: {
                            display: true,
                            text: 'amount'
                        },
                        suggestedMin: 0,
                    }
                }
            }
        });
    }
}

class SinglePhotonSource {
    constructor(excitation_rate, decay_rate){
        this.excitation_rate = excitation_rate
        this.decay_rate = decay_rate
        this.excited = false
        this.chance = Math.random();
        this.time = 0
    }

    get_photon(){
        let chance;
        if (this.excited){
            chance = Math.pow(Math.E, -this.decay_rate*this.time)
        } else {
            chance = Math.pow(Math.E, -this.excitation_rate*this.time)
        }
        this.time += TIME_INTERVAL
        if (chance < this.chance){
            this.time = 0
            this.chance = Math.random();
            this.excited = !this.excited
            if (this.excited){
                return 1
            }
        }
        return 0
    }
}

function random_numer(λ) {
    let number = Math.floor(Math.random() * (λ + 1))
    return number
}

function poisson_random_number(λ) {
    let L = Math.pow(Math.E, -λ)
    let k = 0
    let p = 1.
    while (p > L) {
        k = k + 1
        let u = Math.random()
        p = p * u
    }
    return k - 1
}


const average_amount = 0.5
const random = new Simulation(document.getElementById("random"), random_numer.bind(null, average_amount))
const coherent_source = new Simulation(document.getElementById("coherent_source"), poisson_random_number.bind(null, average_amount))
let source = new SinglePhotonSource(0.25*average_amount, 0.25*average_amount)
const single_source = new Simulation(document.getElementById("single_source"), source.get_photon.bind(source));
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
