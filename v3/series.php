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
        <div class="container-fluid">
            <table class="table" data-provides="rowlink">
                <thead>
                    <tr>
                        <th>Series</th>
                        <th>Volume</th>
                        <th>Name</th>
                    </tr>
                </thead>
                <tbody>

                    <?php
                    require_once '../conf/DbConnector.php';

                    $db = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME)
                            or die('<p><font color=red>Fehler bei der Datenbankverbindung: ' .
                                    mysqli_connect_errno() . ': ' . mysqli_connect_error() . '</p>');

                    $sql = "SELECT Book.Series_Ser_ID, Book.Book_ID, Book.Book_Vol, Book.Book_Name, Series.Ser_Name 
    FROM Book, Series, Meta, LNDL
    WHERE (Meta.Meta_ID = Book.Meta_Meta_ID OR (Book.Book_ID = LNDL.Book_Book_ID AND LNDL.Visible = 1))
    AND Book.Series_Ser_ID = " . mysqli_real_escape_string($db, $_GET['SID']) . "
    AND Series.Ser_ID = " . mysqli_real_escape_string($db, $_GET['SID']) . "
    GROUP BY Book.Book_ID
    ORDER BY Book.Book_Vol";

                    $result = mysqli_query($db, $sql);


                    while ($zeile = mysqli_fetch_array($result)) {
                        ?>
                        <tr>
                            <td><a href="book.php?BID=<?php echo $zeile['Book_ID']; ?>"><?php echo $zeile['Ser_Name']; ?></a></td>
                            <td><?php echo $zeile['Book_Vol']; ?></td>
                            <td><?php echo $zeile['Book_Name']; ?></td>
                        </tr>
                        <?php
                    }

                    mysqli_close($db);
                    ?>
                </tbody>
            </table>
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
