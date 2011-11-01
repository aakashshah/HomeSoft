<?php
	session_start();
	if(!isset($_SESSION['uname']) || $_SESSION['uname']=="") { //Check for session
		header("Location:../index.php");
	}
	$deposited = 0;
	
	/*
		00 = deposit and not approved
		01 = deposit and approved
		02 = notice
		10 = expense and not approved
		11 = expense and approved
*/
?>

<html>
	<head>
		<title>Welcome to HomeSoft v1.0</title>
		<link rel="stylesheet" type="text/css" href="../design.css" />
	</head>
	<body bgcolor="#EEEEEE">
		<table width="100%" height="100%" border="0" cellpadding="5">
		<tr>
		<td colspan="3">
		<?php		
			//Display the Main Bar
			require("../mainBar.php");	
		?>
		</td>
		</tr>
	<tr><td colspan="3"><br><br><h2>Collect Money (Common):</h2><br><br></td></tr>
	<?php //If the form is not filled
	if (isset($_POST['collect'])) {
		if (!filter_var($_POST['amount'], FILTER_VALIDATE_INT) || $_POST['amount'] <= 0) {
			echo ("
				<tr><td colspan='3' height='100%' valign='top'>Please enter only DIGITS in 'Amount' Field<br>Zeros and Negetive Numbers are not allowed<br><br><a href='addexp.php'><b>OK</b></a></td></tr>
			");
			$deposited = 2;
		}
		else if (trim($_POST['reason']) == "") {
			echo ("
				<tr><td colspan='3' height='100%' valign='top'>Reason MUST be added<br><br><a href='addexp.php'><b>OK</b></a></td></tr>
			");
			$deposited = 2;
		}
		else {
			echo("
			<tr><td colspan='3' height='100%' valign='top'>
			<table width='50%' cellpadding='5' border='0' align='center'>
				<tr>
					<td><b>Amount to Collect (Rs.):</b></td><td width='100%'>".$_POST['amount']."</td>
				</tr>
				<tr>
					<td><b>Comment (Mandatory):</b></td><td width='100%'>".$_POST['reason']."</td>
				</tr>
				<tr>
					<br><br><td colspan='2'><a href='../welcome.php'>Done</a><br><br>
					<a href='collect.php'>Collect More Money</a></td>
				</tr>
			</table>
			</td></tr>
			");
			
			if(require("../login.php")) {
				mysql_select_db("home", $con);
				$_SESSION['common'] -= $_POST['amount'];
				$d = date("Y-m-d");
				$query = "INSERT INTO common VALUES ('', '$_SESSION[uname]', '$_POST[amount]', '$_POST[reason]', '$d', '02')";
				mysql_query ($query, $con);
				mysql_close($con);
				$deposited = 1;
			}
			else {
				die ("Cannot Connect to Database !");
			}
		}
	}
	else if (isset($_POST['cancel'])) {
		header("Location:../welcome.php");
	}
	else {
		echo ("
		<tr><td colspan='3' height='100%' valign='top'>
		<form name='coll' action='collect.php' method='post'>
		<table width='50%' cellpadding='5' border='0' align='center'>
			<tr>
				<td><b>Amount to Collect (Rs.):</b></td><td><input type='text' name='amount' maxlength='5' />
					<select>
						<option >from all</option>
						<option>in total</option>
					</select>
				</td>
			</tr>
			<tr>
				<td valign='top'><b>Comments (Mandatory):</b></td><td><textarea name='reason' rows='10' cols='40'></textarea></td>
			</tr>
			<tr>
				<td><br></td><td><input type='submit' name='collect' value='Send Notice' onclick = \"if (! confirm('Is the information filled correct?')) return false;\" />&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type='submit' name='cancel' value='Cancel' /></td>
			</tr>
		</table>
		</form>
		</td></tr>
		");
	}
	?>
	<tr>
		<td colspan="3" valign="bottom">
		<?php
			if($deposited == 1) {			
				$statusMsg = "Record Added Succesfully";
				$statusColor = "darkgreen";
			}
			else if ($deposited == 2) {
				$statusMsg = "Record Rejected";
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