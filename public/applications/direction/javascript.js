function updatePosition() {
    // Calculate direction
    let startX = document.getElementById("startX").value
    let startZ = document.getElementById("startZ").value
    let endX = document.getElementById("endX").value
    let endZ = document.getElementById("endZ").value

    let dx = startX - endX
    let dz = endZ - startZ

    let direction = Math.atan2(dx, dz)
    let distance = Math.sqrt(dx * dx + dz * dz)

    // dz 1 geeft 0
    // dx 1 geeft -90
    // dx -1 geeft 90
    // dz -1 geeft 180

    // draw on canvas
    draw(direction)

    // Update text
    document.getElementById("direction-number").innerHTML = Math.round(direction / Math.PI * 180 * 10) / 10 + "°"
    document.getElementById("length-number").innerHTML = Math.round(distance)

    // Update times
    update_movement_time(distance)
}

function update_movement_time(distance) {
    let movementOutputs = document.getElementsByClassName("movement-types")[0].children
    for (let i = 0; i < movementOutputs.length; i++) {
        let item = movementOutputs[i];
        let time_item = item.getElementsByClassName("movement-time")[0]
        let speed = time_item.getAttribute("speed")
        let time_seconds = distance / speed

        let periods = {
            "second": 1,
            "minute": 60,
            "hour": 3_600,
            "day": 86_400,
            "week": 604_800,
            "year": 31_536_000,
            "decade": 315_569_520,
            "century": 3_155_695_200,
            "millennium": 31_557_600_000,
            "giga-annum": 3.15576e+16
        }
        let period = "second"
        for (let key in periods){
            if (time_seconds > periods[key]){
                period = key
            }
        }

        let time = time_seconds / periods[period]
        if (String(Math.ceil(time)).length < 2){
            time = Math.ceil(time * 10) / 10
        } else {
            time = Math.ceil(time)
        }
        if (time != 1){
            period += "s"
        }
        time_item.innerHTML = time + " "+ period
    }
}

function draw(direction) {
    let canvas = document.getElementById("direction");
    let ctx = canvas.getContext("2d");
    ctx.setTransform(1, 0, 0, 1, 0, 0);
    ctx.clearRect(0, 0, canvas.width, canvas.height);
    let radius = canvas.height / 2;
    ctx.translate(radius, radius);
    radius = radius * 0.90

    draw_cicle(ctx, radius)
    // drawNumbers(ctx, radius);
    draw_pointer(ctx, direction, radius, 15)
}

function draw_cicle(ctx, radius) {
    let grad;

    ctx.beginPath();
    ctx.arc(0, 0, radius, 0, 2 * Math.PI);
    ctx.fillStyle = 'white';
    ctx.fill();

    grad = ctx.createRadialGradient(0, 0, radius * 0.95, 0, 0, radius * 1.05);
    grad.addColorStop(0, '#a1a1a1');
    grad.addColorStop(0.5, '#a1a1a1');
    grad.addColorStop(1, '#a1a1a1');
    ctx.strokeStyle = grad;
    ctx.lineWidth = radius * 0.1;
    ctx.stroke();

    ctx.beginPath();
    ctx.arc(0, 0, radius * 0.1, 0, 2 * Math.PI);
    ctx.fillStyle = '#333';
    ctx.fill();
}

function draw_numbers(ctx, radius) {
    let ang;
    let num;
    ctx.font = radius * 0.15 + "px arial";
    ctx.textBaseline = "middle";
    ctx.textAlign = "center";
    let positions = ["90", "0", "270", "180"]
    for (num = 0; num < positions.length; num++) {
        ang = num * 2 * Math.PI / positions.length;
        ctx.rotate(ang);
        ctx.translate(0, -radius * 0.85);
        ctx.rotate(-ang);
        ctx.fillText(positions[num], 0, 0);
        ctx.rotate(ang);
        ctx.translate(0, radius * 0.85);
        ctx.rotate(-ang);
    }
}

function draw_pointer(ctx, pos, length, width) {
    ctx.strokeStyle  = "black"
    ctx.beginPath();
    ctx.lineWidth = width;
    ctx.lineCap = "round";
    ctx.moveTo(0, 0);
    ctx.rotate(pos);
    ctx.lineTo(0, -length);
    ctx.stroke();
    ctx.rotate(-pos);
}


updatePosition()