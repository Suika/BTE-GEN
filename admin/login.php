<?php
// Fehlermeldungen unterdrücken
error_reporting(0);

// Erzwingen das Session-Cookies benutzt werden und die SID nicht per URL transportiert wird
ini_set('session.use_only_cookies', '1');
ini_set('session.use_trans_sid', '0');

// Session starten
session_start();

// Sicherstellen das die SID durch den Server vergeben wurde
// um einen möglichen Session Fixation Angriff unwirksam zu machen
if (!isset($_SESSION['server_SID'])) {
    // Möglichen Session Inhalt löschen
    session_unset();
    // Ganz sicher gehen das alle Inhalte der Session gelöscht sind
    $_SESSION = array();
    // Session zerstören
    session_destroy();
    // Session neu starten
    session_start();
    // Neue Server-generierte Session ID vergeben
    session_regenerate_id();
    // Status festhalten
    $_SESSION['server_SID'] = true;
}

// Funktionen einbinden
include( 'auth.php' );

// Variablen deklarieren
$_SESSION['angemeldet'] = false;
$conid = '';
$eingabe = array();
$anmeldung = false;
$update = false;
$fehlermeldung = '';

// Datenbankverbindung öffnen
$conid = db_connect();

// Wenn das Formular abgeschickt wurde
if (isset($_POST['login'])) {
    // Benutzereingabe bereinigen
    $eingabe = cleanInput();
    // Benutzer anmelden
    $anmeldung = loginUser($eingabe['benutzername'], $eingabe['passwort'], $conid);

    // Anmeldung war korrekt
    if ($anmeldung) {
        // Benutzer Identifikationsmerkmale in DB speichern
        $update = updateUser($eingabe['benutzername'], $conid);
        // Bei erfolgreicher Speicherung
        if ($update) {
            // Auf geheime Seite weiterleiten
            mysqli_close($conid);
            header('location: index.php');
            exit;
        } else {
            $fehlermeldung = '<h3>Bei der Anmeldung ist ein Problem aufgetreten!</h3>';
        }
    } else {
        $fehlermeldung = '<h3>Die Anmeldung war fehlerhaft!</h3>';
    }
}

if (isset($_POST['register']) & !empty($_POST['rbenutzer']) & !empty($_POST['rpasswort']) & !empty($_POST['rkey'])) {

    $db = $conid;

    $eingabe = cleanInputRegister();

    $sql = "SELECT `Name` FROM `User` WHERE LOWER(`Name`) = '" . mysqli_real_escape_string($db, $eingabe['checkname']) . "' LIMIT 1";
    $result = mysqli_query($db, $sql);

    if (mysqli_num_rows($result) == 0) {

        $sql = "SELECT ID, OTP_Used, Groups_ID FROM OTP WHERE OTP_Key = '" . mysqli_real_escape_string($db, $eingabe['key']) . "' LIMIT 1";
        $result = mysqli_query($db, $sql) or die('Verbindungsfehler!');
        $zeile = mysqli_fetch_array($result);

        if (mysqli_num_rows($result) > 0 & $zeile['OTP_Used'] == 0) {

            $sql = "UPDATE `OTP` SET `OTP_Used` = 1 WHERE OTP_Key = '" . mysqli_real_escape_string($db, $eingabe['key']) . "' LIMIT 1";
            mysqli_query($db, $sql) or die('Verbindungsfehler!');

            $pseudo = pseudo_rand(128);
            $hash = create_hash($eingabe['passwort'], $pseudo);

            $sql = "INSERT INTO `User`(`Name`, `Password`, `Salt`, `Created`, `OTP_ID`) 
               VALUES ('" . mysqli_real_escape_string($db, $eingabe['benutzername']) . "', '" . $hash . "', '" . $pseudo . "', NOW(), " . $zeile['ID'] . ")";
            mysqli_query($db, $sql) or die('Verbindungsfehler!');

            $sql = "SELECT MAX(`ID`) AS User_ID FROM User";
            $result = mysqli_query($db, $sql) or die('Verbindungsfehler!');
            $maxuser = mysqli_fetch_array($result);

            $sql = "INSERT INTO `User_has_Groups` (`User_ID`, `Groups_ID`) VALUES ('" . $maxuser['User_ID'] . "', '" . $zeile['Groups_ID'] . "');";
            mysqli_query($db, $sql) or die('Verbindungsfehler!');
            
            $fehlermeldung = '<h3>Registration Complete</h3>';
        } else {
            $fehlermeldung = '<h3>Trying to use a wrong key ? *eyes of pity*</h3>';
        }
    } else {
        $fehlermeldung = '<h3>One of the fieds is wrong... But I won\'t say which.</h3>';
    }
}


mysqli_close($conid);
?>
<!doctype html>
<html lang="en">
    <head>
        <meta charset="utf-8" />

        <!-- disable zooming -->
        <meta name="viewport" content="initial-scale=1.0, user-scalable=0" />

        <link rel="stylesheet" href="../css/card-style.css" media="screen" />
    </head>
    <body>
<?php
// Falls die Fehlermeldung gesetzt ist
if ($fehlermeldung)
    echo '<center style="color: white">' . $fehlermeldung . '</center>';
?>
        <section class="container">
            <div id="card">
                <figure class="front">
                    <form method="post" class="signin" action="/admin/login.php">
                        <fieldset class="textbox">
                            <label class="benutzer">
                                <span>Username</span>
                                <input id="benutzer" name="benutzer" value="" type="text" autocomplete="on" placeholder="Username">
                            </label>
                            <label class="passwort">
                                <span>Password</span>
                                <input id="passwort" name="passwort" value="" type="password" placeholder="Password">
                            </label>
                            <button class="submit button" name="login" type="submit">Sign in</button>
                        </fieldset>
                    </form>
                    <button id="flip" class="submit button">To Registration</button>
                </figure>
                <figure class="back">
                    <form method="post" class="signin" action="/admin/login.php">
                        <fieldset class="textbox">
                            <label class="benutzer">
                                <span>Username</span>
                                <input id="rbenutzer" name="rbenutzer" value="" type="text" autocomplete="on" placeholder="Username">
                            </label>
                            <label class="passwort">
                                <span>Password</span>
                                <input id="rpasswort" name="rpasswort" value="" type="password" placeholder="Password">
                            </label>
                            <label class="benutzer">
                                <span>Key</span>
                                <input id="rkey" name="rkey" value="" type="text" autocomplete="off" placeholder="Key">
                            </label>
                            <button class="submit button" name="register" type="submit">Register</button>

                        </fieldset>
                    </form>
                </figure>
            </div>
        </section>

        <script src="../js/utils.js"></script>
        <script src="../js/flip-card.js"></script>
    </body>
</html>
<?php
/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
?>
