<?php
require_once '../conf/DbConnector.php';

$db=mysqli_connect(DB_HOST,DB_USER,DB_PASSWORD,DB_NAME)
		or die('<p><font color=red>Fehler bei der Datenbankverbindung: '.
		mysqli_connect_errno().': '.mysqli_connect_error().'</p>');	

$sql="SELECT Series.Ser_ID, Series.Ser_Name, Author.Auth_ID, Author.Auth_Name 
FROM Series, Author, Book, Meta, LNDL
WHERE Series.Author_Auth_ID = Author.Auth_ID 
AND Series.Ser_ID = Book.Series_Ser_ID 
AND ((Book.Meta_Meta_ID = Meta.Meta_ID AND Meta.Generate = 1) OR (Book.Book_ID = LNDL.Book_Book_ID AND LNDL.Visible = 1))
GROUP BY Series.Ser_ID
ORDER BY Series.Ser_Name";

$result = mysqli_query($db, $sql);

    
while ($zeile=mysqli_fetch_array($result)){


?>
        <div id="content">
            <div class="ENAME" onClick="get(<?php echo $zeile['Ser_ID'] ?>);"><div class="SNAME"><?php echo $zeile['Ser_Name']; ?></div>  <div class="ANAME"><?php echo $zeile['Auth_Name']; ?>	</div>	</div>
        </div>
<?php

}

mysqli_close($db);
?>