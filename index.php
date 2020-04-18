<?php
/*
Project Description: This website will help teach you how to read and understand kanji characters.
API Used:
-Words API: Used for getting dictionary of words
-Learn to read and write Japanese kanji API: Get the translated words and other useful information about the word in kanji
-Google Cloud Vision: Used for the write method in learning to recognize the user input
 * */
?>

<?php
//Master Layout Header
require_once "master/header.php";
?>
<link rel="stylesheet" href="styles/index.css">
<div class="mainContent">
    <p>A place where you can learn Japanese Kanji by reading and writing.</p>
</div>
<div class="row">
    <div class="col-lg-4">
        <form action="dictionary.php" method="post">
            <button type="submit" name="dictionary" class="btn btn-primary float-right dictionaryBtn">Dictionary</button>
        </form>
    </div>
    <div class="col-lg-4">
        <form action="learning.php" method="post">
            <button type="submit" name="learning" class="btn btn-primary learningBtn">Learning</button>
        </form>
    </div>
</div>
<?php
//Master Layout Footer
require_once "master/footer.php";
?>
