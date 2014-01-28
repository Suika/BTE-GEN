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

        <script type="text/javascript">

            var _gaq = _gaq || [];
            _gaq.push(['_setAccount', 'UA-35344454-1']);
            _gaq.push(['_trackPageview']);

            (function() {
                var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
                ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
                var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
            })();
        </script>
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
                            <li>
                                <a href="index.php"><i class="icon-home icon-white"></i> Home</a>
                            </li>
                            <li class="active">
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
        <div class="container-fluid">
            <?php
            require_once '../conf/DbConnector.php';

            $ser = $_POST['series'];

            $db = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME)
                    or die('<p><font color=red>Fehler bei der Datenbankverbindung: ' .
                            mysqli_connect_errno() . ': ' . mysqli_connect_error() . '</p>');

            $sql = "SELECT Book_ID, Ser_ID, Ser_Name, Book_Vol, Book_Name, Auth_Name, ISBN, Translator, Title, Generate
FROM Book, Series, Meta, Author
WHERE Series.Ser_ID = Book.Series_Ser_ID
AND Meta.Meta_ID = Book.Meta_Meta_ID
AND Book.Author_Auth_ID = Author.Auth_ID
ORDER BY Meta.Meta_ID DESC
LIMIT 10";

            $result = mysqli_query($db, $sql);

            while ($zeile = mysqli_fetch_array($result)) {
                ?>
                <div class="hero-unit">

                    <?php if ($cover_image = searchFolder("../images/books/" . $zeile['Ser_ID'] . "/" . $zeile['Book_ID'] . "/*", '/^[Cc]over.jp[e]?g/')) { ?>
                        <img src="../images/books/<?php echo $zeile['Ser_ID']; ?>/<?php echo $zeile['Book_ID']; ?>/<?php echo $cover_image; ?>" class="span2 pull-right">
                    <?php } else { ?>
                        <img src="../images/err/404.png" class="span2 pull-right">
                    <?php } ?>
                    <h1><?php echo $zeile['Ser_Name']; ?></h1>
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
                    <form method="post" action="../getEpub.php" id="form">
                        <input type="hidden" id="series" name="series" value="<?php echo $zeile['Ser_ID'] ?>" />
                        <input type="hidden" id="books" name="books" value="<?php echo $zeile['Title'] ?>" />
                        <input type="hidden" name="img" value="wi" id="img" />
                        <input type="hidden" name="size" value="original" />
                        <a class="btn btn-primary btn-large" href="book.php?BID=<?php echo $zeile['Book_ID']; ?>"><i class="icon-arrow-right icon-white"></i> Novel</a>  <?php if (!empty($zeile['Generate']) && ($zeile['Generate'] != 0)) { ?><button class="btn btn-inverse btn-large" type="submit" name="submit" onClick="_gaq.push(['_trackEvent', 'Download','ePUB', '<?php echo $zeile['Book_ID']; ?>']);"><i class="icon-download-alt icon-white"></i> Download</button><?php } ?>
                    </form>
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
