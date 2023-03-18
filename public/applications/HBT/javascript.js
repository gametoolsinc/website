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
const single_source = new Simulation(document.getElementById("single_source"), source.get_photon.bind(source))