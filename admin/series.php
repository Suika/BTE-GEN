<?php

// Erzwingen das Session-Cookies benutzt werden und die SID nicht per URL transportiert wird
ini_set( 'session.use_only_cookies', '1' );
ini_set( 'session.use_trans_sid', '0' );

// Session starten
session_start();

// Funktionen einbinden
include( 'auth.php' );

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
<html>
  <head>
    <title>
    </title>
	<link rel="stylesheet" href="../css/stylesheet-admin.css" type="text/css" />
	<script src="http://code.jquery.com/jquery-latest.js"></script>
  </head>
  <body>
<?php
require_once '../conf/DbConnector.php';

$db=mysqli_connect(DB_HOST,DB_USER,DB_PASSWORD,DB_NAME)
		or die('<p><font color=red>Fehler bei der Datenbankverbindung: '.
		mysqli_connect_errno().': '.mysqli_connect_error().'</p>');	
?>

      
      <form method="post" action="../sql/addSeries.php" enctype="multipart/form-data">
          <div>
              <table>
              <tr>
                  <td><labe for="name">Series Name: </label></td>
                  <td><input type="text" id="name" name="name" required="required"></td>
              </tr>
              <tr>
                  <td>Author</td>
              <td>
                  <select name="author" id="author">
                  <?php
                  
                  $sql="SELECT Author.Auth_ID, Author.Auth_Name 
                        FROM Author
                        ORDER BY Author.Auth_Name";
                  
                  $result = mysqli_query($db, $sql); while ($zeile=mysqli_fetch_array($result)){ ?>
                  <option value="<?php echo $zeile['Auth_ID']; ?>"><?php echo $zeile['Auth_Name']; ?></option>
                  <?php } ?>
                  </select>
              </td>
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