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

$sql="SELECT Name FROM User, User_has_Groups WHERE LOWER(`Name`) = '" . mysqli_real_escape_string($conid, $_SESSION['benutzername']) . "' AND User_has_Groups.Groups_ID = 1 AND User_has_Groups.User_ID = User.ID AND User.Closed = 0";

        $userresult = mysqli_query($conid, $sql);
        
        if(mysqli_num_rows($userresult) == 1){
?>
<?php
require_once '../conf/DbConnector.php';

$UID = $_POST['user'];
$SID = $_POST['series'];

$db=mysqli_connect(DB_HOST,DB_USER,DB_PASSWORD,DB_NAME)
		or die('<p><font color=red>Fehler bei der Datenbankverbindung: '.
		mysqli_connect_errno().': '.mysqli_connect_error().'</p>');	

$sql="SELECT DL_ID, DL_Link, Name, Book_Name, Book_Vol, Ser_Name FROM LNDL, User, Book, Series WHERE LNDL.User_ID = User.ID AND LNDL.Book_Book_ID = Book.Book_ID AND Book.Series_Ser_ID = Series. Ser_ID AND LNDL.Visible = 0 AND LNDL.Closed = 0";
                  
$result = mysqli_query($db, $sql);

while ($zeile=mysqli_fetch_array($result)){
    
$title = $zeile['Ser_Name'];
$title .= (!empty($zeile['Book_Vol'])) ? " - Volume " . $zeile['Book_Vol'] : null;
$title .= (!empty($zeile['Book_Name'])) ? " - " . $zeile['Book_Name'] : null;

echo "<div><center><p>$zeile[Name]: <a href=\"" . urldecode($zeile['DL_Link']) . "\" title=\"" . urldecode($zeile['DL_Link']) . "\">" . $title . "</a><input type=\"button\" value=\"Aprove\" onClick=\"aproveLink($zeile[DL_ID]);\" text-align=\"right\"><input type=\"button\" value=\"Close\" onClick=\"closeLink($zeile[DL_ID]);\" text-align=\"right\"></p></center></div>";
 } 
mysqli_close($db); 
?>
<?php
        } else {resetUser();}
mysqli_close($conid);
?>