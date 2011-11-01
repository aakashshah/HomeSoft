<html>
<body>
<?php
echo(strlen($_POST['txt'])."\n");
//echo(str_replace("\n"," ",str_replace("\n\r"," ",str_replace("\r\n"," ",escapeshellcmd($_POST['txt'])))));
echo(str_replace("\n"," ",str_replace("\r\n"," ",$_POST['txt'])));
?>
<form action="tmp.php" method="post">
<textarea name="txt" rows="10" cols="10"></textarea>
<input name="Submit" type="submit" />
</form>
</body>
</html>
