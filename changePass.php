<?php
	session_start();
	if(!isset($_SESSION['uname']) || $_SESSION['uname']=="") { //Check for session
		header("Location:index.php");
	}
	$error = 0;
?>

<html>
	<head>
		<title>Welcome to HomeSoft v1.0</title>
		<link rel="stylesheet" type="text/css" href="../design.css" />
	</head>
	<body bgcolor="#EEEEEE">
	<table border="0" height="100%" width="100%">
		<tr>
			<td>
			<?php		
				//Display the Main Bar
				require("mainBar.php");	
			?>
			</td>
		</tr>
		<tr><td><br></td></tr>
		<?php
			require("displogo.php");
		?>
		<tr><td><br></td></tr>
		<tr>
			<td height="100%" valign="top">
			<?php
				if (isset($_POST['changepass'])) {
					if($_POST['newpass'] == $_POST['newpassrt']) {
						require("login.php");
						mysql_select_db("home",$con);
						
						$pwdToMatch = md5($_POST['oldpass']);
						
						$query = "SELECT password FROM userpass WHERE username='$_SESSION[uname]'";
						$rcSet = mysql_query($query,$con);
						$row = mysql_fetch_array($rcSet);
						
						if($row['password'] == $pwdToMatch) {
							$newpwd = md5($_POST['newpass']);
							$query = "UPDATE userpass SET password='".$newpwd."' WHERE username='$_SESSION[uname]'";
							mysql_query($query,$con);
							$error = 1;
							echo("
								<br><br><h2>Change Password:</h2><br><br>
							");
							echo("<table align='center'><tr><td><b>Password has been changed</b><br><br><a href='welcome.php'>OK</a></td></tr></table>");
						}
						else {
							echo("
								<br><br><h2>Change Password:</h2><br><br>
							");
							echo("<table align='center'><tr><td><b>Invalid old password</b><br><br><a href='changePass.php'>OK</a></td></tr></table>");
							$error = 2;
						}
					}
					else {
						echo("
							<br><br><h2>Change Password:</h2><br><br>
						");
						echo("<table align='center'><tr><td><b>New Passwords entered are not matching</b><br><br><a href='changePass.php'>OK</a></td></tr></table>");
						$error = 2;
					}
				}
				else if (isset($_POST['cancel'])) {
					header("Location:welcome.php");
				}
				else {
			?>
			<h2><font color="#666600">Change Password:</font></h2>
				<form name="password" action="changePass.php" method="post">
					<table border="0" cellpadding="5" width="50%" align="center">
						<tr>
							<td align="right"><b>Old Password:</b></td>
							<td align="left"><input type="password" name="oldpass" /></td>
						</tr>
						<tr>
							<td align="right"><b>New Password:</b></td>
							<td align="left"><input type="password" name="newpass" /></td>
						</tr>
							<td align="right"><b>Retype New Password:</b></td>
							<td align="left"><input type="password" name="newpassrt" /></td>
						<tr>
							<td align="center" colspan="2"><input type="submit" name="changepass" value="Change Password" />&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type="submit" name="cancel" value="Cancel" /></td>
						</tr>
					</table>
				</form>
				<?php } ?>
			</td>
		</tr>
		<tr>
			<td valign="bottom">
				<?php
					if($error == 1) {
						$statusMsg = "Password Changed Sucessfully";
						$statusColor = "darkgreen";
					}
					else if($error == 2) {
						$statusMsg = "Old Password Retained";
						$statusColor = "red";
					}
					else {
						$statusMsg = "Ready";
						$statusColor = "darkgreen";
					}
					echo ("<table width='100%' cellpadding='5'><tr><td class='status'><font color='".$statusColor."'>".$statusMsg."</font></td></tr></table>");
				?>
			</td>
		</tr>
	</table>
	</body>
</html>