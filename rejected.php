<?php
	session_start();
	if(!isset($_SESSION['uname']) || $_SESSION['uname']=="") { //Check for session
		header("Location:index.php");
	}
	
	/*
		00 = deposit and not approved
		01 = deposit and approved
		02 = notice
		10 = expense and not approved
		11 = expense and approved
		20 = rejected
	*/
?>
<html>
	<head>
		<title>Welcome to HomeSoft v1.0</title>
		<link rel="stylesheet" type="text/css" href="design.css" />
		<link rel="shortcut icon" href="favicon.ico">
		<script type="text/javascript">
		function checkAll() {
			for(var i=0; i < document.rejdata.elements.length; i++) {
				if (document.rejdata.elements[i].type == "checkbox") {
					document.rejdata.elements[i].checked = true;
				}
			}
		}
			
		function unCheckAll() {
			for(var i=0; i < document.rejdata.elements.length; i++) {
				if (document.rejdata.elements[i].type == "checkbox") {
					document.rejdata.elements[i].checked = false;
				}
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
		<?php
			require("displogo.php");
		?>
		<tr>
			<td height='100%' valign='top'>
			<?php
			//Connect to Database
			require("login.php");
			mysql_select_db("home",$con);
			
			if(isset($_POST['delSel'])) {
				foreach ($_POST['app'] as $aprv) {
					$query = "DELETE FROM common WHERE expno='$aprv'";
					mysql_query($query,$con);
				}
				header("Location:rejected.php");
			}
			
			if(isset($_POST['cancel'])) {
				header("Location:welcome.php");
			}
			
			if(isset($_POST['srt'])) {
				$orderby = $_POST['srt'];
				$order = $_POST['ascdsc'];
			}
			else {			
				$orderby = "expno";
				$order = "DESC";
			}
			
			$rcSet = mysql_query("SELECT * FROM common WHERE username='$_SESSION[uname]' AND approved='20' ORDER BY ".$orderby." ".$order);
			if(mysql_num_rows($rcSet) > 0) {
				echo("<br><b><font color='red'><center>Following ".((mysql_num_rows($rcSet)<2)?"entry has":"entries have")." been rejected. Please delete ".((mysql_num_rows($rcSet)<2)?"it":"them").".</center></font></b>");
			echo("<br><br><table class='coll' border='1' align='center' width='80%' cellpadding='5'>");
			echo("<tr><td colspan='6'><form action='rejected.php' method='post'>
				<b>Sort Data By:</b>&nbsp;&nbsp;&nbsp;
				<select name='srt'>
					<option value='username'>Name</option>
					<option value='amount'>Amount</option>
					<option value='dateadded'>Date</option>
					<option value='reason'>Reason</option>
				</select>&nbsp;&nbsp;&nbsp;
				<select name='ascdsc'>
					<option value='ASC'>Ascending Order</option>
					<option value='DESC'>Descending Order</option>
				</select>&nbsp;&nbsp;&nbsp;
				<input type='submit' name='sort' value='Go' />
			</form></td></tr>");
						
			echo("<form name='rejdata' action='rejected.php' method='post'><tr>");
				echo("<td align='center'><b>Select</b><br><a href='javascript:void(0);' onClick='checkAll();'>All</a>&nbsp;/&nbsp;<a href='javascript:void(0);' onClick='unCheckAll();'>None</a></td>");
				echo("<td><b>Username</b></td>");
				echo("<td><b>Credit (Rs.)</b></td>");
				echo("<td><b>Debit (Rs.)</b></td>");
				echo("<td><b>Date (YYYY-MM-DD)</b></td>");
				echo("<td width='100%'><b>Reason</b></td>");
			echo("</tr>");
			$cnt = 0;
			$credit = 0;
			$debit = 0;
			while ($row = mysql_fetch_array($rcSet)) {
				if($cnt%2 == 0) {
					$col = " bgcolor='#DDDDDD'";
				}
				else {
					$col = "";
				}
				echo("<tr".$col.">");
					echo("<td align='center'><input type='checkbox' name='app[]' value='$row[expno]' /></td>");
					echo("<td align='center'>".$row['username']."</td>");
					if($row['amount'] > 0) {
						echo("<td align='right'>".abs($row['amount'])."</td>");
						echo("<td align='center'>*</td>");
						if($row['approved'] != 0) {
							$credit += $row['amount'];
						}
					}
					else {
						echo("<td align='center'>*</td>");
						echo("<td align='right'>".abs($row['amount'])."</td>");
						if($row['approved'] != 0) {
							$debit += $row['amount'];
						}
					}
					echo("<td align='center'>".$row['dateadded']."</td>");
					echo("<td width='100%'>".$row['reason']."</td>");
				echo("</tr>");
				$cnt++;
			}
			
			echo("<tr bgcolor='orange'>
						<td align='center'><br></td>
						<td align='center'><b>Total</b></td>
						<td align='right'><b>".abs($credit)."</b></td>
						<td align='right'><b>".abs($debit)."</b></td>
						<td align='center'><b>*</b></td>
						<td align='center'><b>*</b></td>
					</tr>");
					
			echo("<tr><td colspan='6' align='center'><input type='submit' name='delSel' value='Delete Selected' />&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type='submit' name='cancel' value='Done' /></td></tr></form>");
			echo("</form></table>");
			}
			else {
				echo("<center><br><br><br><br><b>You Have No Rejections</b><br><br><a href='welcome.php'>OK</a></center>");
			}
			mysql_close($con);
			echo("<br><br></td></tr>");
			?>
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