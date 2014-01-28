<?php
// Erzwingen das Session-Cookies benutzt werden und die SID nicht per URL transportiert wird
ini_set('session.use_only_cookies', '1');
ini_set('session.use_trans_sid', '0');

// Session starten
session_start();

// Funktionen einbinden
include( '../admin/auth.php' );

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

$sql="SELECT Name FROM User, User_has_Groups WHERE LOWER(`Name`) = '" . mysqli_real_escape_string($conid, $_SESSION['benutzername']) . "' AND User_has_Groups.User_ID = User.ID AND User.Closed = 0";

        $userresult = mysqli_query($conid, $sql);
        
        if(mysqli_num_rows($userresult) == 1){
?>
<html>
    <head>
        <title></title>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
        <link rel="stylesheet" href="../css/admin.css" />
        <script src="http://code.jquery.com/jquery-latest.js"></script>
        <script type="text/javascript" src="../js/tinybox.js"></script>
        <script>function getUEL(SID) { $.post('../sql/getUEL.php', {series: SID}, function (output) { $('#links').html(output).show(); }); }</script>
        <script>function closeUserLink(ID) { $.post('../sql/closeUserLink.php', {id: ID}, function () { getAprove(); }); }</script>
        <script>
            $(document).ready(function() { 
            });   
        </script>
    </head>
    <body>
    <center>
        <input type="button" onclick="location.href='index.php'" value="Back">
        <input type="button" onclick="TINY.box.show({iframe:'lndl.php',boxid:'frameless',width:600,height:300,fixed:false,maskid:'graymask',maskopacity:40,closejs:function(){closeJS()}})" value="Add DL Link">
        <input type="button" onclick="location.href='<?php echo $_SERVER['PHP_SELF']. "?benutzer=abmelden"; ?>'" value="Log Out">
    </center>
        <div id="series" class="series">
<?php 
$sql="SELECT Series.Ser_ID, Series.Ser_Name, User.ID AS User_ID FROM Series, User, LNDL, Book  WHERE LNDL.User_ID = User.ID AND LOWER(`Name`) = '" . mysqli_real_escape_string($conid, $_SESSION['benutzername']) . "' AND LNDL.Book_Book_ID = Book.Book_ID AND Series.Ser_ID = Book.Series_Ser_ID AND LNDL.Closed = 0 GROUP BY Series.Ser_ID";         
$result = mysqli_query($conid, $sql);

while ($zeile=mysqli_fetch_array($result)){    
echo "<div onclick='getUEL($zeile[Ser_ID]);' class=\"content\"><center>" . $zeile['Ser_Name'] . "</center></div>";
 } 
?>
        </div>
        <div id="links" class="EUL"></div>
    </body>
</html>   
<?php
        } else {resetUser();}
mysqli_close($conid);
?>