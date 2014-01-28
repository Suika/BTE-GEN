<?php

function getDBconn() {
    require_once '../conf/DbConnector.php';

    $db = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME)
            or die('<p><font color=red>Fehler bei der Datenbankverbindung: ' .
                    mysqli_connect_errno() . ': ' . mysqli_connect_error() . '</p>');

    return $db;
}

function constructRss($rss, $db) {
    header('Content-Type: text/xml');

    echo '<?xml version="1.0" encoding="ISO-8859-1"?>  
<rss version="2.0">  
<channel>  
<title>M-Chan LN</title>  
<description>Baka-Tsuki ePUB Generator</description>  
<link>ln.m-chan.org/v2</link>';


    $sql = "SELECT Book_ID, Ser_ID, Ser_Name, Book_Vol, Book_Name, Meta.Translator, Meta.Description, Meta.ISBN, DATE_FORMAT(Book.Created,'%a, %e %b %Y %T') as formatted_date
FROM Book, Series, Meta 
WHERE Series.Ser_ID = Book.Series_Ser_ID
AND Meta.Meta_ID = Book.Meta_Meta_ID
ORDER BY Meta.Meta_ID DESC
LIMIT 15";

    $result = mysqli_query($db, $sql);

    while ($zeile = mysqli_fetch_array($result)) {
        
        $title = $zeile['Ser_Name'];
        $title .= (!empty($zeile['Book_Vol'])) ? " - Volume " . $zeile['Book_Vol'] : null;
        $title .= (!empty($zeile['Book_Name'])) ? " - " . $zeile['Book_Name'] : null;
        
        $data = NULL;
        (!empty($zeile['Translator'])) ? $data .= 'Translator: ' . $zeile['Translator'] . PHP_EOL : '' ;
        (!empty($zeile['ISBN'])) ? $data .= 'ISBN: ' . $zeile['ISBN'] . PHP_EOL : '' ;
        (!empty($zeile['Description'])) ? $data .= 'Description: ' . $zeile['Description'] . PHP_EOL : '' ;
        
        $link = "?BID=$zeile[Book_ID]&amp;SID=$zeile[Ser_ID]";

        rssItems($title, $data, $link, $zeile['formatted_date']);
    }
    echo '</channel>
    </rss>';

    mysqli_close($db);
}

function rssItems($title, $data, $link, $date) {
    echo '  
       <item>  
          <title>' . $title . '</title>  
          <description><![CDATA[  
          ' . $data . '  
          ]]></description>  
          <link>' . $link . '</link>
    <pubDate>' . $date . ' GMT</pubDate>
    </item>';
}

?>
