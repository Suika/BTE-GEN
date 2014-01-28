<?php

if (isset($_POST['register']) & isset($_POST['rbenutzer']) & isset($_POST['rpasswort']) & isset($_POST['rkey'])) {


    $dbhost = 'sql171.your-server.de';
    $dbuser = 'mmchan_btds_w';
    $dbpass = 'h17HeT5G';
    $dbname = 'mmchan_btds';
    // Verbindung herstellen und Verbindungskennung zurück geben
    $db = mysqli_connect($dbhost, $dbuser, $dbpass, $dbname) or die('Verbindungsfehler!');
    mysqli_real_escape_string($db, $benutzer);

    $eingabe = cleanInputRegister();

    $sql = "SELECT `Name` FROM `User` WHERE LOWER(`Name`) = '" . mysqli_real_escape_string($db, $eingabe['checkname']) . "' LIMIT 1";
    $result = mysqli_query($db, $sql);

    if (mysqli_num_rows($result) == 0) {

        $sql = "SELECT ID, OTP_Used FROM OTP WHERE OTP_Key = '" . mysqli_real_escape_string($db, $eingabe['key']) . "' LIMIT 1";
        $result = mysqli_query($db, $sql)or die('Verbindungsfehler!');
        $zeile = mysqli_fetch_array($result);

        if (mysqli_num_rows($ergebnis) == 1 & $zeile['OTP_Used'] == 0) {

            $sql = "UPDATE `OTP` SET `OTP_Used` = 1 WHERE OTP_Key = '" . mysqli_real_escape_string($db, $eingabe['key']) . "' LIMIT 1";
            mysqli_query($db, $sql) or die('Verbindungsfehler!');
            
            $pseudo = pseudo_rand(128);
            $hash = create_hash($eingabe['passwort'], $pseudo);
            
            $sql = "INSERT INTO `User`(`Name`, `Password`, `Salt`, `Created`, `OTP_ID`) 
               VALUES ('" . mysqli_real_escape_string($db, $eingabe['benutzername']) . "', '" . $hash . "', '" . $pseudo . "', NOW(), " . $zeile['ID'] . ")";
            mysqli_query($db, $sql) or die('Verbindungsfehler!');
        } else {
            $fehlermeldung = '<h3>Trying to use a wrong key ? *eyes of pity*</h3>';
        }
    } else {
        $fehlermeldung = '<h3>One of the fieds is wrong... But I won\'t say which.</h3>';
    }
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

/**
 * Generates a secure, pseudo-random password with a safe fallback.
 */
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

?>
