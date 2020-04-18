//Base Model of drawing on the canvas is referenced from:
//https://www.codicode.com/art/how_to_draw_on_a_html5_canvas_with_a_mouse.aspx
//https://stackoverflow.com/questions/2368784/draw-on-html5-canvas-using-a-mouse

var canvas;
var ctx;
var mousePressed = false;
var lastX = 0;
var lastY = 0;
var strokes = [];
//Index to the array stroke
var currentStroke = -1;
var kanjis = [];
var englishWord = "";
var kanji = "";

//Line Class
function Line(startX, startY, endX, endY) {
    this.startX = startX;
    this.startY = startY;
    this.endX = endX;
    this.endY = endY;
}

window.onload = function() {
    canvas = document.getElementById('kanjiCanvas');
    ctx = canvas.getContext("2d");

    $('#kanjiCanvas').mousedown(function (e) {
        mousePressed = true;
        //Start a new stroke
        strokes.push([]);
        currentStroke++;
        Draw(e.pageX - $(this).offset().left, e.pageY - $(this).offset().top, false);
    });

    $('#kanjiCanvas').mousemove(function (e) {
        //Only draw when the mouse is down
        if (mousePressed) {
            var x = e.pageX - $(this).offset().left;
            var y = e.pageY - $(this).offset().top;
            //Store the line
            strokes[currentStroke].push(new Line(lastX, lastY, x, y));
            Draw(x, y, true);
        }
    });

    $('#kanjiCanvas').mouseup(function (e) {
        mousePressed = false;
    });

    $('#kanjiCanvas').mouseleave(function (e) {
        mousePressed = false;
    });

    //Prepare the array of kanjis to use
    var settings = {
        "async": true,
        "crossDomain": true,
        "url": "https://kanjialive-api.p.rapidapi.com/api/public/kanji/all",
        "method": "GET",
        "headers": {
            "x-rapidapi-host": "kanjialive-api.p.rapidapi.com",
            "x-rapidapi-key": "bd0a600553msh6cf129de33f85ccp1b304cjsn6afa3b116c75"
        }
    }
    $.ajax(settings).done(function (response) {
        for (let i = 0; i < response.length; i++) {
            kanjis.push({
                kanji: response[i]['kanji']['character'],
                english: response[i]['kanji']['meaning']['english']
            });
        }
        NewQuestion();
    });
}

//Set up the next question
function NewQuestion() {
    $('#result').hide();
    $('#resultMessage').text("");
    let randomIndex = Math.floor(Math.random() * kanjis.length);
    $('#englishWord').text(kanjis[randomIndex].english);
    englishWord = kanjis[randomIndex].english;
    kanji = kanjis[randomIndex].kanji;
}

function Draw(x, y, isDown) {
    if (isDown) {
        ctx.beginPath();
        ctx.strokeStyle = "black";
        ctx.lineWidth = 2;
        ctx.lineJoin = "round";
        ctx.moveTo(lastX, lastY);
        ctx.lineTo(x, y);
        ctx.closePath();
        ctx.stroke();
    }
    lastX = x;
    lastY = y;
}

function Clear() {
    //Clear the canvas
    ctx.clearRect(0, 0, canvas.width, canvas.height);
    //Clear out the strokes
    strokes = [];
    currentStroke = -1;
    mousePressed = false;
}

function Check() {
    var dataURL = canvas.toDataURL();
    document.getElementById("canvasImg").src = dataURL;
    //Hide the saved image
    document.getElementById("canvasImg").style.display = "none";

    //Download the saved image
    //var image = canvas.toDataURL("image/png").replace("image/png", "image/octet-stream");  // here is the most important part because if you dont replace you will get a DOM 18 exception.
    //window.location.href = image;

    //Sudo Code for the planned code

    //Here we have to call the Google Cloud Vision for detecting text in images (OCR)

    //After getting the response we compare it with the "kanji" variable

    //if the value are the same clear the canvas and call using JQuery show the result element

    //if the value are not the same still show the result (to show message of incorrect)
    //Note hide the newQuestion button
}

function RemoveLastStroke() {
    ctx.clearRect(0, 0, canvas.width, canvas.height);
    strokes.pop();
    currentStroke--;
    //Redraw all the strokes
    for (let i = 0; i < strokes.length; i++) {
        for (let j = 0; j < strokes[i].length; j++) {
            ctx.beginPath();
            ctx.strokeStyle = "black";
            ctx.lineWidth = 2;
            ctx.lineJoin = "round";
            ctx.moveTo(strokes[i][j].startX, strokes[i][j].startY);
            ctx.lineTo(strokes[i][j].endX, strokes[i][j].endY);
            ctx.closePath();
            ctx.stroke();
        }
    }
}
