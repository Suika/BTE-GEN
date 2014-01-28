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

$db=mysqli_connect(DB_HOST,DB_USER,DB_PASSWORD,DB_NAME)
		or die('<p><font color=red>Fehler bei der Datenbankverbindung: '.
		mysqli_connect_errno().': '.mysqli_connect_error().'</p>');	

$sql="SELECT `ID`, `Name` FROM `User` WHERE `Closed` = 0 AND `is_Founder` = 0";
                  
$result = mysqli_query($db, $sql);

while ($zeile=mysqli_fetch_array($result)){    
echo "<div class=\"content\"><div class=\"ENAME\" onclick='getSwL($zeile[ID]);' class=\"content\"><div class=\"SNAME\">" . $zeile['Name'] . "</div> <div class=\"ANAME\"><input type=\"button\" value=\"Close\" onClick=\"closeUser($zeile[ID]);\"></div></div></div>";
 } 
mysqli_close($db); 
?>
<?php
        } else {resetUser();}
mysqli_close($conid);
?>