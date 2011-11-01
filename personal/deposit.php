<?php
	session_start();
	if(!isset($_SESSION['uname']) || $_SESSION['uname']=="") { //Check for session
		header("Location:../index.php");
	}
	$error_msg = 0;
	
	/*
		0 = not approved
		1 = approved
*/
?>

<html>
	<head>
		<title>Welcome to HomeSoft v1.0</title>
		<link rel="stylesheet" type="text/css" href="../design.css" />
		<script type="text/javascript">
			function subButton() {
				if (! confirm("Is the information filled correct?"))
					return false;
				return true;
			}
			
			function act() {
				document.getElementById("lenname").disabled = !document.getElementById("lenname").disabled;
				document.getElementById("namecheck").checked = !document.getElementById("namecheck").checked;
			}
			function chkName(obj) {
				var name;
				if (obj.value == "addname") {
					name = prompt("Enter Name");
					if (name == "") {
						alert("Please Enter a Name");
						document.getElementById("addedname").value = "???";
					}
					else if (name != null) {
						document.getElementById("addedname").value = name;
						window.location = "/personal/deposit.php?addedname=" + name;
					}
				}
			}
		</script>
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
	<tr><td colspan="3"><h2><font color="#666600">Add/Deposit Money (Personal):</font></h2></td></tr>
	<?php //If the form is not filled
	if (isset($_GET['addedname'])) {
		$flag = 0;
		require("../login.php");
		mysql_select_db("home", $con);
		$query = "SELECT username FROM userpass";
		$rcSet = mysql_query($query, $con);
		while ($row = mysql_fetch_array($rcSet)) {
			if ($row['username'] == trim($_GET['addedname'])) {
				$flag = 2;
				break;
			}
		}
		if ($flag == 0) {
			$query = "SELECT name FROM friends".$_SESSION['uname']."";
			$rcSet = mysql_query($query, $con);
			while ($row = mysql_fetch_array($rcSet)) {
				if ($row['name'] == trim($_GET['addedname'])) {
					$flag = 1;
					break;
				}
			}
		}
		if ($flag == 0) {
			$query = "INSERT INTO friends".$_SESSION['uname']." VALUES ('".strtolower($_GET['addedname'])."')";
			mysql_query($query, $con);
			$error_msg = 3;
		}
		else if ($flag == 2) {
			$error_msg = 5;
		}
		else {
			$error_msg = 4;
		}
	}
	if (isset($_POST['deposit'])) {
		if (!filter_var($_POST['amount'], FILTER_VALIDATE_INT) || $_POST['amount'] <= 0) {
			echo ("
				<tr><td colspan='3' height='100%' valign='top'>Please enter only DIGITS in 'Amount' Field<br>Zeros and Negative Numbers are not allowed<br><br><a href='addexp.php'><b>OK</b></a></td></tr>
			");
			$error_msg = 2;
		}
		else if (trim($_POST['reason']) == "") {
			echo ("
				<tr><td colspan='3' height='100%' valign='top'>Reason MUST be added<br><br><a href='deposit.php'><b>OK</b></a></td></tr>
			");
			$error_msg = 2;
		}
		else {
			echo("
			<tr><td colspan='3' height='100%' valign='top'>
			<table width='50%' cellpadding='5' border='0' align='center'>
				<tr>
					<td><b>Amount&nbsp;(Rs.):</b></td><td width='100%'>".$_POST['amount']."</td>
				</tr>
				<tr>
					<td><b>Comment (Mandatory):</b></td><td width='100%'>".$_POST['reason']."</td>
				</tr>
				<tr>
					<br><br><td colspan='2'><a href='../welcome.php'>Done</a><br><br>
					<a href='deposit.php'>Add/Deposit More Money</a></td>
				</tr>
			</table>
			</td></tr>
			");
			
			if(require("../login.php")) {
				mysql_select_db("home", $con);
				$_SESSION['common'] -= $_POST['amount'];
				$d = date("Y-m-d");
				$query = "INSERT INTO ".$_SESSION['uname']." VALUES ('', '$_SESSION[uname]', '',  '$_POST[amount]', '$_POST[reason]', '$d', '1')";
				mysql_query ($query, $con);
				mysql_close($con);
				$error_msg = 1;
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
		<form name='lended' action='deposit.php' method='post'>
		<table width='50%' cellpadding='5' border='0' align='center'>
			<tr>
				<td><b>Amount&nbsp;(Rs.):</b></td><td><input type='text' name='amount' maxlength='5' /></td>
				<td onDblClick='act();'>");
				if(isset($_GET['addedname'])) {
					echo("<input id='namecheck' type='checkbox' onClick='actNameField();' CHECKED />");
				}
				else {
					echo("<input id='namecheck' type='checkbox' onClick='actNameField();' />");
				}
				echo("<b>Borrowed from Someone?:</b>");
					if(isset($_GET['addedname'])) {
						echo("<select id='lenname' name='lenname' onChange='chkName(this);'>");
					}
					else {
						echo("<select id='lenname' name='lenname' onChange='chkName(this);' DISABLED>");
					}
						echo("<option value='empty'>-Select Name-</option>");
						require("../login.php");
						mysql_select_db("home", $con);
						// $query = "SELECT username FROM userpass";
						// $rcSet = mysql_query($query, $con);
						// while($row = mysql_fetch_array($rcSet)) {
							// if($row['username'] != $_SESSION['uname']) {
								// echo("<option value='".$row['username']."'>".$row['username']."</option>");
							// }
						// }
						$query = "SELECT name FROM friends".$_SESSION['uname']."";
						$rcSet = mysql_query($query, $con);
						while($row = mysql_fetch_array($rcSet)) {
							if($row['name'] == $_GET['addedname']) {
								echo("<option value='".$row['name']."' SELECTED>".$row['name']."</option>");
							}
							else {
								echo("<option value='".$row['name']."'>".$row['name']."</option>");
							}
						}
						mysql_close($con);
						echo("<option value='addname'>-Add Name-</option>
					</select>
					<input type='hidden' name='addedname' id='addedname' value='???' />
				</td>
			</tr>
			<tr>
				<td valign='top'><b>Comments&nbsp;(Mandatory):</b></td><td colspan='2'><textarea name='reason' rows='10' cols='40'></textarea></td>
			</tr>
			<tr>
				<td><br></td><td colspan='2'><input type='submit' name='deposit' value='Add Money' onclick = 'if (!checkCat()) return false;' />&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type='submit' name='cancel' value='Cancel' /></td>
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
			if($error_msg == 1) {			
				$statusMsg = "Record Added Successfully";
				$statusColor = "darkgreen";
			}
			else if ($error_msg == 2) {
				$statusMsg = "Record Rejected";
				$statusColor = "red";
			}
			else if ($error_msg == 3) {
				$statusMsg = "Name Added Successfully";
				$statusColor = "darkgreen";
			}
			else if ($error_msg == 4) {
				$statusMsg = "Name Already Exists";
				$statusColor = "red";
			}
			else if ($error_msg == 5) {
				$statusMsg = "You Cannot Add Record For Borrowing Money From Your Common Group";
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