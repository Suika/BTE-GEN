<?php
require_once '../conf/DbConnector.php';

$db=mysqli_connect(DB_HOST,DB_USER,DB_PASSWORD,DB_NAME)
		or die('<p><font color=red>Fehler bei der Datenbankverbindung: '.
		mysqli_connect_errno().': '.mysqli_connect_error().'</p>');	

//connect to your database

$term = trim(strip_tags($_GET['term']));//retrieve the search term that autocomplete sends

$sql = "SELECT Book.Book_ID, Series.Ser_ID, Series.Ser_Name, Book.Book_Name, Book.Book_Vol
FROM Book, Series, Meta
WHERE Book.Series_Ser_ID = Series.Ser_ID
AND Book.Meta_Meta_ID = Meta.Meta_ID
AND Series.Ser_Name LIKE '%".$term."%'
ORDER BY Series.Ser_Name, Book.Book_Vol";

$result = mysqli_query($db, $sql);

while ($zeile=mysqli_fetch_array($result)){
    $title = $zeile['Ser_Name'];
    $title .= (!empty($zeile['Book_Vol'])) ? " - Volume " . $zeile['Book_Vol'] : null;
    $title .= (!empty($zeile['Book_Name'])) ? " - " . $zeile['Book_Name'] : null;
    
    $row['value']=htmlentities(stripslashes($title));
    $row['id']=(int)$zeile['Book_ID'];
    $row['sid']=(int)$zeile['Ser_ID'];
    $row_set[] = $row;//build an array
}

echo json_encode($row_set);//format the array into json data
?>
