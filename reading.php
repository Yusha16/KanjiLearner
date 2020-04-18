<?php
session_start();

require_once 'vendor/autoload.php';

//Make the request and return the response
function GetResponse($url, $headers) {
    $response = "";
    //Weird error for connection, so try again until connected
    while ($response == "") {
        try {
            Unirest\Request::timeout(1);
            $response = Unirest\Request::get($url, $headers);
        } catch (Exception $e) {
            //echo 'Caught exception: ',  $e->getMessage(), "\n";
            $response = "";
        }
    }
    return $response;
}

//Set up all the kanjis we will use and store it in session
function SetUpKanjis($responseBody) {
    $kanjis = array();
    foreach ($responseBody as $element) {
        $kanjis[] = $element->kanji->character;
    }
    $_SESSION['kanjis'] = $kanjis;
}

//Return a string for the meaning of the kanji
function GetMeaning($kanji) {
    //Must convert the kanji string to Percent encoding for URIs
    $kanji = urlencode($kanji); //訪 = %E8%A8%AA
    $url = "https://kanjialive-api.p.rapidapi.com/api/public/kanji/$kanji";
    $headers = array(
        "X-RapidAPI-Host" => "kanjialive-api.p.rapidapi.com",
        "X-RapidAPI-Key" => "bd0a600553msh6cf129de33f85ccp1b304cjsn6afa3b116c75"
    );
    $response = GetResponse($url, $headers);
    return $response->body->kanji->meaning->english;
}

//Return a string from a random word in the WordsAPI
function GetRandomWord() {
    $url = "https://wordsapiv1.p.rapidapi.com/words/?random=true";
    $headers = array(
        "X-RapidAPI-Host" => "wordsapiv1.p.rapidapi.com",
        "X-RapidAPI-Key" => "bd0a600553msh6cf129de33f85ccp1b304cjsn6afa3b116c75"
    );
    $response = GetResponse($url, $headers);
    return $response->body->word;
}

//Set up the 4 words in the page also setting up the correct word they have to choose
function SetUpQuestion() {
    //Get the english meaning for 1 of the kanjis
    $randomIndex = rand(0, sizeof($_SESSION['kanjis']) - 1);
    $_SESSION['kanjiWord'] = $_SESSION['kanjis'][$randomIndex];
    //Get the meaning in an array (Ex. 風 => "wind, manner")
    $correctWords = explode(",", GetMeaning($_SESSION['kanjis'][$randomIndex]));
    $correctWord = $correctWords[rand(0, sizeof($correctWords) - 1)];
    //Save the correct word
    $_SESSION['correctWord'] = $correctWord;

    //The words we will show to the user
    $words = array();
    $words[] = $correctWord;
    while (sizeof($words) < 4) {
        $randomWord = GetRandomWord();
        //Make sure the random word is not in the array
        if (!in_array($randomWord, $words)) {
            $words[] = $randomWord;
        }
    }
    //Randomize the order of the array
    shuffle($words);

    //Save the words
    $_SESSION['words'] = $words;
}

//When user submits the form to choose a grade level
if (isset($_POST['level'])) {
    $_SESSION['gradeLevel'] = $_POST['gradeLevel'];
    $gradeLevel = $_SESSION['gradeLevel'];

    //Set up the kanji's to be used
    $url = "https://kanjialive-api.p.rapidapi.com/api/public/search/advanced/?grade=$gradeLevel";
    $headers = array(
        "X-RapidAPI-Host" => "kanjialive-api.p.rapidapi.com",
        "X-RapidAPI-Key" => "bd0a600553msh6cf129de33f85ccp1b304cjsn6afa3b116c75"
    );
    $response = GetResponse($url, $headers);
    SetUpKanjis($response->body);
    SetUpQuestion();
}

//When user click on the Go Back button
if (isset($_POST['back'])) {
    session_destroy();
    header("Location: learning.php");
    exit;
}

$outputMessage = "";
if (isset($_SESSION['gradeLevel'])) {
    //Now check when user click on the button if they click on the correct word
    if (isset($_POST['option0']) && $_SESSION['words'][0] == $_SESSION['correctWord']) {
        $outputMessage = "You are correct";
    }
    else if (isset($_POST['option1']) && $_SESSION['words'][1] == $_SESSION['correctWord']) {
        $outputMessage = "You are correct";
    }
    else if (isset($_POST['option2']) && $_SESSION['words'][2] == $_SESSION['correctWord']) {
        $outputMessage = "You are correct";
    }
    else if (isset($_POST['option3']) && $_SESSION['words'][3] == $_SESSION['correctWord']) {
        $outputMessage = "You are correct";
    }
    else if (isset($_POST['option0']) || isset($_POST['option1']) || isset($_POST['option2']) || isset($_POST['option3'])) {
        $outputMessage = "You are incorrect";
    }
}

if (isset($_POST['next'])) {
    SetUpQuestion();
}

?>


<?php
//Master Layout Header
require_once "master/header.php";
?>

<link rel="stylesheet" href="styles/reading.css">
<h2>Reading</h2>
<?php
if (!isset($_SESSION['gradeLevel'])) {
?>
    <div>
        <form action="" method="post">
            <div>
                <label for="gradeLevel">Please select a grade level: </label>
                <select id="gradeLevel" name="gradeLevel">
                    <option value="1">1</option>
                    <option value="2">2</option>
                    <option value="3">3</option>
                    <option value="4">4</option>
                    <option value="5">5</option>
                    <option value="6">6</option>
                </select>
            </div>
            <button type="submit" name="level" class="btn btn-primary">Submit</button>
        </form>
    </div>
<?php
}
else {
?>
    <div>
        <h3>Grade Level: <?= $_SESSION['gradeLevel'] ?></h3>
        <form action="" method="post">
            <h4>Select the correct translated kanji word</h4>
            <div class="row">
                <div class="col-lg-3 kanjiQuestion">
                    <?= $_SESSION['kanjiWord'] ?>
                </div>
                <div class="col-lg-6 row">
                    <button type="submit" name="option0" class="btn btn-danger col-lg-6 wordOption"><?= $_SESSION['words'][0] ?></button>
                    <button type="submit" name="option1" class="btn btn-success col-lg-6 wordOption"><?= $_SESSION['words'][1] ?></button>
                    <button type="submit" name="option2" class="btn btn-warning col-lg-6 wordOption"><?= $_SESSION['words'][2] ?></button>
                    <button type="submit" name="option3" class="btn btn-info col-lg-6 wordOption"><?= $_SESSION['words'][3] ?></button>
                </div>
                <div class="col-lg-3 resultMessage">
                    <?= $outputMessage ?>
                </div>
            </div>
        </form>
        <div>
            <form action="" method="post">
                <button type="submit" name="next" class="btn btn-primary">Next Word</button>
            </form>
        </div>
    </div>
<?php
}
?>

<?php
//Master Layout Footer
require_once "master/footer.php";
?>