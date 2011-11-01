<?php
	session_start();
	if(!isset($_SESSION['uname']) || $_SESSION['uname']=="") { //Check for session
		header("Location:../index.php");
	}
	$deposited = 0;
	
	/*
		0 = not approved
		1 = approved
*/

function eMailList($con)
{
	$emailto = "";
	$emailpending = "";
	$rcSet = mysql_query("select * from userpass", $con);
	while($row = mysql_fetch_array($rcSet)) {
		if ($row['enabled']) {
			if ($row['email']) {
				if ($emailto != "") {
					$emailto = $emailto." ";
				}
				$emailto = $emailto.$row['email'];
			} else {
				if ($emailpending != "") {
					$emailpending = $emailpending." ";
				}
				$emailpending = $emailpending.$row['username'];
			}
		}
	}
	if ($emailpending != "") {
		system("echo \"".$emailpending."\" | mailx -r homesoft-addexp -s \"Add email ids\" mihirgorecha@gmail.com");
	}
	return $emailto;
}
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
			function checkCat() {
				if (document.getElementById("category").value == "empty") {
					alert("Please Select a Category !");
					return false;
				}
				else {
					if (!subButton())
						return false;
					return true;
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
	<tr><td colspan="3"><h2><font color="#666600">Add Expense (Common):</font></h2></td></tr>
	<?php //If the form is not filled
	if (isset($_POST['deposit'])) {
		if (!filter_var($_POST['amount'], FILTER_VALIDATE_INT) || $_POST['amount'] <= 0) {
			echo ("
				<tr><td colspan='3' height='100%' valign='top'>Please enter only DIGITS in 'Amount' Field<br>Zeros and Negative Numbers are not allowed<br><br><a href='addexp.php'><b>OK</b></a></td></tr>
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
					<td colspan='2'><b>Your request is awaiting approval</b></a>
				</tr>
				<tr>
					<td><b>Amount&nbsp;(Rs.):</b></td><td width='100%'>".$_POST['amount']."</td>
				</tr>
				<tr>
					<td><b>Category:</b></td><td width='100%'>".$_POST['category']."</td>
				</tr>
				<tr>
					<td><b>Comment (Mandatory):</b></td><td width='100%'>".$_POST['reason']."</td>
				</tr>
				<tr>
					<br><br><td colspan='2'><a href='../welcome.php'>Done</a><br><br>
					<a href='addexp.php'>Add More Expenses</a></td>
				</tr>
			</table>
			</td></tr>
			");

			if(require("../login.php")) {
				mysql_select_db("home", $con);
				$_SESSION['common'] -= $_POST['amount'];
				$d = date("Y-m-d");
				$query = "INSERT INTO common VALUES ('', '$_SESSION[uname]', '$_POST[amount]', '$_POST[reason]', '$d', '0', '$_POST[category]')";
				mysql_query ($query, $con);
				$deposited = 1;
				$msg = "user: $_SESSION[uname]\n".
					"category: $_POST[category]\n".
					"amount: $_POST[amount]\n".
					"reason: ".escapeshellcmd(str_replace("\n"," ",str_replace("\r\n"," ",$_POST[reason])))."\n".
					"via: web\n";
				system("echo \"".$msg."\" | mailx -r homesoft-addexp -s \"Homesoft expense added (".date("Y-M-d D H:i:s").")\" ".eMailList($con));
				//system("echo ".$msg." | nail -r homesoft-addexp -s \"Homesoft expense added (".date("Y-M-d D H:i:s").")\" mihirgorecha@gmail.com");
				//system("echo ".$msg." !!!"." | nail eMailSMS+9886396753@dta.rr.nu");
				mysql_close($con);
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
		<form name='expense' action='addexp.php' method='post'>
		<table width='50%' cellpadding='5' border='0' align='center'>
			<tr>
				<td><b>Amount&nbsp;(Rs.):</b></td><td><input type='text' name='amount' maxlength='5' /></td>
				<td width='100%'>
					<b>Category:&nbsp;</b>");
						require("../login.php");
						mysql_select_db("home", $con);
						$query = "SELECT category FROM expcat";
						$rcSet = mysql_query($query, $con);
						echo("<select id='category' name='category'>");
							echo("<option value='empty'>- Select Category -</option>");
						while($row = mysql_fetch_array($rcSet)) {
							echo("<option value='".$row['category']."'>".$row['category']."</option>");
						}
						echo("</select>");
				echo("</td>
			</tr>
			<tr>
				<td valign='top'><b>Comments&nbsp;(Mandatory):</b></td><td colspan='2'><textarea name='reason' rows='10' cols='40'></textarea></td>
			</tr>
			<tr>
				<td><br></td><td colspan='2'><input type='submit' name='deposit' value='Add Expense' onclick = 'if (!checkCat()) return false;' />&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type='submit' name='cancel' value='Cancel' /></td>
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