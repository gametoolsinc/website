function throwCoin(){
    let number = Math.floor(Math.random() * 2)
    let coins = document.getElementsByClassName("coin")
    let images = {
        0: "heads.svg",
        1: "tails.svg"
    }
    for (let index in coins){
        let coin = coins[index]
        coin.innerHTML = "<img class='coin-throw' src='/module/coin_flip/faces/"+images[number]+"'>"
    }
}

throwCoin()