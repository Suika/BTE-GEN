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
  </head>
  <body>
      <form method="post" action="../sql/addAuthor.php" enctype="multipart/form-data">
          <div>
              <table>
              <tr>
                  <td><label for="author">Author Name:</label></td>
                  <td><input type="text" name="author" id="author" required="required"></td>
              </tr>
              <tr>
                  <td></td>
                  <td><input type="submit" name="submit" value="Add"></td>
              </tr>
              </table>
          </div>
      </form>
  </body>
</html>