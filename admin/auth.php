<?php

/* * ************************** */
/* * ** Datenbankverbindung *** */
/* * ************************** */

function db_connect() {
    // Zugangsdaten für die DB
    $dbhost = 'sql171.your-server.de';
    $dbuser = 'mmchan_btds_w';
    $dbpass = 'h17HeT5G';
    $dbname = 'mmchan_btds';
    // Verbindung herstellen und Verbindungskennung zurück geben
    $conid = mysqli_connect($dbhost, $dbuser, $dbpass, $dbname) or die('Verbindungsfehler!');

    return $conid;
}

/* * ********************************* */
/* * ** Benutzereingabe bereinigen *** */
/* * ********************************* */

function cleanInput() {
    // Maskierende Slashes aus POST Array entfernen
    if (get_magic_quotes_gpc()) {
        $eingabe['benutzername'] = stripslashes($_POST['benutzer']);
        $eingabe['passwort'] = stripslashes($_POST['passwort']);
    } else {
        $eingabe['benutzername'] = $_POST['benutzer'];
        $eingabe['passwort'] = $_POST['passwort'];
    }
    // Trimmen
    $eingabe['benutzername'] = trim($eingabe['benutzername']);
    $eingabe['passwort'] = trim($eingabe['passwort']);
    // In Kleinschrift umwandeln
    $eingabe['benutzername'] = strtolower($eingabe['benutzername']);
    // Eingabe zurückgeben
    return $eingabe;
}

/* * ************************ */
/* * ** Benutzer anmelden *** */
/* * ************************ */

function loginUser($benutzer, $passwort, $conid) {
    // Anweisung zusammenstellen
    $sql = "SELECT `Salt`, `Password`, `Fail` FROM `User` WHERE LOWER(`Name`) = '" . mysqli_real_escape_string($conid, $benutzer) . "' AND `Closed` = 0";
    // Anweisung an DB schicken
    $ergebnis = mysqli_query($conid, $sql);
    // Wurde ein Datensatz gefunden, existiert dieser Benutzername, also
    // prüfen wir ob die Anmeldedaten korrekt ist
    if (mysqli_num_rows($ergebnis) == 1) {
        $datensatz = mysqli_fetch_array($ergebnis);
        // Resourcen freigeben
        mysqli_free_result($ergebnis);
        // Anmeldepasswort vorbereiten
        $zusatz = $datensatz['Salt'];
        $hashed_pass = $datensatz['Password'];
        $anmeldepw = validateLogin($passwort, $hashed_pass, $zusatz);
        // Prüfen ob ein Datensatz gefunden wurde. In dem Fall stimmen die Anmeldedaten
        if ($anmeldepw == TRUE) {
            // Counter für Fehlversuche resetten
            if ($datensatz['Fail'] != 0) {
                $sql = "UPDATE `User` SET `Fail` = 0 WHERE LOWER(`Name`) = '" . mysqli_real_escape_string($conid, $benutzer) . "' LIMIT 1";
                mysqli_query($conid, $sql);
            }
            // Korrekte Anmeldung zurückgeben
            return true;
        } else {
            // Das angegebene Passwort war nicht korrekt, also gehen wir von einem Angriffsversuch aus
            // und erhöhen den Counter der fehlerhaften Anmeldeversuche
            $sql = "UPDATE `User` SET `Fail` = `Fail` + 1 WHERE LOWER(`Name`) = '" . mysqli_real_escape_string($conid, $benutzer) . "' LIMIT 1";
            mysqli_query($conid, $sql);
            // Abfragen ob das Limit von 10 Fehlversuche erreicht wurde und in diesem Fall ...
            $sql = "SELECT `Fail` FROM `User` WHERE LOWER(`Name`) = '" . mysqli_real_escape_string($conid, $benutzer) . "'";
            $ergebnis = mysqli_query($conid, $sql);
            $anzahl = mysqli_fetch_array($ergebnis);
            mysqli_free_result($ergebnis);
            // ... das Konto deaktivieren
            if ($anzahl['Fail'] > 9) {
                $sql = "UPDATE `User` SET `Fail` = 0, `Closed` = 1 WHERE LOWER(`Name`) = '" . mysqli_real_escape_string($conid, $benutzer) . "' LIMIT 1";
                mysqli_query($conid, $sql);
            }
        }
    }
}

/* * *************************************** */
/* * ** Benutzer Datensatz aktualisieren *** */
/* * *************************************** */

function updateUser($benutzer, $conid) {
    // Benutzer-Datensatz aktualisieren
    $sql = "UPDATE `User` SET `ip` = '" . mysqli_real_escape_string($conid, $_SERVER['REMOTE_ADDR']) . "', 
            `Info` = '" . mysqli_real_escape_string($conid, $_SERVER['HTTP_USER_AGENT']) . "', 
            `registration` = '" . mysqli_real_escape_string($conid, md5($_SERVER['REQUEST_TIME'])) . "', 
            `Last_Login` = NOW()
            WHERE LOWER(`Name`) = '" . mysqli_real_escape_string($conid, $benutzer) . "'
			LIMIT 1";
    mysqli_query($conid, $sql);
    // Prüfen ob der datensatz aktualisiert wurde
    if (mysqli_affected_rows($conid) == 1) {
        // Session Variablen setzen
        $_SESSION['angemeldet'] = true;
        $_SESSION['benutzername'] = $benutzer;
        $_SESSION['anmeldung'] = md5($_SERVER['REQUEST_TIME']);
        return true;
    }
}

/* * ********************************** */
/* * ** Status des Benutzers prüfen *** */
/* * ********************************** */

function checkUser($conid) {
    // Alte Session löschen und Sessiondaten in neue Session transferieren
    session_regenerate_id();
    if ($_SESSION['angemeldet'] !== true){
        return false;}
    // Benutzerdaten aus DB laden
    $sql = "SELECT `IP`, `Info`, `registration`, UNIX_TIMESTAMP(`Last_Login`) as Last_Login FROM `User` 
        WHERE `Name` = '" . mysqli_real_escape_string($conid, $_SESSION['benutzername']) . "' 
        AND `Closed` = 0";
    $ergebnis = mysqli_query($conid, $sql);
    if (mysqli_num_rows($ergebnis) == 1) {
        $benutzerdaten = mysqli_fetch_array($ergebnis);
        // Resourcen freigeben
        mysqli_free_result($ergebnis);
        // Daten aus der DB mit den Benutzerdaten vergleichen
        if ($benutzerdaten['IP'] != $_SERVER['REMOTE_ADDR'])
            return false;
        if ($benutzerdaten['Info'] != $_SERVER['HTTP_USER_AGENT'])
            return false;
        if ($benutzerdaten['registration'] != $_SESSION['anmeldung'])
            return false;
        if (($benutzerdaten['Last_Login'] + 600) <= $_SERVER['REQUEST_TIME'])
            return false;
    }
    else {
        return false;
    }
    // Wenn die Benutzerdaten okay sind
    // Letzte Aktivität aktualisieren
    $sql = "UPDATE `User` SET `Last_Login` = NOW() WHERE LOWER(`Name`) = '" . mysqli_real_escape_string($conid, $_SESSION['benutzername']) . "' LIMIT 1";
    mysqli_query($conid, $sql);
    // Status zurückgeben
    return true;
}

/* * ************************ */
/* * ** Benutzer abmelden *** */
/* * ************************ */

function resetUser() {
    session_destroy();
    header('location: login.php');
    exit;
}

function validateLogin($pass, $hashed_pass, $salt) {
    if (function_exists('hash') && in_array($hash_method, hash_algos())) {
        return ($hashed_pass === crypt($pass, $hashed_pass));
    }
    return ($hashed_pass === _create_hash($pass, $salt));
}

function pseudo_rand($length) {
    if (function_exists('openssl_random_pseudo_bytes')) {
        $is_strong = false;
        $rand = openssl_random_pseudo_bytes($length, $is_strong);
        if ($is_strong === true)
            return $rand;
    }
    $rand = '';
    $sha = '';
    for ($i = 0; $i < $length; $i++) {
        $sha = hash('sha256', $sha . mt_rand());
        $chr = mt_rand(0, 62);
        $rand .= chr(hexdec($sha[$chr] . $sha[$chr + 1]));
    }
    return $rand;
}

/**
 * Fall-back SHA512 hashing algorithm with stretching.
 */
function _create_hash($password, $salt) {
    $hash = '';
    for ($i = 0; $i < 20000; $i++) {
        $hash = hash('sha512', $hash . $salt . $password);
    }
    return $hash;
}

/**
 * Creates a very secure hash. Uses blowfish by default with a fallback on SHA512.
 */
function create_hash($string, &$salt = '', $stretch_cost = 10) {
    $salt = pseudo_rand(128);
    $salt = substr(str_replace('+', '.', base64_encode($salt)), 0, 22);
    if (function_exists('hash') && in_array($hash_method, hash_algos())) {
        return crypt($string, '$2a$' . $stretch_cost . '$' . $salt);
    }
    return _create_hash($string, $salt);
}

function cleanInputRegister() {
    // Maskierende Slashes aus POST Array entfernen
    if (get_magic_quotes_gpc()) {
        $eingabe['benutzername'] = stripslashes($_POST['rbenutzer']);
        $eingabe['passwort'] = stripslashes($_POST['rpasswort']);
        $eingabe['key'] = stripslashes($_POST['rkey']);
    } else {
        $eingabe['benutzername'] = $_POST['rbenutzer'];
        $eingabe['passwort'] = $_POST['rpasswort'];
        $eingabe['key'] = $_POST['rkey'];
    }
    // Trimmen
    $eingabe['benutzername'] = trim($eingabe['benutzername']);
    $eingabe['passwort'] = trim($eingabe['passwort']);
    $eingabe['key'] = trim($eingabe['key']);
    // In Kleinschrift umwandeln
    $eingabe['checkname'] = strtolower($eingabe['benutzername']);
    // Eingabe zurückgeben
    return $eingabe;
}
?>