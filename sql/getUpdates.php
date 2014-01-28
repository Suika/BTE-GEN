<?php

require_once '../conf/DbConnector.php';

$ser = $_POST['series'];

$db = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME)
        or die('<p><font color=red>Fehler bei der Datenbankverbindung: ' .
                mysqli_connect_errno() . ': ' . mysqli_connect_error() . '</p>');

$sql = "SELECT Book_ID, Ser_ID, Ser_Name, Book_Vol, Book_Name
FROM Book, Series, Meta 
WHERE Series.Ser_ID = Book.Series_Ser_ID
AND Meta.Meta_ID = Book.Meta_Meta_ID
ORDER BY Meta.Meta_ID DESC
LIMIT 10";

$result = mysqli_query($db, $sql);

while ($zeile = mysqli_fetch_array($result)) {
    if (isset($zeile['Book_Name']) & isset($zeile['Book_Vol']))
        echo "<div id=\"content\"><div class=\"ENAME\" align=\"center\"><p onClick=\"searchBook(" . $zeile['Book_ID'] . "," . $zeile['Ser_ID'] . ");\">" . $zeile['Ser_Name'] . " -- Volume " . $zeile['Book_Vol'] . " -- " . $zeile['Book_Name'] . "</p></div></div>";

    if (isset($zeile['Book_Name']) & empty($zeile['Book_Vol']))
        echo "<div id=\"content\"><div class=\"ENAME\" align=\"center\"><p onClick=\"searchBook(" . $zeile['Book_ID'] . "," . $zeile['Ser_ID'] . ");\">" . $zeile['Ser_Name'] . " -- " . $zeile['Book_Name'] . "</p></div></div>";

    if (empty($zeile['Book_Name']) & isset($zeile['Book_Vol']))
        echo "<div id=\"content\"><div class=\"ENAME\" align=\"center\"><p onClick=\"searchBook(" . $zeile['Book_ID'] . "," . $zeile['Ser_ID'] . ");\">" . $zeile['Ser_Name'] . " -- Volume " . $zeile['Book_Vol'] . "</p></div></div>";

    if (empty($zeile['Book_Name']) & empty($zeile['Book_Vol']))
        echo "<div id=\"content\"><div class=\"ENAME\" align=\"center\"><p onClick=\"searchBook(" . $zeile['Book_ID'] . "," . $zeile['Ser_ID'] . ");\">" . $zeile['Ser_Name'] . "</p></div></div>";
}
mysqli_close($db);
?>