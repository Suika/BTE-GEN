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
	<script src="http://code.jquery.com/jquery-latest.js"></script>
        <script>function get() { $.post('../sql/getBooks.php', {series: $('#series').val()}, function (output) { $('#books').html(output).show(); });}</script>
        <script>$(document).ready(function() {
                    get();
                });</script>
  </head>
  <body>
<?php
require_once '../conf/DbConnector.php';

$db=mysqli_connect(DB_HOST,DB_USER,DB_PASSWORD,DB_NAME)
		or die('<p><font color=red>Fehler bei der Datenbankverbindung: '.
		mysqli_connect_errno().': '.mysqli_connect_error().'</p>');	
?>
      
      <form method="post" action="../sql/addLNDL.php">
          <div>
              <table>
              <tr>
                  <td><label for="dlln">DL Link: </label></td>
                  <td><input type="text" name="dlln" id="dlln" required="required"></td>
              </tr>
              <tr>
                  <td><label for="comment">Comment: </label></td>
                  <td><input type="text" name="comment" id="comment"></td>
              </tr>
              <tr>
                  <td><label for="type">Type: </label></td>
              <td>
                  <select id="type" name="type">
                  <?php
                  
                  $sql="SELECT *
                        FROM Extentions";
                  
                  $result = mysqli_query($db, $sql); while ($zeile=mysqli_fetch_array($result)){ ?>
                  <option value="<?php echo $zeile['Ext_ID']; ?>"><?php echo $zeile['Ext_Name']; ?></option>
                  <?php } ?>
                  </select>
              </td>
              </tr>
              <tr>
                  <td><label for="lang">Language: </label></td>
              <td>
                  <select id="lang" name="lang">
                  <?php
                  
                  $sql="SELECT * FROM Lang";
                  
                  $result = mysqli_query($db, $sql); while ($zeile=mysqli_fetch_array($result)){ ?>
                  <option value="<?php echo $zeile['Lang_ID']; ?>"><?php echo $zeile['Lang']; ?></option>
                  <?php } ?>
                  </select>
              </td>
              </tr>
              <tr>
                  <td><label for="creator">Creator: </label></td>
              <td>
                  <select id="creator" name="creator">
                  <?php
                  
                  $sql="SELECT * FROM Creator ORDER BY Creator.Creator_Name";
                  
                  $result = mysqli_query($db, $sql); while ($zeile=mysqli_fetch_array($result)){ ?>
                  <option value="<?php echo $zeile['Creator_ID']; ?>"><?php echo $zeile['Creator_Name']; ?></option>
                  <?php } ?>
                  </select>
              </td>
              </tr>
              <tr>
                  <td><label for="series">Series: </label></td>
              <td>
                  <select id="series" name="series" onChange="get();">
                  <?php
                  
                  $sql="SELECT Series.Ser_ID, Series.Ser_Name
                        FROM Series, Book
                        WHERE Series.Ser_ID = Book.Series_Ser_ID 
                        GROUP BY Series.Ser_ID
                        ORDER BY Series.Ser_Name";
                  
                  $result = mysqli_query($db, $sql); while ($zeile=mysqli_fetch_array($result)){ ?>
                  <option value="<?php echo $zeile['Ser_ID']; ?>"><?php echo $zeile['Ser_Name']; ?></option>
                  <?php } ?>
                  </select>
              </td>
              </tr>
              <tr>
                  <td><label for="books">Book: </label></td>
                  <td><select id="books" name="books"></select></td>
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