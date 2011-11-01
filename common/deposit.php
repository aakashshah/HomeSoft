<?php
	session_start();
	if(!isset($_SESSION['uname'])) { //Check for session
		header("Location:../index.php");
	}
	else if ( $_SESSION['uname'] != "admin" ) {
		header("Location:../welcome.php");
	}
	$deposited = 0;
	
	/*
		0 = not approved
		1 = approved
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
		<?php
			require("../displogo.php");
		?>
	<tr><td colspan="3"><h2><font color="#666600">Deposit Money (Common):</font></h2></td></tr>
	<?php //If the form is not filled
	if (isset($_POST['deposit'])) {
		if (!filter_var($_POST['amount'], FILTER_VALIDATE_INT) || $_POST['amount'] <= 0) {
			echo ("
				<tr><td colspan='3' height='100%' valign='top'>Please enter only DIGITS in 'Amount' Field<br>Zeros and Negetive Numbers are not allowed<br><br><a href='deposit.php'><b>OK</b></a></td></tr>
			");
			$deposited = 2;
		}
		else {
			if(require("../login.php")) {
				mysql_select_db("home", $con);
				$_SESSION['common'] += $_POST['amount'];				
				$d = date("Y-m-d");
				
				foreach ($_POST['names'] as $dep) {
					if ($dep == "all") {
						$query = "SELECT * FROM userpass";
						$rcSet = mysql_query($query, $con);
						while ($row = mysql_fetch_array($rcSet)) {
							if ($row['enabled'] == 1) {
								$query = "INSERT INTO common VALUES ('', '$row[username]', '-$_POST[amount]', '$_POST[reason]', '$d', '0', 'Deposit')";
								mysql_query($query, $con);
							}
						}
						break;
					}
					else {
						$query = "INSERT INTO common VALUES ('', '$dep', '-$_POST[amount]', '$_POST[reason]', '$d', '0', 'Deposit')";
						mysql_query($query, $con);
					}
				}
				
				mysql_close($con);
				$deposited = 1;
			}
			else {
				die ("Cannot Connect to Database !");
			}
			
			echo("
			<tr><td colspan='3' height='100%' valign='top'>
			<table width='50%' cellpadding='5' border='0' align='center'>
				<tr>
					<td colspan='2'><b>Please approve request</b>
				</tr>
				<tr>
					<td><b>Amount&nbsp;(Rs.):</b></td><td width='100%'>".$_POST['amount']."</td>
				</tr>
				<tr>
					<td><b>Reason:</b></td><td width='100%'>".$_POST['reason']."</td>
				</tr>
				<tr>
					<br><br><td colspan='2'><a href='../welcome.php'>Done</a><br><br>
					<a href='deposit.php'>Deposit More Money</a></td>
				</tr>
			</table>
			</td></tr>
			");
		}
	}
	else if (isset($_POST['cancel'])) {
		header("Location:../welcome.php");
	}
	else {
		require("../login.php");
		mysql_select_db("home",$con);
		$query = "SELECT * FROM userpass";
		$rcSet = mysql_query($query, $con);
		if($rcSet) {
			$noOfUsers = mysql_num_rows($rcSet);
		}
		else {
			$noOfUsers = 0;
		}
				
		echo ("
		<tr><td colspan='3' height='100%' valign='top'>
		<form name='deposit' action='deposit.php' method='post'>
		<table width='50%' cellpadding='5' border='0' align='center'>
			<tr>
				<td colspan='3'>
				</td>
			</tr>
			<tr>
				<td><b>Amount (Rs.):</b></td><td><input type='text' name='amount' maxlength='5' tabindex='1' /></td>
				<td rowspan='3' valign='top'><b>Send Notice To:</b><br><br>
			");
				if ($noOfUsers != 1) {
					echo("<select name='names[]' MULTIPLE tabindex='3'>
						<option value='all' SELECTED>-ALL-</option>");
						while($row = mysql_fetch_array($rcSet)) {
							if ($row['enabled'] == 1) {
								echo("<option value='".$row['username']."'>".$row['username']."</option>");
							}
						}
					echo("</select>");
				}
				else {
					echo("No&nbsp;Users&nbsp;Yet.<br><br>Please&nbsp;Create&nbsp;a&nbsp;'New&nbsp;Account'<br>from&nbsp;your&nbsp;<a href='/welcome.php'>Home&nbsp;Page</a>");
				}
					mysql_close($con);
					echo("
				</td>
			</tr>
			<tr>
				<td valign='top'><b>Comments (Optional):</b></td><td><textarea name='reason' rows='10' cols='40' tabindex='2'></textarea></td>
			</tr>
			<tr>
				<td><br></td><td><input type='submit' name='deposit' value='Deposit' onclick = \"if (! confirm('Is the information filled correct?')) return false;\" />&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type='submit' name='cancel' value='Cancel' /></td>
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