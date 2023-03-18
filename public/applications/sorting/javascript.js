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
  };