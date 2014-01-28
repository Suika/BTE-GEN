<?php
require_once '../conf/DbConnector.php';

$db=mysqli_connect(DB_HOST,DB_USER,DB_PASSWORD,DB_NAME)
		or die('<p><font color=red>Fehler bei der Datenbankverbindung: '.
		mysqli_connect_errno().': '.mysqli_connect_error().'</p>');	

$sql="SELECT Book.Series_Ser_ID, Book.Book_ID, Book.Book_Vol, Book.Book_Name, Series.Ser_Name 
    FROM Book, Series, Meta, LNDL
    WHERE (Meta.Meta_ID = Book.Meta_Meta_ID OR (Book.Book_ID = LNDL.Book_Book_ID AND LNDL.Visible = 1))
    AND Book.Series_Ser_ID = " . $_POST['series'] . "
    AND Series.Ser_ID = " . $_POST['series'] . "
    GROUP BY Book.Book_ID
    ORDER BY Book.Book_Vol";

$result = mysqli_query($db, $sql);

    
while ($zeile=mysqli_fetch_array($result)){


?>
        
        <div id="content">
            <div class="ENAME" onClick="getBook(<?php echo $zeile['Book_ID']; ?>);"><?php if (isset($zeile['Book_Vol'])) { ?><div class="SNAME">Volume <?php echo $zeile['Book_Vol']; ?></div><?php } ?><?php if (isset($zeile['Book_Name'])) { ?><div class="ANAME"><?php echo $zeile['Book_Name']; ?></div><?php } ?><?php if (empty ($zeile['Book_Name']) & empty( $zeile['Book_Vol'])) { ?><div align="center"><?php echo $zeile['Ser_Name']; ?></div><?php } ?></div>
        </div>
        
<?php

}

mysqli_close($db);

?>