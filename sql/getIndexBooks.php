<?php

// Erzwingen das Session-Cookies benutzt werden und die SID nicht per URL transportiert wird
ini_set( 'session.use_only_cookies', '1' );
ini_set( 'session.use_trans_sid', '0' );

// Session starten
session_start();

// Funktionen einbinden
include( '../admin/auth.php' );

// Datenbankverbindung öffnen
$conid = db_connect();

// Benutzer prüfen
if (!checkUser( $conid ))
{
	resetUser();
}

// Benutzer abmelden
if ($_GET['benutzer'] == 'abmelden')
{
	resetUser();
}

?>
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
echo "<div onclick='getInfo($zeile[Book_ID]);'><p>Volume " . $zeile['Book_Vol'] . " -- " . $zeile['Book_Name'] . "</p></div>";

if (isset($zeile['Book_Name']) & empty($zeile['Book_Vol'])) 
echo "<div onclick='getInfo($zeile[Book_ID]);'><p>" . $zeile['Book_Name'] . "</p></div>";

if (empty($zeile['Book_Name']) & isset($zeile['Book_Vol'])) 
echo "<div onclick='getInfo($zeile[Book_ID]);'><p>Volume " . $zeile['Book_Vol'] . "</p></div>";

if (empty($zeile['Book_Name']) & empty($zeile['Book_Vol'])) 
echo "<div onclick='getInfo($zeile[Book_ID]);'><p>" . $zeile['Ser_Name'] . "</p></div>";
                  
 } 
mysqli_close($db); 
?>