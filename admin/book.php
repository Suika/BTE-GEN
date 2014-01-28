<?php
// Erzwingen das Session-Cookies benutzt werden und die SID nicht per URL transportiert wird
ini_set('session.use_only_cookies', '1');
ini_set('session.use_trans_sid', '0');

// Session starten
session_start();

// Funktionen einbinden
include( 'auth.php' );

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
<html>
    <head>
        <title>
        </title>
        <script src="http://code.jquery.com/jquery-latest.js"></script>
        <script>function required() {  
            var trl = document.forms["book_form"]["translators"].value;
            var ttl = document.forms["book_form"]["title"].value;
            var isbn = document.forms["book_form"]["isbn"].value;
            //if (trl == "") {  
            //    alert("Add a translator");  
            //    return false;  
            //} else if (ttl == ""){  
            //    alert("Add a title");  
            //    return false;   
            //} else if (isbn == "" & isbn.length < 17){  
            //    alert('ISBN is written wrong or is empty !');  
            //    return false;   
            //} else {   
            //    return true;   
            //}
            if (ttl == ""){  
                alert("Add a title");  
                return false;   
            } else{   
                return true;   
            }
        }
        </script>
    </head>
    <body>
<?php
require_once '../conf/DbConnector.php';

$db = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME)
        or die('<p><font color=red>Fehler bei der Datenbankverbindung: ' .
                mysqli_connect_errno() . ': ' . mysqli_connect_error() . '</p>');
?>

        <form name="book_form"  method="post" action="../sql/addBook.php" enctype="multipart/form-data" onsubmit="return required();">
            <div>
                <table>
                    <tr>
                        <td><label for="name">Book Name: </label></td>
                        <td><input name="name" id="name" type="text"></td>
                    </tr>
                    <tr>
                        <td><label for="volume">Book Volume:</label></td>
                        <td><input type="number" name="volume" id="volume" step="any" min="0" max="30"></td>
                    </tr>
                    <tr>
                        <td><label for="title">B-T Title:</label></td>
                        <td><input type="text" name="title" id="title"></td>
                    </tr>
                    <tr>
                        <td><label for="translators">Translators:</label></td>
                        <td><input type="text" name="translators" id="translators"></td>
                    </tr>
                    <tr>
                        <td><label for="isbn">ISBN:</label></td>
                        <td><input type="text" name="isbn" id="isbn"></td>
                    </tr>
                    <tr>
                        <td><label for="series">Series: </label></td>
                        <td>
                            <select id="series" name="series">
<?php
$sql = "SELECT Series.Ser_ID, Series.Ser_Name
                        FROM Series
                        ORDER BY Series.Ser_Name";

$result = mysqli_query($db, $sql);
while ($zeile = mysqli_fetch_array($result)) {
    ?>
                                    <option value="<?php echo $zeile['Ser_ID']; ?>"><?php echo $zeile['Ser_Name']; ?></option>
                                <?php } ?>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <td><label for="file">Cover:</label></td>
                        <td><input type="file" name="file" id="file" /></td>
                    </tr>
                    <tr>
                        <td></td>
                        <td><input type="submit" name="submit" value="Add"></td>
                    </tr>
                </table>
            </div>
        </form>
    </body>
<?php mysqli_close($db); ?>
</html>