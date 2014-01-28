<?php
// Erzwingen das Session-Cookies benutzt werden und die SID nicht per URL transportiert wird
ini_set('session.use_only_cookies', '1');
ini_set('session.use_trans_sid', '0');

// Session starten
session_start();

// Funktionen einbinden
include( '../admin/auth.php' );

// Datenbankverbindung öffnen
$conid = db_connect();

// Benutzer prüfen
if (!checkUser($conid)) {
    resetUser();
}

// Benutzer abmelden
if ($_GET['benutzer'] == 'abmelden') {
    resetUser();
}
?>
<?php
if (!empty($_POST['dlln'])) {

    require_once '../conf/DbConnectorW.php';

    $db = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME)
            or die('<p><font color=red>Fehler bei der Datenbankverbindung: ' .
                    mysqli_connect_errno() . ': ' . mysqli_connect_error() . '</p>');

    $sql = "SELECT User_has_Groups.Groups_ID, User.Name, User.ID FROM User, User_has_Groups WHERE LOWER(`Name`) = '" . mysqli_real_escape_string($db, $_SESSION['benutzername']) . "' and User.ID = User_has_Groups.User_ID";
    $result = mysqli_query($db, $sql);
    $zeile = mysqli_fetch_array($result);

    $url = urlencode($_POST['dlln']);

    if (!empty($_POST['books'])) {
        $com = urlencode($_POST['comment']);
    } else {
        $com = '';
    }

    $sql = "INSERT INTO LNDL (DL_Link, DL_Com, Book_Book_ID, Lang_Lang_ID, Extentions_Ext_ID, Creator_Creator_ID, User_ID, Created) VALUES ('$url', '$com', $_POST[books], $_POST[lang], $_POST[type], $_POST[creator], $zeile[ID], NOW())";
    mysqli_query($db, $sql);

    $sql = "SELECT MAX(`DL_ID`) AS DL_ID FROM LNDL";
    $result = mysqli_query($db, $sql);
    $zeile2 = mysqli_fetch_array($result);


    $sql = "INSERT INTO Log (`Time`, `Activity`, `User_ID`, `Groups_ID`) VALUES (NOW(), '$zeile[Name] added Link with ID $zeile2[DL_ID]', $zeile[ID], $zeile[Groups_ID])";
    mysqli_query($db, $sql);

    mysqli_close($db);
    ?><div align="center"><p><font color=green>The Link was added to the DB.</p><p><input type="button" value="Close" onclick="window.parent.TINY.box.hide()" ></p></div><?php
} else {
    ?><div align="center"><p><font color=red>You forgot to the Link.</p><p><input type="button" value="Close" onclick="window.parent.TINY.box.hide()" ></p></div><?php
}
?>
