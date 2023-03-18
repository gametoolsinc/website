function rollDice(){
    let number = Math.ceil(Math.random() * 6)
    let dices = document.getElementsByClassName("dice")
    for (let index in dices){
        let dice = dices[index]
        dice.innerHTML = "<img class='dice-roll' src='/module/dice/faces/"+number+".svg'>"
    }
}

rollDice()