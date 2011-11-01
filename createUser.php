<?php
	session_start(); //Start a session to store the username
	if(!isset($_SESSION['uname']) || $_SESSION['uname']=="") { //Check for session
		header("Location:index.php");
	}

	if (isset($_POST['create'])) { //If create button is clicked, check if the fields are not empty
		if ( trim($_POST['usrname']) != "" && trim($_POST['passwd']) != "") {
			require("login.php");
			mysql_select_db("home",$con);

			//Check if the username already exists, if not create the user
			$rcSet = mysql_query("SELECT * FROM userpass WHERE username='$_POST[usrname]'", $con);
			if (!mysql_fetch_array($rcSet)) {
				$pwd = md5($_POST['passwd']);
				$query = "INSERT INTO userpass (username, password, adjustments) VALUES ('$_POST[usrname]', '$pwd', 0)";
				mysql_query($query, $con);
				/*$query = "CREATE TABLE ".$_POST['usrname']." (expno SMALLINT AUTO_INCREMENT PRIMARY KEY, name VARCHAR(25) NOT NULL, foreignexp SMALLINT NOT NULL, amount MEDIUMINT NOT NULL, reason VARCHAR(100), dateadded DATE NOT NULL, clear TINYINT NOT NULL)";
				 mysql_query($query, $con);*/
				echo ("<table width='100%' cellpadding='5'><tr><td bgcolor='#DDDDDD'><font color='darkgreen'>User Created. Kindly Login</font></td></tr></table>");
			}
			else {
				echo ("<table width='100%' cellpadding='5'><tr><td class='status'><font color='red'>Username already exists. Please choose a different username</font></td></tr></table>");
			}

			mysql_close($con);
		}
		else {
			echo ("<table width='100%' cellpadding='5'><tr><td class='status'><font color='red'>Username/Password cannot be empty. Please Try Again</font></td></tr></table>");
		}
	}
	else {
		echo ("<table width='100%' cellpadding='5'><tr><td class='status'><font color='darkgreen'>Ready</font></td></tr></table>");
	}
?>
<html>
	<head>
		<title>Welcome to HomeSoft v1.0</title>
		<link rel="stylesheet" type="text/css" href="design.css" />
		<link rel="shortcut icon" href="favicon.ico">
	</head>
	<body bgcolor="#EEEEEE" onLoad="login.usrname.focus();">
		<br><br>
		<table width="100%" cellpadding="5"><tr><td align="left"><h2><font color="#666600">Create User</font></h2></td></tr></table>
		<form name="login" action="createUser.php" method="post">
			<table border="0" cellpadding="3" align="center">
				<tr>
					<td><b>Username:</b></td><td><input type="text" name="usrname" maxlength="25" tabindex="1" /></td>
				</tr>
				<tr>
					<td><b>Password:</b></td><td><input type="password" name="passwd" tabindex="2"/></td>
				</tr>
				<tr>
					<td align="center"><input type="submit" name="create" value="Create User" /></td><td align="center"><input type="submit" name="cancel" value="Close Window" onClick="javascript:window.close();"/></td>
				</tr>
			</table>
		</form>
	</body>
</html>