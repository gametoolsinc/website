
function getRandomColor() {
    return getRandomRgb()
    let number = Math.random() * 2
    if (number < 1) {
        return getRandomHsl()
    } else {
        return getRandomRgb()
    }
}

function getRandomHsl() {
    let color = [Math.round(Math.random() * 361), Math.round(Math.random() * 101), Math.round(Math.random() * 101)]
    let text = "hsl(" + color[0] + ", " + color[1] + "%, " + color[2] + "%)"
    return text;
}

function getRandomRgb() {
    let color = [Math.floor(Math.random() * 256), Math.floor(Math.random() * 256), Math.floor(Math.random() * 256)]
    let text = "rgb(" + color[0] + ", " + color[1] + ", " + color[2] + ")"
    return text;
}

function generate() {
    let name = document.getElementById("name");
    let color = getRandomColor()
    name.innerHTML = color

    let elements = document.querySelectorAll('#random-values > div')
    let correct = Math.floor(Math.random() * elements.length)
    console.log(elements)
    console.log(correct + " is correct")
    for (let i = 0; i < elements.length; i++) {
        let element = elements[i]
        if (i == correct) {
            element.style.backgroundColor = color
        } else {
            let colorOption = getRandomColor()
            console.log(colorOption)
            element.style.backgroundColor = colorOption
        }
    }
}

function check(element) {
    let name = document.getElementById("name");
    console.log(name.innerHTML)
    console.log(element.style.backgroundColor)
    if (name.innerHTML == element.style.backgroundColor) {
        console.log("Correct, you genious")
        increaseGoodScore()
        generate()
    } else {
        console.log("WRONG, you fool!")
        increaseWrongScore()
    }
}

function increaseWrongScore() {
    let wrong = document.getElementById("wrong")
    wrong.innerHTML = parseInt(wrong.innerHTML) + 1
}

function increaseGoodScore() {
    let correct = document.getElementById("correct")
    correct.innerHTML = parseInt(correct.innerHTML) + 1
}

generate()

