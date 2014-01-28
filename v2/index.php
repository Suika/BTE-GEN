<?php
if (isset($_GET['rss'])) {
    header('Content-Type: text/xml');

    echo '<?xml version="1.0" encoding="ISO-8859-1"?>  
<rss version="2.0">  
<channel>  
<title>M-Chan LN</title>  
<description>Baka-Tsuki ePUB Generator</description>  
<link>ln.m-chan.org/</link>
<item>  
<title>End for v2. Now go over to v3!</title>  
<description><![CDATA[   The new version of this site can be found under ln.m-chan.org/v3/   and the feed you\'ll find under ln.m-chan.org/feed
]]></description>  
<pubDate>Fri, 22 Feb 2013 18:06:00 GMT</pubDate>
</item>
</channel>
</rss>';
} else {
    ?>
    <html>
        <head>	
            <title>Baka-Tsuki ePUB Generator || For the moment...</title>
            <link rel="stylesheet" href="css/stylesheet.css" type="text/css" />
            <script src="http://code.jquery.com/jquery-1.8.0.min.js"></script>
            <link href="http://ajax.googleapis.com/ajax/libs/jqueryui/1.8/themes/base/jquery-ui.css" rel="stylesheet" type="text/css"/>
            <script src="http://code.jquery.com/ui/1.10.0/jquery-ui.min.js"></script>
            <script src="../js/jquery.tools.min.js"></script>
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
            <div id="b2i"></div> <div id="b2s"></div> <div id="updates"></div>
            <div id="kopf">
                <div id="kopf1">
                    <div id="login">
                        <center><a href="#login-box" class="login-window">Login / Sign In</a></center>
                    </div>
                    <div id="logo">
                    </div>
                </div>
                <div id="pfad">
                    <input type="text" id="quickfind" class="ui-autocomplete-input" autocomplete="off" role="textbox" aria-autocomplete="list" aria-haspopup="true" style="width: 100%;" autofocus="autofocus" placeholder="Search" >
                </div>
            </div>
            <div id="login-box" class="login-popup">
                <a href="#" class="close"><img src="../images/close_pop.png" class="btn_close" title="Close Window" alt="Close" /></a>
                <form method="post" class="signin" action="/admin/login.php">
                    <fieldset class="textbox">
                        <label class="benutzer">
                            <span>Username</span>
                            <input id="benutzer" name="benutzer" value="" type="text" autocomplete="on" placeholder="Username">
                        </label>
                        <label class="passwort">
                            <span>Password</span>
                            <input id="passwort" name="passwort" value="" type="password" placeholder="Password">
                        </label>
                        <button class="submit button" name="login" type="submit">Sign in</button>
                    </fieldset>
                </form>
            </div>
            <div id="series"></div>
            <div id="volumes"></div>
            <div id="book"></div>
            <div id="comments"></div>
        </body>
        <script src="../js/functions.js"></script>
        <?php if (isset($_GET['BID']) & isset($_GET['SID']) & is_numeric($_GET['BID']) & is_numeric($_GET['SID'])) { ?>
            <script>
                searchBook(<?php echo $_GET['BID'] . ',' . $_GET['SID']; ?>);
                $('#b2i:hidden').show('drop', {
                    direction: 'left'
                }, 1000);
                $('#b2s:hidden').show('drop', {
                    direction: 'left'
                }, 1000);
            </script>
        <?php } ?>
    </html>
<?php } ?>