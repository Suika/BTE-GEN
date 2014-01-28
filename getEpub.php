<?php

require_once 'conf/DbConnector.php';
include_once 'epub.php';

$epub = new epub();

$SID = $_POST['series'];
$title = $_POST['books'];
$noImages = $_POST['img'];
$resolution = $_POST['size'];

/*
  $post_series = 21;
  $post_books = "Golden_Time:Volume1";
  $post_img = "wi";
  $post_size = "original";
 */

$db = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME) or die('<p><font color=red>Fehler bei der Datenbankverbindung: ' . mysqli_connect_errno() . ': ' . mysqli_connect_error() . '</p>');

$sql = "SELECT  Book_ID, 
                Author.Auth_Name,  
                Series.Ser_Name, 
                Book.Book_Name, 
                Book.Book_Vol, 
                Meta.Title, 
                Meta.Translator, 
                Meta.Editor, 
                Meta.Creator, 
                Meta.Description, 
                Meta.ISBN, 
                Meta.UUID,
                Meta.UUIDI 
            FROM Author, Book, Meta, Series 
            WHERE Book.Series_Ser_ID = " . $SID . " 
            AND Book.Meta_Meta_ID = Meta.Meta_ID 
            AND Book.Author_Auth_ID = Author.Auth_ID 
            AND Book.Series_Ser_ID = Series.Ser_ID 
            AND Meta.Title = \"" . $title . "\"";

$result = mysqli_query($db, $sql);
$zeile = mysqli_fetch_array($result);
mysqli_close($db);

$fileName = $zeile['Ser_Name'];
$fileName .= (!empty($zeile['Book_Vol'])) ? " - Volume " . $zeile['Book_Vol'] : null;
$fileName .= (!empty($zeile['Book_Name'])) ? " - " . $zeile['Book_Name'] : null;

$epub->setTitle($title);
$epub->setAuthor($zeile['Auth_Name']);
$epub->setTranslator($zeile['Translator']);
$epub->setDate(date("Y-m-d"));
$epub->setEditor("Simon");
//$epub->setIllustrator("Tetsuo");
$epub->setIsbn($zeile['ISBN']);
$epub->setLanguage("en");
$epub->setPublisher("Baka-Tsuki");
$epub->setResolution($resolution);
$epub->setRights("All materials' copyrights reserved by their respective authors and the associated publishers. Please respect their rights. Works will be deleted upon request by copyright holders.");
$epub->setBID($zeile['Book_ID']);
$epub->setSID($SID);
$epub->setFileName($fileName);


if ($noImages == "wi") {
    $epub->setNoImages(FALSE);
    $epub->setUuid($zeile['UUIDI']);
} elseif ($noImages == "ni") {
    $epub->setNoImages(TRUE);
    $epub->setUuid($zeile['UUID']);
}


$epub->processEpub();
?>
