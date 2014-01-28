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
require_once '../conf/DbConnectorW.php';

$db=mysqli_connect(DB_HOST,DB_USER,DB_PASSWORD,DB_NAME)
		or die('<p><font color=red>Fehler bei der Datenbankverbindung: '.
		mysqli_connect_errno().': '.mysqli_connect_error().'</p>');	

$id = $_POST['id'];

$sql="UPDATE LNDL SET Closed = 1 WHERE DL_ID = $id LIMIT 1";
                  
mysqli_query($db, $sql);

$sql = "SELECT User_has_Groups.Groups_ID, User.Name, User.ID FROM User, User_has_Groups WHERE LOWER(`Name`) = '" . mysqli_real_escape_string($db, $_SESSION['benutzername']) . "' and User.ID = User_has_Groups.User_ID";
$result = mysqli_query($db, $sql);
$zeile=mysqli_fetch_array($result);


$sql = "INSERT INTO Log (`Time`, `Activity`, `User_ID`, `Groups_ID`) VALUES (NOW(), '$zeile[Name] Closed Link with ID $id', $zeile[ID], $zeile[Groups_ID])";
mysqli_query($db, $sql);

mysqli_close($db); 
?>
<?php
        } else {resetUser();}
mysqli_close($conid);
?>