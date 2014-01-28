<?php
require_once '../conf/DbConnector.php';


$epub = array();
$mobi = array();
$pdf = array();


$db = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME)
        or die('<p><font color=red>Fehler bei der Datenbankverbindung: ' .
                mysqli_connect_errno() . ': ' . mysqli_connect_error() . '</p>');

$sql = "SELECT Book.Book_Name, Book.Book_ID, Book.Book_Vol, Book.Book_Cover, Series.Ser_ID,Series.Ser_Name, Author.Auth_Name, Meta.Title, Meta.ISBN, Meta.Translator, Meta.Generate
FROM Author, Book, Series, Meta
WHERE Book.Book_ID = " . $_POST['book'] . " 
AND Book.Series_Ser_ID = Series.Ser_ID
AND Book.Author_Auth_ID = Author.Auth_ID
AND Book.Meta_Meta_ID = Meta.Meta_ID
GROUP BY Book.Book_ID";

$Books = "SELECT DL_Link, DL_Com, Extentions.Ext_Name, Extentions.Ext_ID, Creator.Creator_Name 
FROM LNDL, Extentions, Creator
WHERE LNDL.Book_Book_ID = " . $_POST['book'] . "
AND Extentions.Ext_ID = LNDL.Extentions_Ext_ID
AND Creator.Creator_ID = LNDL.Creator_Creator_ID
AND LNDL.Visible = 1
ORDER BY LNDL.Extentions_Ext_ID";



$result = mysqli_query($db, $sql);
$BooksResult = mysqli_query($db, $Books);


while ($zeile2 = mysqli_fetch_array($BooksResult)) {

    if ($zeile2['Ext_ID'] == 1) {
        $file = "<a href=" . urldecode($zeile2['DL_Link']) . ">" . $zeile2['Ext_Name'] . "</a> by " . $zeile2['Creator_Name'];
        if (!empty($zeile2['DL_Com'])) {
            $file .= ': ' . urldecode($zeile2['DL_Com']);
        }
        $epub[] = $file;
    }
    if ($zeile2['Ext_ID'] == 2) {
        $file = "<a href=" . urldecode($zeile2['DL_Link']) . ">" . $zeile2['Ext_Name'] . "</a> by " . $zeile2['Creator_Name'];
        if (!empty($zeile2['DL_Com'])) {
            $file .= ': ' . urldecode($zeile2['DL_Com']);
        }
        $mobi[] = $file;
    }
    if ($zeile2['Ext_ID'] == 3) {
        $file = "<a href=" . urldecode($zeile2['DL_Link']) . ">" . $zeile2['Ext_Name'] . "</a> by " . $zeile2['Creator_Name'];
        if (!empty($zeile2['DL_Com'])) {
            $file .= ': ' . urldecode($zeile2['DL_Com']);
        }
        $pdf[] = $file;
    }
}

$zeile = mysqli_fetch_array($result);
?>
<div class="cover">
    <svg xmlns="http://www.w3.org/2000/svg" height="100%" 
         preserveAspectRatio="xMidYMid meet" version="1.1" viewBox="0 0 200 400" 
         width="100%" xmlns:xlink="http://www.w3.org/1999/xlink">
        <?php if ($cover_image = searchFolder("../images/books/" . $zeile['Ser_ID'] . "/" . $zeile['Book_ID'] . "/*", '/^[Cc]over.jp[e]?g/')) { ?>
            <image height="400" width="200" xlink:href="../images/books/<?php echo $zeile['Ser_ID']; ?>/<?php echo $zeile['Book_ID']; ?>/<?php echo $cover_image; ?>"></image>
<?php } else { ?>
            <image height="400" width="200" xlink:href="../images/err/404.png"></image>
    <?php } ?>
    </svg>
</div>

<div class="BTEXT">
    <?php if (isset($zeile['Ser_Name'])) { ?>

        <p><b>Series:</b> <?php echo $zeile['Ser_Name']; ?></p>

<?php } ?>

    <?php if (isset($zeile['Book_Name'])) { ?>

        <p><b>Title:</b> <?php echo $zeile['Book_Name']; ?></p>

<?php } ?>

    <?php if (isset($zeile['Vook_Vol'])) { ?>

        <p><b>Volume:</b> <?php echo $zeile['Book_Vol']; ?></p>

<?php } ?>

    <?php if (isset($zeile['Auth_Name'])) { ?>

        <p><b>Author:</b> <?php echo $zeile['Auth_Name']; ?></p>

<?php } ?>

    <?php if (isset($zeile['ISBN'])) { ?>

        <p><b>ISBN:</b> <?php echo $zeile['ISBN']; ?></p>

<?php } ?>

    <?php if (isset($zeile['Translator'])) { ?>

        <p><b>Translator/s:</b> <?php echo $zeile['Translator']; ?></p>

<?php } ?>

    <?php if (isset($zeile['Title'])) { ?>

        <p><b>Source:</b> <a href="http://www.baka-tsuki.org/project/index.php?title=<?php echo $zeile['Title']; ?>">HERE</a></p>

    <?php } ?>
</div>
<div class="BTEXT">
    <?php
//$fp = fsockopen("test.baka-tsuki.org", 80, $errno, $errstr, 3);
//if (!$fp) {
//    echo "<h3>Baka-Tsuki is unreachable. Try to reload or wait until Baka-Tsuki is online</h3>";
//} else {
    if (isset($zeile['Title']) & $zeile['Generate'] == 1) {
        ?>
        <h3>Generate ePUB</h3>
        <form method="post" action="../getEpub.php" id="form">
            <div>
                <input type="hidden" id="series" name="series" value="<?php echo $zeile['Ser_ID'] ?>" />
                <input type="hidden" id="books" name="books" value="<?php echo $zeile['Title'] ?>" />
                <label for="page">Without Images</label><input type="radio" name="img" value="ni" id="img" onchange="$('#sizebox').hide();" checked>

                    <label for="page">With Images</label><input type="radio" name="img" value="wi" id="img" onchange="$('#sizebox').show();">
                        <span id="sizebox">
                            <label for="size">Device: </label><select id="size" name="size">
                                <option value="original" selected >Original</option>
                                <option value="acer">ACER</option>
                                <option value="kindlefire">Kindle Fire</option>
                                <option value="sgs2" >SGS 2</option>
                                <option value="sgs3" >SGS 3 / Nexus 4</option>
                                <option value="sb">Samsung Brightside</option>
                                <option value="t9800">Blackberry T9800</option>
                                <option value="t9810">Blackberry T9810</option>
                                <option value="htc">HTC</option>
                                <option value="lg">LG</option>
                                <option value="iphone3">iPhine 3</option>
                                <option value="iphone4s">iPhone 4S</option>
                                <option value="iphone5">iPhone 5</option>
                                <option value="ipad">iPad</option>
                                <option value="ipad2">iPad 2</option>
                                <option value="kobotouch">Kobo Touch</option>
                            </select>
                        </span>
                        <input type="submit" name="submit" value="Download" onClick="_gaq.push(['_trackEvent', 'Download','ePUB', '<?php echo $zeile['Book_ID']; ?>']);">
                            </div>
                            </form>
<?php
}
//fclose($fp);
//}
?>
                                <?php if (!empty($epub)) { ?>    
                            <h3>ePUB</h3>
                            <div>
                                <ul>
                            <?php for ($i = 0; $i < count($epub); $i++) { ?>
                                        <li><?php echo $epub[$i]; ?></li>
    <?php } ?>
                                </ul>
                            </div>
                                <?php } ?>
                                <?php if (!empty($mobi)) { ?>        
                            <h3>MOBI</h3>
                            <div>
                                <ul>
                            <?php for ($i = 0; $i < count($mobi); $i++) { ?>
                                        <li><?php echo $mobi[$i]; ?></li>
    <?php } ?>
                                </ul>
                            </div>
                                <?php } ?>
                                <?php if (!empty($pdf)) { ?>        
                            <h3>PDF</h3>
                            <div>
                                <ul>
    <?php for ($i = 0; $i < count($pdf); $i++) { ?>
                                        <li><?php echo $pdf[$i]; ?></li>
    <?php } ?>
                                </ul>
                            </div>
<?php } ?>
                        </div>
                        <script>
                            $('#sizebox').hide();
                            $("ccss").click( function () {
                                $("#comments").expose({
    
                                    color: "#789",

                                    onBeforeLoad: function() {
                                        $("#comments").slideToggle("slow");
                                    },
 
                                    onBeforeClose: function() {
                                        $("#comments").slideToggle("slow");
                                    }
                                });
                            });
                        </script>
                        <?php
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