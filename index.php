<?php
	session_start(); //Start a session to store the username<html>
?>
<html>
	<head>
		<title>Welcome to HomeSoft v1.0</title>
		<link rel="stylesheet" type="text/css" href="design.css" />
		<link rel="shortcut icon" href="favicon.ico">
		<script language="javascript">
		<!--
		function cookie()
		{
			document.getElementById("width").value = screen.width;
			document.getElementById("height").value = screen.height;
			document.login.usrname.focus();
		}
		//-->
		</script>
	</head>
	<body bgcolor="#EEEEEE" onLoad="cookie();">
<?php
	if(isset($_POST['width']) && isset($_POST['height'])) {
		$_SESSION['width'] = $_POST["width"] - 100;
		$_SESSION['height'] = $_POST["height"] - 375;
	} else {
		$_SESSION['width'] = 900;
		$_SESSION['height'] = 500;
	}

$ip = "(".$_SERVER['HTTP_CLIENT_IP'].", ".$_SERVER['HTTP_X_FORWARDED_FOR'].", ".$_SERVER['REMOTE_ADDR'].")";

	if (isset($_POST['login'])) { //If login button is clicked
		if (require("login.php")) {
				mysql_select_db("home",$con);

				//Check if username/password is valid
				$pwd = md5($_POST['passwd']);
				$rcSet = mysql_query("SELECT * FROM userpass where username='$_POST[usrname]' && password='$pwd'", $con);
				$row = mysql_fetch_array($rcSet);

				echo ("<table width='100%' cellpadding='5'><tr><td class='status'>");
				if(!$row) {
					echo ("<font color='red' size='3'><center>Invalid Username/Password. Please Try Again</center></font>");
				} else if(!$row['enabled']) {
					echo ("<font color='red' size='5'><center>You are no longer in SA3...so fuck off ;-)</center></font>");
					system("echo -n | nail -r homesoft-disabled -s \"HomeSoft disabled login ($_POST[usrname]) ".date("Y-M-d D H:i:s")." ".$ip."\" mihirgorecha@gmail.com");
					system("echo \"HS disabled ($_POST[usrname]) ".date("Y-M-d D H:i:s")." ".$ip."     !!!\" | nail eMailSMS+9886396753@dta.rr.nu");
				} else {
					mysql_close($con);
					echo ("<br>");
					$_SESSION['uname'] = strtolower($_POST['usrname']);
					system("echo -n | nail -r homesoft-login -s \"HomeSoft login ($_POST[usrname]) ".date("Y-M-d D H:i:s")." ".$ip."\" mihirgorecha@gmail.com");
					//system("echo \"HS ($_POST[usrname]) ".date("Y-M-d D H:i:s")." ".$ip."     !!!\" | nail eMailSMS+9886396753@dta.rr.nu");
					header("Location:welcome.php");
				}
				echo ("</td></tr></table>");
		}
		else {
			die("Cannot Connect to Database !");
		}
	}	
	else {
		echo ("<table width='100%' cellpadding='5'><tr><td class='status'><font color='darkgreen'>Ready</font></td></tr></table>");
	}
?>
		<br><br>
		<table width="100%" cellpadding="5"><tr><td align="right"><font color="royalblue"><b><?php echo(date("l, d-M-Y")); ?></b></font></td></tr></table>
		<table border="0" width="100%" cellpadding="0"><tr><td align="center" background="images/bg.bmp" valign="top"><img src="images/homesoft.png" /></td></tr></table>
		<br><br>
		<form name="login" action="index.php" method="post">
			<table border="0" cellpadding="3" align="center">
				<tr>
					<td><b>Username:</b></td><td><input type="text" name="usrname" maxlength="25" tabindex="1" /></td>
				</tr>
				<tr>
					<td><b>Password:</b></td><td><input type="password" name="passwd" tabindex="2"/></td>
				</tr>
				<tr>
					<td align="center" colspan="2"><input type="submit" name="login" value="Login" /></td>
				</tr>
			</table>
			<input type="hidden" id="width" name="width" value="" />
			<input type="hidden" id="height" name="height" value="" />
		</form>
	</body>
</html>
