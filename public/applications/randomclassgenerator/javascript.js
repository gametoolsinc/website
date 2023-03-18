//Call function for random class
var data;
function randomClass() {
    var xhr = new XMLHttpRequest();
    xhr.open("POST", "/tools/randomclassgenerator/randomclass.php");
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
            <img src="{img src}" onerror="this.onerror=null; this.src='/data/codcoldwar/user_interface/attachments/not_found.png'">
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
    firstWeaponHTML = firstWeaponHTML.replace(/{img url}/g, '/data/codcoldwar/user_interface/weapons/' + firstWeaponName.format());
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
    secondWeaponHTML = secondWeaponHTML.replace(/{img url}/g, '/data/codcoldwar/user_interface/weapons/' + secondWeaponName.format());
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
        string = string.replace(/{img url}/g, '/data/codcoldwar/user_interface/equipment/' + classData["class"]["equipment"][type].format());
        equipmentString += string;
    }
    equipmentHTML = equipmentHTML.replace(/{items}/g, equipmentString);

    //---------------------------//
    //Wildcard & Perks
    var other = otherHTML.replace(/{name}/g, classData["class"]["wildcard"]["name"]);
    other = other.replace(/{img url}/g, '/data/codcoldwar/user_interface/wildcards/' + classData["class"]["wildcard"]["name"].format());
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
            var imgSrc = '/data/codcoldwar/user_interface/attachments/' + type.toLowerCase() + '/' + weaponData[type].format();
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
        string += '<img draggable="false" src="{src}" alt="">'.replace(/{src}/g, '/data/codcoldwar/user_interface/perks/' + array[number]["name"].format());
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