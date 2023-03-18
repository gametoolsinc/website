let past_scores = [[8000, 8000]]

// Input

function getInputValue() {
    let output_div = document.getElementById("output")
    let output = output_div.getElementsByTagName("input")[0]
    return parseInt(output.value);
}

function setInputValue(value) {
    let output_div = document.getElementById("output")
    let output = output_div.getElementsByTagName("input")[0]
    output.value = value
}

function addToInput(number) {
    setInputValue(getInputValue() * Math.pow(10, number.length) + parseInt(number))
}

function resetInput() {
    setInputValue(0)
}

function backspaceInput() {
    let new_value = Math.floor(getInputValue() / 10);
    setInputValue(new_value)
}

// Score

function updateScoreboard() {
    for (let i = 0; i < 2; i++) {
        let score = past_scores[past_scores.length - 1][i]
        let previous_score = score;
        for (let j = past_scores.length - 1; j >= 0; j--) {
            if (past_scores[j][i] != score) {
                previous_score = past_scores[j][i]
                break;
            }
        }
        let scores = document.getElementsByClassName("score")
        scores[i].textContent = score
        let last_score_div = document.getElementsByClassName("last-score")[i]
        let text = score - previous_score
        last_score_div.classList.remove("negative")
        last_score_div.classList.remove("positive")
        if (text > 0) {
            last_score_div.classList.add("positive")
            text = "+" + text
        } else if (text < 0) {
            last_score_div.classList.add("negative")
        }
        last_score_div.textContent = text
    }
}

function getScores() {
    let scores = document.getElementsByClassName("score")
    return [parseInt(scores[0].textContent), parseInt(scores[1].textContent)]
}

function undoLastScore() {
    if (past_scores.length > 1) {
        past_scores.pop()
    }
    update()
}

function addScore(scoreboard_number, multiplier) {
    let new_score = getScores()[scoreboard_number] + getInputValue() * multiplier;
    let scores = past_scores[past_scores.length - 1].slice()
    scores[scoreboard_number] = new_score
    past_scores.push(scores)
    update()
    resetInput()
}

// Past scores

function resetPastScores() {
    past_scores = [[8000, 8000]]
    update()
}

function toggleShowLastScores(hide = false) {
    let history = document.getElementById("history")
    let history_button = document.getElementById("history-button")
    if (history.style.display == "block" || hide) {
        history.style.display = "none";
        history_button.classList.remove("active")
    } else {
        toggleShowRandom(hide = true)
        history.style.display = "block";
        history_button.classList.add("active")
    }
}

function updateShowLastScores() {
    let history = document.getElementById("scores")
    history.innerHTML = ""
    for (let i = 0; i < past_scores.length; i++) {
        let score = past_scores[i]
        let element = document.createElement("p")
        element.textContent = score[0] + "-" + score[1]
        history.appendChild(element)
    }
}

// Random

function toggleShowRandom(hide = false) {
    let random = document.getElementById("random")
    let random_button = document.getElementById("random-button")
    if (random.style.display == "block" || hide) {
        random.style.display = "none";
        random_button.classList.remove("active")
    } else {
        toggleShowLastScores(hide = true)
        random.style.display = "block";
        random_button.classList.add("active")
    }
}



function update() {
    updateScoreboard()
    updateShowLastScores()
}

function reset() {
    resetPastScores()
    resetInput()
    update()
}

reset()