function showConsentBanner() {
    document.getElementById("consent-banner").style.display = "block"
}

function closeConsentBanner() {
    document.getElementById("consent-banner").style.display = "none"
}

function openConsentBanner() {
    document.getElementById("consent-banner").style.display = ""
}

function consentBannerAgree() {
    consentUpdateOptions(true, true, true, true)
    closeConsentBanner()
}

function consentBannerDecline() {
    consentUpdateOptions(true, false, false, false)
    closeConsentBanner()
}

function showPreferences() {
    closeConsentBanner()
    document.getElementById("consent-preferences").style.display = "block"
}

function closePreferences() {
    document.getElementById("consent-preferences").style.display = "none"
}

function savePreferences() {
    var strictlyNecessary = true
    var functionality = document.getElementsByName("consent-preferences-functionality")[0].checked
    var tracking = document.getElementsByName("consent-preferences-tracking")[0].checked
    var targeting = document.getElementsByName("consent-preferences-targeting")[0].checked
    consentUpdateOptions(strictlyNecessary, functionality, tracking, targeting)
}

function consentUpdateOptions(strictlyNecessary, functionality, tracking, targeting) {
    setCookie("consent", JSON.stringify([strictlyNecessary, functionality, tracking, targeting]), 10*365)
    consentUpdateOption("strictly-necessary", strictlyNecessary)
    consentUpdateOption("functionality", functionality)
    consentUpdateOption("tracking", tracking)
    consentUpdateOption("targeting", targeting)
}

function consentUpdateOption(option, allowed) {
    var scripts = document.querySelectorAll('[cookie-consent="' + option + '"]');
    for (var i = 0; i < scripts.length; i++) {
        var script = scripts[i]
        var new_script = script.cloneNode(true)
        if (allowed) {
            new_script.setAttribute("type", "text/javascript")
        } else {
            new_script.setAttribute("type", "text/plain")
        }
        script.replaceWith(new_script)
    }
}

function setCookie(name, value, days) {
    const d = new Date();
    d.setTime(d.getTime() + (days * 24 * 60 * 60 * 1000));
    let expires = "expires=" + d.toUTCString();
    document.cookie = name + "=" + value + ";" + expires + ";path=/";
}

function getCookie(cname) {
    let name = cname + "=";
    let decodedCookie = decodeURIComponent(document.cookie);
    let ca = decodedCookie.split(';');
    for (let i = 0; i < ca.length; i++) {
        let c = ca[i];
        while (c.charAt(0) == ' ') {
            c = c.substring(1);
        }
        if (c.indexOf(name) == 0) {
            return c.substring(name.length, c.length);
        }
    }
    return "";
}

cookie = getCookie("consent")
if (cookie != ""){
    options = JSON.parse(cookie)
    consentUpdateOptions(options[0], options[1], options[2], options[3])
    closeConsentBanner()
} else {
    openConsentBanner()
}