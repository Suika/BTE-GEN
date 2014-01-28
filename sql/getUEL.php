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

$sql="SELECT Name FROM User, User_has_Groups WHERE LOWER(`Name`) = '" . mysqli_real_escape_string($conid, $_SESSION['benutzername']) . "' AND User_has_Groups.User_ID = User.ID AND User.Closed = 0";

        $userresult = mysqli_query($conid, $sql);
        
        if(mysqli_num_rows($userresult) == 1){
?>
<?php
require_once '../conf/DbConnector.php';

$SID = $_POST['series'];

$db=mysqli_connect(DB_HOST,DB_USER,DB_PASSWORD,DB_NAME)
		or die('<p><font color=red>Fehler bei der Datenbankverbindung: '.
		mysqli_connect_errno().': '.mysqli_connect_error().'</p>');	

$sql="SELECT ID FROM User, User_has_Groups WHERE LOWER(`Name`) = '" . mysqli_real_escape_string($db, $_SESSION['benutzername']) . "' AND User_has_Groups.User_ID = User.ID AND User.Closed = 0";
$result2 = mysqli_query($db, $sql);
$zeile2=mysqli_fetch_array($result2);

$sql="SELECT DL_ID, DL_Link, Book_Vol, Book_Name FROM LNDL, Book, Series WHERE LNDL.User_ID = $zeile2[ID] AND Series.Ser_ID = $SID AND LNDL.Book_Book_ID = Book.Book_ID AND Book.Series_Ser_ID = Series.Ser_ID AND LNDL.Closed = 0";
                  
$result = mysqli_query($db, $sql);

while ($zeile=mysqli_fetch_array($result)){

$title = (!empty($zeile['Book_Vol'])) ? "Volume " . $zeile['Book_Vol'] : null;
$title .= (!empty($zeile['Book_Name'])) ? " - " . $zeile['Book_Name'] : null;    
    
echo "<div class=\"content\"><div class=\"ENAME\"><div class=\"SNAME\"><a href=\"" . urldecode($zeile['DL_Link']) . "\" title=\"" . urldecode($zeile['DL_Link']) . "\">" . $title . "</a></div><div class=\"ANAME\"><input type=\"button\" onclick=\"TINY.box.show({iframe:'../sql/getEditLink.php?lndlid=$zeile[DL_ID]',boxid:'frameless',width:600,height:300,fixed:false,maskid:'graymask',maskopacity:40,closejs:function(){closeJS()}})\" value=\"Edit\"><input type=\"button\" value=\"Remove\" onClick=\"closeUserLink($zeile[DL_ID]);\"></div></div></div>";
 } 
mysqli_close($db); 
?>
<?php
        } else {resetUser();}
mysqli_close($conid);
?>