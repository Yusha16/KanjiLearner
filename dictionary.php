<?php
//Dictionary Mode: In this mode you can type in any English Word and it will
// translate the word to Japanese (kanji) and give you a basic definition in English.

# These code snippets use an open-source library. http://unirest.io/python

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

//Get all the kanji's from the english word
function GetAllKanji($responseBody) {
    $results = array();
    foreach ($responseBody as $element) {
        if (!in_array($element->kanji->character, $results)) {
            $results[] = $element->kanji->character;
        }
        /*
        //Radical are not included
        if (!in_array($element->radical->character, $results)) {
            $results[] = $element->radical->character;
        }
        */
    }
    return $results;
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
    $response =GetResponse($url, $headers);
    return $response->body->kanji->meaning->english;
}

$errorMessage = "";
$searchWord = "";
$kanjiWords = array();
$definitions = array();

if (isset($_POST['searchWord'])) {
    $searchWord = $_POST['searchWord'];
}

//When user click on the Define button
if (isset($_POST['define'])) {
    //Validate the word
    if (!isset($_POST['searchWord']) || $_POST['searchWord'] == ""){
        $errorMessage = "Please enter a word";
    }
    else {
        $searchWord = strtolower($searchWord);
        $url = "https://kanjialive-api.p.rapidapi.com/api/public/search/advanced/?rem=$searchWord";
        $headers = array(
            "X-RapidAPI-Host" => "kanjialive-api.p.rapidapi.com",
            "X-RapidAPI-Key" => "bd0a600553msh6cf129de33f85ccp1b304cjsn6afa3b116c75"
        );
        $response = GetResponse($url, $headers);

        //var_dump($response->body);

        //Check if the response has a error message (invalid input or not in the database)
        if (isset($response->error)) {
            $errorMessage = $response->error;
        }
        else if ($response->body == []) {
            $errorMessage = "No kanji found";
        }
        //There was a kanji(s) found
        else {
            $kanjiWords = GetAllKanji($response->body);
            //var_dump($kanjiWords);

            //Must loop through all the kanji words
            $definitions = array();
            foreach($kanjiWords as $kanjiWord) {
                $definitions[] = GetMeaning($kanjiWord);
            }
        }
    }
}
?>

<?php
//Master Layout Header
require_once "master/header.php";
?>

<link rel="stylesheet" href="styles/dictionary.css">

<h2>Dictionary</h2>
<div class="row">
    <div class="col-lg-4">
        <form action="" method="post">
            <h3>Please enter a english word</h3>
            <div class="form-group">
                <label for="searchWord">Word: </label>
                <input name="searchWord" type="text" value="<?= $searchWord ?>"/>
                <div class="error">
                    <?= $errorMessage ?>
                </div>
            </div>
            <button type="submit" name="define" class="btn btn-primary">Define</button>
        </form>
    </div>
    <div class="col-lg-4 resultTable">
        <!-- Header Row -->
        <?php
        if (sizeof($kanjiWords) > 0) {
        ?>
            <div class="row resultHeader">
                <div class="col-lg-3">Kanji</div>
                <div class="col-lg-9">Definition</div>
            </div>
        <?php
        }
        ?>
        <!-- Body Content -->
        <?php
        for ($i = 0; $i < sizeof($kanjiWords); $i++) {
        ?>
            <div class="row resultBody">
                <div class="col-lg-3"><?= $kanjiWords[$i] ?></div>
                <div class="col-lg-9"><?= $definitions[$i] ?></div>
            </div>
        <?php
        }
        ?>
    </div>
</div>
<?php
//Master Layout Footer
require_once "master/footer.php";
?>