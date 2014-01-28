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

if (!empty($_POST['series'])){

if ($_POST['volume'] == 0) {
    $_POST['volume'] = null;
} elseif ($_POST['volume'] == "0") {
    $_POST['volume'] = null;
}
    
require_once '../conf/DbConnectorW.php';
include_once '../conf/lib.uuid.php';
    
$db=mysqli_connect(DB_HOST,DB_USER,DB_PASSWORD,DB_NAME)
		or die('<p><font color=red>Fehler bei der Datenbankverbindung: '.
		mysqli_connect_errno().': '.mysqli_connect_error().'</p>');

$sql = "SELECT Author_Auth_ID FROM Series WHERE Ser_ID = $_POST[series] ";
$result = mysqli_query($db, $sql);
$zeile=mysqli_fetch_array($result);
$A_ID = $zeile['Author_Auth_ID'];

$sql_meta = "INSERT INTO Meta (Title, Translator, ISBN, UUID, UUIDI) VALUES ('" . urlencode($_POST[title]) . "', '" . $_POST[translators] . "', '" . $_POST[isbn] . "', '" . UUID::mint(4) . "', '" . UUID::mint(4) . "')";
mysqli_query($db, $sql_meta) or die();

$sql_metaID = "SELECT MAX(`Meta_ID`) AS Meta_ID FROM Meta";
$result_metaID = mysqli_query($db, $sql_metaID) or die();
$zeile_metaID=mysqli_fetch_array($result_metaID);
$M_ID = $zeile_metaID['Meta_ID'];


if (empty($_POST['name']) & empty($_POST['volume'])){
$sql = "INSERT INTO Book (Series_Ser_ID, Author_Auth_ID, Meta_Meta_ID) VALUES ($_POST[series],$A_ID,$M_ID)";
}

if (!empty($_POST['name']) & !empty($_POST['volume'])){
$sql = "INSERT INTO Book (Book_Name, Book_Vol, Series_Ser_ID, Author_Auth_ID, Meta_Meta_ID) VALUES ('$_POST[name]',$_POST[volume],$_POST[series],$A_ID,$M_ID)";
}

if (empty($_POST['name']) & !empty($_POST['volume'])){
$sql = "INSERT INTO Book (Book_Vol, Series_Ser_ID, Author_Auth_ID, Meta_Meta_ID) VALUES ($_POST[volume],$_POST[series],$A_ID,$M_ID)";
}

if (!empty($_POST['name']) & empty($_POST['volume'])){
$sql = "INSERT INTO Book (Book_Name, Series_Ser_ID, Author_Auth_ID, Meta_Meta_ID) VALUES ('$_POST[name]',$_POST[series],$A_ID,$M_ID)";
}

mysqli_query($db, $sql);


$sql = "SELECT MAX(`Book_ID`) AS Book_ID FROM Book";
$result = mysqli_query($db, $sql);
$zeile=mysqli_fetch_array($result);
$B_ID = $zeile['Book_ID'];

$sql = "SELECT Book.Series_Ser_ID AS Ser_ID FROM Book WHERE Book.Book_ID = $zeile[Book_ID] ";
$result = mysqli_query($db, $sql);
$zeile=mysqli_fetch_array($result);
$S_ID = $zeile['Ser_ID'];

mysqli_close($db);


if (!empty($_FILES["file"]["name"])) {
$allowedExts = array("jpg", "jpeg", "png");
$extension = end(explode(".", $_FILES["file"]["name"]));
if ((($_FILES["file"]["type"] == "image/jpeg")
|| ($_FILES["file"]["type"] == "image/pjpeg")
|| ($_FILES["file"]["type"] == "image/png"))
&& in_array($extension, $allowedExts))
  {
  if ($_FILES["file"]["error"] > 0)
    {
    echo "Return Code: " . $_FILES["file"]["error"] . "<br />";
    }
  else
    {
      
    if (file_exists("../images/books/" . $S_ID . "/" . $B_ID . "/cover.jpg"))
      {
      echo "Cover already exists. ";
      }
    elseif ("png" === $extension){
          
          if (!is_dir('../images/books/' . $S_ID)) {
            mkdir('../images/books/' . $S_ID);
            }
            
          if (!is_dir('../images/books/' . $S_ID . '/' . $B_ID)) {
            mkdir('../images/books/' . $S_ID . '/' . $B_ID);
            }
          
          $image = imagecreatefrompng($_FILES["file"]["tmp_name"]);
          $bg = imagecreatetruecolor(imagesx($image), imagesy($image));
          imagefill($bg, 0, 0, imagecolorallocate($bg, 255, 255, 255));
          imagealphablending($bg, TRUE);
          imagecopy($bg, $image, 0, 0, 0, 0, imagesx($image), imagesy($image));
          imagedestroy($image);
          imagejpeg($bg, "../images/books/" . $S_ID . "/" . $B_ID . "/cover.jpg", 100);// the 50 is to set the quality, 0 = worst-smaller file, 100 = better-bigger file 
          ImageDestroy($bg);
     
          }
      else
      {
          
          if (!is_dir('../images/books/' . $S_ID)) {
            mkdir('../images/books/' . $S_ID);
            }
            
          if (!is_dir('../images/books/' . $S_ID . '/' . $B_ID)) {
            mkdir('../images/books/' . $S_ID . '/' . $B_ID);
            }
          
      move_uploaded_file($_FILES["file"]["tmp_name"],
      "../images/books/" . $S_ID . "/" . $B_ID . "/cover.jpg");
      echo "Stored in: " . "../images/books/" . $S_ID . "/" . $B_ID . "/cover.jpg";
      }
    }
  }
else
  {
  echo "Invalid file";
  }
}


?><div align="center"><p><font color=green>The book was added to the DB.</p><p><input type="button" value="Close" onclick="window.parent.TINY.box.hide()" ></p></div><?php

} else {
    ?><div align="center"><p><font color=red>There was an error, please contact the administrator.</p><p><input type="button" value="Close" onclick="window.parent.TINY.box.hide()" ></p></div><?php
}
?>
