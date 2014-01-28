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
<?php
require_once '../conf/DbConnector.php';

$db=mysqli_connect(DB_HOST,DB_USER,DB_PASSWORD,DB_NAME)
		or die('<p><font color=red>Fehler bei der Datenbankverbindung: '.
		mysqli_connect_errno().': '.mysqli_connect_error().'</p>');	

$sql="SELECT Series.Ser_ID, Series.Ser_Name FROM Series ORDER BY Series.Ser_Name";

$result = mysqli_query($db, $sql);
?>
<html>
    <head>
        <title></title>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
        <link rel="stylesheet" href="../css/style.css" />
        <script src="http://code.jquery.com/jquery-latest.js"></script>
        <script type="text/javascript" src="../js/tinybox.js"></script>
        <script>function get(ID) { $.post('../sql/getIndexBooks.php', {series: ID}, function (output) { $('#books').html(output).show(); }); }</script>
        <script>function getInfo(ID) { $.post('../sql/getIndexInfo.php', {book: ID}, function (output) { $('#info').html(output).show(); }); }</script>
    </head>
    <body>
        <center>
        <div id="aButtons">
        <input type="button" onclick="TINY.box.show({iframe:'series.php',boxid:'frameless',width:270,height:120,fixed:false,maskid:'graymask',maskopacity:40,closejs:function(){closeJS()}})" value="Add Series">
        <input type="button" onclick="TINY.box.show({iframe:'book.php',boxid:'frameless',width:500,height:280,fixed:false,maskid:'graymask',maskopacity:40,closejs:function(){closeJS()}})" value="Add Book">
        <input type="button" onclick="TINY.box.show({iframe:'author.php',boxid:'frameless',width:270,height:120,fixed:false,maskid:'graymask',maskopacity:40,closejs:function(){closeJS()}})" value="Add Author">
        <input type="button" onclick="TINY.box.show({iframe:'lndl.php',boxid:'frameless',width:600,height:300,fixed:false,maskid:'graymask',maskopacity:40,closejs:function(){closeJS()}})" value="Add DL Link">
        <input type="button" onclick="TINY.box.show({iframe:'creator.php',boxid:'frameless',width:270,height:120,fixed:false,maskid:'graymask',maskopacity:40,closejs:function(){closeJS()}})" value="Add Creator">
        <?php 
        
        $sql="SELECT Name FROM User, User_has_Groups WHERE LOWER(`Name`) = '" . mysqli_real_escape_string($db, $_SESSION['benutzername']) . "' AND User_has_Groups.Groups_ID = 1 AND User_has_Groups.User_ID = User.ID AND User.Closed = 0";

        $userresult = mysqli_query($db, $sql);
        
        if(mysqli_num_rows($userresult) == 1){
        ?>
        <input type="button" onclick="location.href='admin.php'" value="Admin">
        <?php } ?>
        <input type="button" onclick="location.href='user.php'" value="User Page">
        <input type="button" onclick="location.href='<?php echo $_SERVER['PHP_SELF']. "?benutzer=abmelden"; ?>'" value="Log Out">
        </div>
        </center>
        <div class="series">
            <?php while ($zeile=mysqli_fetch_array($result)){ ?>
            <div onclick="get(<?php echo $zeile['Ser_ID'] ?>);"><p><?php echo $zeile['Ser_Name'] ?></p></div>
            <?php } ?>
        </div>
        <div id="books" class="books"></div>
        <div id="info" class="info">
            
        </div>
    </body>
</html>   
<?php
mysqli_close($db);
?>
