<?php
	session_start();
	if(!isset($_SESSION['uname']) || $_SESSION['uname']=="") { //Check for session
		header("Location:index.php");
	}
	unset($_SESSION['day']);
	unset($_SESSION['month']);
	unset($_SESSION['year']);
	unset($_SESSION['sel']);
?>

<html>
	<head>
		<title>Welcome to HomeSoft v1.0</title>
		<link rel="stylesheet" type="text/css" href="design.css" />
		<link rel="shortcut icon" href="favicon.ico">
		<script type="text/javascript">
			var newWindow;
			function pop(url) {
				newWindow = window.open(url, 'Create New User', 'height=300, width=400, left=100, top=100');
				if(window.focus) {
					newWindow.focus();
				}
			}
		</script>
	</head>
	<body bgcolor="#EEEEEE">
	<table border="0" width="100%" height="100%">
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
	<tr>
	<td height="100%">
	<h2><font color="#666600">Choose Your Option:</font></h2>
	<br><br>
	<table width="70%" cellpadding="5" border="0" align="center">
		<tr>
<!--
			<td align="center" width="25%"><b>Personal </b><font color="red">*Alpha*</font></td>
-->
			<td align="center" width="25%"><b>Common</b></td>
			<td align="center" width="25%"><b>Overall Statistics</b></td>
			<td align="center" width="25%"><b>Categorial Statistics</b></td>
		</tr>
<!--
			<td align="center" valign="top">
				<a href="personal/deposit.php">Add/Deposit Money</a><br><br>
				<a href="personal/expense.php">Add Expense</a><br><br>
				<a href="personal/viewMyExp.php">View My Transactions</a><br><br>
			</td>
-->
			<td align="center" valign="top">
				<?php if ($_SESSION['uname'] == "admin") { ?><a href="common/deposit.php">Deposit Money</a><br><br><a href="common/refund.php">Deposit Refund</a><br><br><?php } ?>
				<a href="common/addexp.php">Add Expenses</a><br><br>
				<a href='common/viewMyExp.php'>View My Transactions</a><br><br>
				<a href="common/viewexp.php">View All Transactions</a><br><br>
			</td>
			<td align="center" valign="top">
				<a href="graphics/dailyGraph.php">Daily Graph</a><br><br>
				<a href="graphics/weeklyGraph.php">Weekly Graph</a><br><br>
				<a href="graphics/monthlyGraph.php">Monthly Graph</a><br><br>
			</td>
			<td align="center" valign="top">
				<a href="graphics/dailyGraphCat.php">Daily Graph</a><br><br>
				<a href="graphics/weeklyGraphCat.php">Weekly Graph</a><br><br>
				<a href="graphics/monthlyGraphCat.php">Monthly Graph</a><br><br>
			</td>
		<tr>
			<td colspan="4"><br></td>
		</tr>
		<tr>
			<td colspan="4" align="center"><b>Account Privileges</b></td>
		</tr>
		<tr>
			<td colspan="4" align="center"><a href="changePass.php">Change Password</a></td>
		</tr>
		<?php
		if($_SESSION['uname'] == "admin" || $_SESSION['uname'] == "aakash") {
		?>
		<tr>
			<td colspan="4" align="center"><a href="exppdf.php">E-Mail Balance Sheet</a></td>
		</tr>
		<?php } ?>
		<tr>
			<td colspan="4" align="center">Transfer Funds</td>
		</tr>
		<?php
		if($_SESSION['uname'] == "admin") {
		?>
		<tr>
			<td colspan="4" align="center"><a href="javascript:pop('createUser.php');">Create New Account</a></td>
		</tr>
		<?php } ?>
		<tr>
			<td colspan="4" align="center">Close Account</td>
		</tr>
	</table>
	</td>
	</tr>
	<tr>
	<td valign="bottom">
	<?php
		echo ("<table width='100%' cellpadding='5'><tr><td class='status'><font color='darkgreen'>Ready</font></td></tr></table>");
	?>
	</td>
	</tr>
	</table>
	</body>
</html>