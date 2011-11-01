<?php
	session_start();
	$_SESSION['uname']="";
	session_destroy();
	header("Location:index.php");
?>