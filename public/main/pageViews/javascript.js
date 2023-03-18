
async function place_graph() {
    // Get data
    const response = await fetch("/library/client/view/views.json");
    const data = await response.json();

    // convert data to coordinates
    let datesets = []

    let pages = {
        "total": {
            "data": []
        }
    }
    for (let date in data) {
        let total = 0
        for (let page in data[date]) {
            if (!(page in pages)) {
                pages[page] = {
                    "data": []
                }
            }
            total += data[date][page]
            pages[page]["data"].push({
                x: date,
                y: data[date][page]
            })
        }
        pages["total"]["data"].push({
            x: date,
            y: total
        })
    }
    console.log(pages)

    let datasets = []
    for (let page in pages) {
        let borderColor = 'rgb(' + 255 * Math.random() + ', ' + 255 * Math.random() + ', ' + 255 * Math.random() + ')'
        let data = pages[page]["data"].sort((a,b) => new Date(a.x) - new Date(b.x));
        datasets.push({
            label: page,
            data: data,
            fill: false,
            borderColor: borderColor,
            tension: 0.3
        })
    }

    console.log(datasets)

    let chart = new Chart(document.getElementById('chart'), {
        type: "line",
        data: {
            // labels: labels,
            datasets: datasets
        },
        options: {
            legend: {
                display: false
            },
            plugins: {
                legend: {
                    display: false
                },
            },
            scales: {
                x: {
                    display: true,
                    title: {
                        display: true,
                        text: 'Date'
                    },
                    type: 'time',
                    // time: {
                    //     displayFormats: {
                    //         quarter: 'MMM YYYY'
                    //     }
                    // }
                },
                y: {
                    display: true,
                    title: {
                        display: true,
                        text: 'Views'
                    },
                    suggestedMin: 0,
                },
                // xAxes: [{
                //     type: 'time',
                //     distribution: 'linear'
                // }]
            }
        }
    });
}

place_graph()