<?php
header('Content-Type: text/xml');

require_once '../conf/DbConnector.php';

$db = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME)
        or die('<p><font color=red>Fehler bei der Datenbankverbindung: ' .
                mysqli_connect_errno() . ': ' . mysqli_connect_error() . '</p>');

echo '<?xml version="1.0" encoding="ISO-8859-1"?>  
<rss version="2.0">  
<channel>  
<title>M-Chan LN</title>  
<description>Baka-Tsuki ePUB Generator</description>  
<link>ln.m-chan.org</link>';


$sql = "SELECT Book_ID, Ser_ID, Ser_Name, Book_Vol, Book_Name, DATE_FORMAT(Book.Created,'%a, %e %b %Y %T') as formatted_date
FROM Book, Series, Meta 
WHERE Series.Ser_ID = Book.Series_Ser_ID
AND Meta.Meta_ID = Book.Meta_Meta_ID
ORDER BY Meta.Meta_ID DESC
LIMIT 15";

$result = mysqli_query($db, $sql);

while ($zeile = mysqli_fetch_array($result)) {

    echo '  
       <item>  
          <title>'. $zeile[Ser_Name] . " " . $zeile[Book_Vol] .'</title>  
          <description><![CDATA[  
          '. $zeile[Book_Name] .'  
          ]]></description>  
          <link>ln.m-chan.org</link>
    <pubDate>'.$zeile[formatted_date].' GMT</pubDate>
    </item>';  
}  
echo '</channel>
    </rss>';  

mysqli_close($db);
?>  