<?php
require_once '../conf/DbConnector.php';

$ser = $_POST['series'];

$db=mysqli_connect(DB_HOST,DB_USER,DB_PASSWORD,DB_NAME)
		or die('<p><font color=red>Fehler bei der Datenbankverbindung: '.
		mysqli_connect_errno().': '.mysqli_connect_error().'</p>');	

$sql="SELECT Book.Book_ID, Book.Book_Name, Book.Book_Vol, Series.Ser_Name
        FROM Book, Series
        WHERE Book.Series_Ser_ID = " . $ser . "  
        AND Series.Ser_ID = " . $ser . " 
        ORDER BY Book.Book_Vol";
                  
$result = mysqli_query($db, $sql);

while ($zeile=mysqli_fetch_array($result)){    

if (isset($zeile['Book_Name']) & isset($zeile['Book_Vol'])) 
echo "<option value=" . $zeile['Book_ID']  . ">Volume " . $zeile['Book_Vol'] . " -- " . $zeile['Book_Name'] . "</option>";

if (isset($zeile['Book_Name']) & empty($zeile['Book_Vol'])) 
echo "<option value=" . $zeile['Book_ID']  . ">" . $zeile['Book_Name'] . "</option>";

if (empty($zeile['Book_Name']) & isset($zeile['Book_Vol'])) 
echo "<option value=" . $zeile['Book_ID']  . ">Volume " . $zeile['Book_Vol'] . "</option>";

if (empty($zeile['Book_Name']) & empty($zeile['Book_Vol'])) 
echo "<option value=" . $zeile['Book_ID']  . ">" . $zeile['Ser_Name'] . "</option>";
                  
 } 
mysqli_close($db); 
?>