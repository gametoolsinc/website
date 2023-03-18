const content = document.getElementById('content')

function newPart(){
    const div = document.createElement('div');
    div.className = "input"
    let html = document.getElementById("template").innerHTML;
    html = html.replaceAll("{number}", content.children.length)
    div.innerHTML = html;
    
    content.appendChild(div);
}

function auto_grow(element) {
    element.style.height = "50px";
    element.style.height = (element.scrollHeight)+"px";
}