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

if (!empty($_POST['name'])){

require_once '../conf/DbConnectorW.php';
    
$db=mysqli_connect(DB_HOST,DB_USER,DB_PASSWORD,DB_NAME)
		or die('<p><font color=red>Fehler bei der Datenbankverbindung: '.
		mysqli_connect_errno().': '.mysqli_connect_error().'</p>');

$sql = "INSERT INTO Series (Ser_Name, Author_Auth_ID) VALUES ('$_POST[name]',$_POST[author])";

mysqli_query($db, $sql);

$sql = "SELECT User_has_Groups.Groups_ID, User.Name, User.ID FROM User, User_has_Groups WHERE LOWER(`Name`) = '" . mysqli_real_escape_string($db, $_SESSION['benutzername']) . "' and User.ID = User_has_Groups.User_ID";
$result = mysqli_query($db, $sql);
$zeile=mysqli_fetch_array($result);

$sql = "SELECT MAX(`Ser_ID`) AS Ser_ID FROM Series";
$result = mysqli_query($db, $sql);
$zeile2=mysqli_fetch_array($result);


$sql = "INSERT INTO Log (`Time`, `Activity`, `User_ID`, `Groups_ID`) VALUES (NOW(), '$zeile[Name] added Creator with ID $zeile2[Ser_ID]', $zeile[ID], $zeile[Groups_ID])";
mysqli_query($db, $sql);


mysqli_close($db);

?><div align="center"><p><font color=green><?php echo $_POST['name'] ?> was added to the DB.</p><p><input type="button" value="Close" onclick="window.parent.TINY.box.hide()" ></p></div><?php

} else {
    ?><div align="center"><p><font color=red>You forgot the Name.</p><p><input type="button" value="Close" onclick="window.parent.TINY.box.hide()" ></p></div><?php
}
?>
