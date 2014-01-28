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
require_once '../conf/DbConnector.php';

$book = $_POST['book'];

$db=mysqli_connect(DB_HOST,DB_USER,DB_PASSWORD,DB_NAME)
		or die('<p><font color=red>Fehler bei der Datenbankverbindung: '.
		mysqli_connect_errno().': '.mysqli_connect_error().'</p>');	

$sql="SELECT Book.Book_Name, Book.Book_ID,Book.Book_Vol, Book.Book_Cover, Series.Ser_ID,Series.Ser_Name, Author.Auth_Name, Meta.Title, Meta.ISBN, Meta.Translator
FROM Author, Book, Series, Meta
WHERE Book.Book_ID = " . $book . " 
AND Book.Series_Ser_ID = Series.Ser_ID
AND Book.Author_Auth_ID = Author.Auth_ID
AND Book.Meta_Meta_ID = Meta.Meta_ID
GROUP BY Book.Book_ID";

$Books="SELECT DL_Link, Extentions.Ext_Name, Extentions.Ext_ID, Creator.Creator_Name 
FROM LNDL, Extentions, Creator
WHERE LNDL.Book_Book_ID = " . $book . "
AND Extentions.Ext_ID = LNDL.Extentions_Ext_ID
AND Creator.Creator_ID = LNDL.Creator_Creator_ID
ORDER BY LNDL.Extentions_Ext_ID";

$result = mysqli_query($db, $sql);

$zeile=mysqli_fetch_array($result);


                  
$result = mysqli_query($db, $sql);
?>

<div class="book">
        
        <div class="cover">
    <svg xmlns="http://www.w3.org/2000/svg" height="100%" 
         preserveAspectRatio="xMidYMid meet" version="1.1" viewBox="0 0 200 400" 
         width="100%" xmlns:xlink="http://www.w3.org/1999/xlink">
        <?php if ($cover_image = searchFolder("../images/books/" . $zeile['Ser_ID'] . "/" . $zeile['Book_ID'] . "/*", '/^[Cc]over.jp[e]?g/')){  ?>
      <image height="400" width="200" xlink:href="../images/books/<?php echo $zeile['Ser_ID']; ?>/<?php echo $zeile['Book_ID']; ?>/<?php echo $cover_image; ?>"></image>
        <?php } else { ?>
      <image height="400" width="200" xlink:href="../images/err/404.png"></image>
        <?php } ?>
    </svg>
  </div>
        
<table class="BTEXT">
<?php if (isset($zeile['Ser_Name'])) { ?>
        
        <tr><td><b>Series:</b></td><td><?php echo $zeile['Ser_Name']; ?></td></tr>
        
<?php } ?>
        
<?php if (isset($zeile['Book_Name'])) { ?>
        
        <tr><td><b>Titel:</b></td><td><?php echo $zeile['Book_Name']; ?></td></tr>
        
<?php } ?>
        
<?php if (isset($zeile['Vook_Vol'])) { ?>
        
        <tr><td><b>Volume:</b></td><td><?php echo $zeile['Book_Vol']; ?></td></tr>
        
<?php } ?>
        
<?php if (isset($zeile['Auth_Name'])) { ?>
        
        <tr><td><b>Author:</b></td><td><?php echo $zeile['Auth_Name']; ?></td></tr>
        
<?php } ?>
</table>
        
        
<?php 

$sql2="SELECT DL_Link, Extentions.Ext_Name, Creator.Creator_Name 
FROM LNDL, Extentions, Creator
WHERE LNDL.Book_Book_ID = " . $book . "
AND Extentions.Ext_ID = LNDL.Extentions_Ext_ID
AND Creator.Creator_ID = LNDL.Creator_Creator_ID
ORDER BY LNDL.Extentions_Ext_ID";

$result2 = mysqli_query($db, $sql2);
?>
<div class="BTEXT"> 
<?php
while ($zeile2=mysqli_fetch_array($result2)){
?>
   
    <p><a href="<?php echo urldecode($zeile2['DL_Link']); ?>"><?php echo $zeile2['Ext_Name']; ?></a> by <?php echo $zeile2['Creator_Name']; ?></p>
        
<?php

}

mysqli_close($db); 

function searchFolder($path, $regex) {
    $files = glob($path);
    if (empty($files)) {
        return FALSE;
    } else {
        foreach ($files as $file) {
            if (preg_match($regex, $exp = end(explode('/', $file)))) {
                return $exp;
            }
        }
    }
}
?>
        
</div>    
</div>