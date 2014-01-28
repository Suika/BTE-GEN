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

$sql="SELECT Name FROM User, User_has_Groups WHERE LOWER(`Name`) = '" . mysqli_real_escape_string($conid, $_SESSION['benutzername']) . "' AND User_has_Groups.Groups_ID = 1 AND User_has_Groups.User_ID = User.ID AND User.Closed = 0";

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
        <script>function getUser() { $.post('../sql/getUser.php', function (output) { $('#user').html(output).show(); }); }</script>
        <script>function getSwL(ID) { $.post('../sql/getUserSeries.php', {user: ID}, function (output) { $('#series').html(output).show(); }); }</script>
        <script>function getBwL(ID,SID) { $.post('../sql/getUserLinks.php', {user: ID, series: SID}, function (output) { $('#links').html(output).show(); }); }</script>
        <script>function getAprove() { $.post('../sql/getAprove.php', function (output) { $('#aprove').html(output).show(); }); }</script>
        <script>function closeLink(ID) { $.post('../sql/closeLink.php', {id: ID}, function () { getAprove(); }); }</script>
        <script>function aproveLink(ID) { $.post('../sql/aproveLink.php', {id: ID}, function () { getAprove(); }); }</script>
        <script>function closeUser(ID) { $.post('../sql/closeUser.php', {id: ID}, function () { getUser(); getAprove();}); }</script>
        <script>
            $(document).ready(function() { 
                getUser();
                getAprove();
            });   
        </script>
    </head>
    <body>
        <center>
        <input type="button" onclick="location.href='index.php'" value="Back">
        <input type="button" onclick="location.href='<?php echo $_SERVER['PHP_SELF']. "?benutzer=abmelden"; ?>'" value="Log Out">
    </center>
        <div id="user" class="user"></div>
        <div id="series" class="series"></div>
        <div id="links" class="links"></div>
        <div id="aprove" class="aprove"></div>
    </body>
</html>   
<?php
        } else {resetUser();}
mysqli_close($conid);
?>