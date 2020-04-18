<?php
//Learning Mode: There are two different method
//Reading: different difficulty based on grade level and user must choose the correct translated English word on the screen.
//Writing: English word(s) displayed and user must write the kanji version
session_start();
session_destroy();
?>

<?php
//Master Layout Header
require_once "master/header.php";
?>

<h2>Learning</h2>

<h3>Please select the learning method</h3>
<div class="row">
    <div class="col-lg-3">
        <form action="reading.php" method="post">
            <button type="submit" name="Learning" class="btn btn-primary">Reading</button>
        </form>
    </div>
    <div class="col-lg-3">
        <form action="drawing.php" method="post">
            <button type="submit" name="Drawing" class="btn btn-primary">Writing</button>
        </form>
    </div>
</div>

<?php
//Master Layout Footer
require_once "master/footer.php";
?>

