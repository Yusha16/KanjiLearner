<?php
//Must export the canvas image to get the recognition
//Use google cloud
//https://cloud.google.com/vision/docs/handwriting?apix_params=%7B%22resource%22%3A%7B%22requests%22%3A%5B%7B%22features%22%3A%5B%7B%22type%22%3A%22DOCUMENT_TEXT_DETECTION%22%7D%5D%2C%22image%22%3A%7B%22source%22%3A%7B%22imageUri%22%3A%22gs%3A%2F%2Fvision-api-handwriting-ocr-bucket%2Fhandwriting_image.png%22%7D%7D%7D%5D%7D%7D#vision-document-text-detection-php
?>

<?php
//Master Layout Header
require_once "master/header.php";
?>

<link rel="stylesheet" href="styles/drawing.css">
<script type="text/javascript" src="scripts/canvas.js"></script>

<h2>Writing</h2>

<img id="canvasImg" src="">

<h3>Write the kanji version of the english word(s)</h3>
<div class="row">
    <div id="englishWord" class="col-lg-3">

    </div>
    <div class="col-lg-3">
        <canvas id="kanjiCanvas" width="300" height="300"></canvas>
        <input type="button" value="Remove Last Stroke" class="btn btn-warning" onclick="RemoveLastStroke()">
        <input type="button" value="Clear" class="btn btn-danger" onclick="Clear()">
        <input type="button" value="Check" class="btn btn-success" onclick="Check()">
    </div>
    <div id="result" class="col-lg-3">
        <div id="resultMessage">
        </div>
        <div>
            <input id="newQuestion" type="button" value="Next Word(s)" class="btn btn-primary" onclick="NewQuestion()">
        </div>
    </div>
</div>

<?php
//Master Layout Footer
require_once "master/footer.php";
?>