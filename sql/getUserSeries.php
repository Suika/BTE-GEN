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

$user = $_POST['user'];

$db=mysqli_connect(DB_HOST,DB_USER,DB_PASSWORD,DB_NAME)
		or die('<p><font color=red>Fehler bei der Datenbankverbindung: '.
		mysqli_connect_errno().': '.mysqli_connect_error().'</p>');	

$sql="SELECT Series.Ser_ID, Series.Ser_Name, User.ID AS User_ID FROM Series, User, LNDL, Book  WHERE LNDL.User_ID = $user AND LNDL.Book_Book_ID = Book.Book_ID AND Series.Ser_ID = Book.Series_Ser_ID AND User.is_Founder = 0 AND LNDL.Closed = 0 GROUP BY Series.Ser_ID";
                  
$result = mysqli_query($db, $sql);

while ($zeile=mysqli_fetch_array($result)){    
echo "<div onclick='getBwL($user,$zeile[Ser_ID]);' class=\"content\"><center>" . $zeile['Ser_Name'] . "</center></div>";
 } 
mysqli_close($db); 
?>
<?php
        } else {resetUser();}
mysqli_close($conid);
?>