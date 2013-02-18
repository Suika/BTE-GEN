<?php

require_once 'conf/DbConnector.php';
include_once 'epub.php';

$epub = new epub();

$SID = $_POST['series'];
$title = $_POST['books'];
$noImages = $_POST['img'];
$resolution = $_POST['size'];

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
