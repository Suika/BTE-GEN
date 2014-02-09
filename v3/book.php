<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <title>Baka-Tsuki ePUB Generator || For the moment...</title>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta name="description" content="">
        <meta name="author" content="Simon">

        <link href="../css/bootstrap.css" rel="stylesheet">
        <link href="../css/docs.css" rel="stylesheet">
        <link href="../css/bte-gen.css" rel="stylesheet">
        <link href="../css/bootstrap-responsive.css" rel="stylesheet">
        <link href="../css/bootstrap-rowlink.min.css" rel="stylesheet">

        <link rel="alternate" type="application/rss+xml" title="M-Chan BTE-GEN" href="/feed/" />

        <!-- HTML5 shim, for IE6-8 support of HTML5 elements -->
        <!--[if lt IE 9]>
        <script src="http://html5shim.googlecode.com/svn/trunk/html5.js"></script>
        <![endif]-->

    </head>
    <body>
        <div class="navbar navbar-inverse navbar-fixed-top">
            <div class="navbar-inner">
                <div class="container-fluid">
                    <a class="btn btn-navbar" data-toggle="collapse" data-target=".nav-collapse">
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                    </a>
                    <a class="brand" href="#">BTE-GEN</a>
                    <div class="nav-collapse collapse">
                        <ul class="nav">
                            <li class="active">
                                <a href="index.php"><i class="icon-home icon-white"></i> Home</a>
                            </li>
                            <li>
                                <a href="updates.php"><i class="icon-eye-open icon-white"></i> Updates</a>
                            </li>
                            <li>
                                <a href="feed.php"><i class="icon-exclamation-sign icon-white"></i> Feed</a>
                            </li>
                            <li>
                                <a href="#login" role="button" data-toggle="modal">Login</a>
                            </li>
                        </ul>
                    </div><!--/.nav-collapse -->
                </div>
            </div>
        </div>
        <div class="container">
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
WHERE Book.Book_ID = " . mysqli_real_escape_string($db, $_GET['BID']) . " 
AND Book.Series_Ser_ID = Series.Ser_ID
AND Book.Author_Auth_ID = Author.Auth_ID
AND Book.Meta_Meta_ID = Meta.Meta_ID
GROUP BY Book.Book_ID";

            $Books = "SELECT DL_Link, DL_Com, Extentions.Ext_Name, Extentions.Ext_ID, Creator.Creator_Name 
FROM LNDL, Extentions, Creator
WHERE LNDL.Book_Book_ID = " . mysqli_real_escape_string($db, $_GET['BID']) . "
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
            <?php if ($cover_image = searchFolder("../images/books/" . $zeile['Ser_ID'] . "/" . $zeile['Book_ID'] . "/*", '/^[Cc]over.jp[e]?g/')) { ?>
                <img src="../images/books/<?php echo $zeile['Ser_ID']; ?>/<?php echo $zeile['Book_ID']; ?>/<?php echo $cover_image; ?>" class="span4 pull-right">
            <?php } ?>
            <h1>Information</h1>
            <?php if (isset($zeile['Ser_Name'])) { ?>

                <p><strong>Series:</strong> <?php echo $zeile['Ser_Name']; ?></p>

            <?php } ?>

            <?php if (!empty($zeile['Book_Name'])) { ?>

                <p><strong>Title:</strong> <?php echo $zeile['Book_Name']; ?></p>

            <?php } ?>

            <?php if (!empty($zeile['Book_Vol'])) { ?>

                <p><strong>Volume:</strong> <?php echo $zeile['Book_Vol']; ?></p>

            <?php } ?>

            <?php if (!empty($zeile['Auth_Name'])) { ?>

                <p><strong>Author:</strong> <?php echo $zeile['Auth_Name']; ?></p>

            <?php } ?>

            <?php if (!empty($zeile['ISBN'])) { ?>

                <p><strong>ISBN:</strong> <?php echo $zeile['ISBN']; ?></p>

            <?php } ?>

            <?php if (!empty($zeile['Translator'])) { ?>

                <p><strong>Translator/s:</strong> <?php echo $zeile['Translator']; ?></p>

            <?php } ?>

            <?php if (!empty($zeile['Title'])) { ?>

                <p><strong>Source:</strong> <a href="http://www.baka-tsuki.org/project/index.php?title=<?php echo $zeile['Title']; ?>">HERE</a></p>

            <?php } ?>
            <?php
//$fp = fsockopen("test.baka-tsuki.org", 80, $errno, $errstr, 3);
//if (!$fp) {
//    echo "<h3>Baka-Tsuki is unreachable. Try to reload or wait until Baka-Tsuki is online</h3>";
//} else {
            if (isset($zeile['Title']) & $zeile['Generate'] == 1) {
                ?>
                <form method="post" action="../getEpub.php" id="form">
                    <fieldset>
                        <legend>Generate ePUB</legend>
                        <input type="hidden" id="series" name="series" value="<?php echo $zeile['Ser_ID'] ?>" />
                        <input type="hidden" id="books" name="books" value="<?php echo $zeile['Title'] ?>" />
                        <?php if (strstr($_SERVER['HTTP_USER_AGENT'], 'Firefox')){
                        echo '<label class="radio">
                            <input type="radio" name="img" value="ni" id="img" onchange="$(\'#size\').hide();" >
                            Without Images
                        </label>
                        <label class="radio">
                            <input type="radio" name="img" value="wi" id="img" onchange="$(\'#size\').show();" checked>
                            With Images
                        </label>';
                        } else {
                        echo '<div class="btn-group" data-toggle="buttons-radio">
                            <button type="button" class="btn" data-toggle="button">
                                Without Images
                                <input type="radio" name="img" value="ni" id="img" onchange="$(\'#size\').hide();" >
                            </button>
                            <button type="button" class="btn active" data-toggle="button">
                                With Images
                                <input type="radio" name="img" value="wi" id="img" onchange="$(\'#size\').show();" checked >
                            </button>
                        </div>';
                        }
                        ?>
                        <input type="hidden" name="size" value="original" />
                        <button class="btn" type="submit" name="submit" onClick="_gaq.push(['_trackEvent', 'Download','ePUB', '<?php echo $zeile['Book_ID']; ?>']);">Download</button>
                        <br>
                        <br>
                        <select id="size" name="size">
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
                    </fieldset>
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

        <!-- Login Part -->

        <div id="login" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                <h3 id="myModalLabel">Login</h3>
            </div>
            <div class="modal-body">
                <form class="form-horizontal" action="/admin/login.php" method="post">
                    <div class="control-group">
                        <label class="control-label" for="inputEmail">User</label>
                        <div class="controls">
                            <input type="text" id="inputUser" placeholder="User" name="benutzer" required="required">
                        </div>
                    </div>
                    <div class="control-group">
                        <label class="control-label" for="inputPassword">Password</label>
                        <div class="controls">
                            <input type="password" id="inputPassword" placeholder="Password" name="passwort" required="required">
                        </div>
                    </div>
                    <div class="control-group">
                        <div class="controls">
                            <button type="submit" class="btn" name="login" >Login</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <script src="http://code.jquery.com/jquery-latest.js" ></script>
        <script src="../js/bootstrap.js"></script>
        <script src="../js/bootstrap-rowlink.min.js"></script>
        <script src="../js/bte-gen.js"></script>
    </body>
</html>
