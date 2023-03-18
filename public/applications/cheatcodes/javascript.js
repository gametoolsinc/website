var url_string = window.location.href
var url = new URL(url_string);
var platform = url.searchParams.get("platform");
var select = document.getElementsByName("platform")[0]
console.log(platform)
if (platform == undefined){
    select.value = [...select.options].map( opt => opt.value )[0]
} else {
    select.value = platform
}

selectPlatform(select)

function selectPlatform(element){
    platforms = [...element.options].map( opt => opt.value )
    for (var i = 0; i < platforms.length; i++){
        var items = document.getElementsByClassName(platforms[i])
        for (item = 0; item < items.length; item += 1) {
            if (platforms[i] == element.value){
                items[item].style.display = 'block';
            } else {
                items[item].style.display = 'none';
            }
        }
    }
    url.searchParams.set('platform', element.value);
    window.history.pushState({}, '', url);
}
