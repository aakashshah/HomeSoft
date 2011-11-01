<?php
	session_start();
	if(!isset($_SESSION['uname']) || $_SESSION['uname']=="") { //Check for session
		header("Location:../index.php");
	}
	if(isset($_POST['day'])) {
		//echo("Day Set");
		$_SESSION['day'] = $_POST['day'];
		$_SESSION['month'] = $_POST['month'];
		$_SESSION['year'] = $_POST['year'];
	}
	if(isset($_SESSION['day'])) {
		//echo("Day Set Session");
		$_POST['day'] = $_SESSION['day'];
		$_POST['month'] = $_SESSION['month'];
		$_POST['year'] = $_SESSION['year'];
		//echo($_POST['day']);
		//echo($_POST['month']);
		//echo($_POST['year']);
	}
	
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
	<table border="0" height="100%" width="100%">
		<tr>
			<td>
			<?php		
				//Display the Main Bar
				require("../mainBar.php");	
			?>
			</td>
		</tr>
		<tr><td><br></td></tr>
		<?php
			require("../displogo.php");
		?>
		<tr><td><br></td></tr>
	<?php
		if(isset($_POST['viewMyExp'])) {
			echo("<tr><td valign='top' height='100%'");
			//Sort Options
			if(isset($_POST['srt'])) {
				$orderby = $_POST['srt'];
				$order = $_POST['ascdsc'];
			}
			else {
				$orderby = "expno";
				$order = "DESC";
			}
			//Build Date Format
			if($_POST['month'] < 10) {
				$_POST['month'] = "0".$_POST['month'];
			}
			if($_POST['day'] < 10) {
				$_POST['day'] = "0".$_POST['day'];
			}
			//Connect to Database
			require("../login.php");
			mysql_select_db("home",$con);
			if ($_POST['year'] == 0) {
				$rcSet = mysql_query("SELECT * FROM ".$_SESSION['uname']." ORDER BY ".$orderby." ".$order);
			}
			else if ($_POST['month'] == 0) {
				$d = $_POST['year']."-%";
				$rcSet = mysql_query("SELECT * FROM ".$_SESSION['uname']." WHERE dateadded LIKE '$d' ORDER BY ".$orderby." ".$order);
			}
			else if ($_POST['day'] != 0) {
				$d = $_POST['year']."-".$_POST['month']."-".$_POST['day'];
				$rcSet = mysql_query("SELECT * FROM ".$_SESSION['uname']." WHERE dateadded='$d' ORDER BY ".$orderby." ".$order);
			}
			else {
				$d = $_POST['year']."-".$_POST['month']."-%";
				$rcSet = mysql_query("SELECT * FROM ".$_SESSION['uname']." WHERE dateadded LIKE '$d' ORDER BY ".$orderby." ".$order);
			}
			
			if(mysql_num_rows($rcSet) > 0) {
				echo("<br><br><table class='coll' border='1' align='center' width='80%' cellpadding='5'>");
				echo("<tr><td colspan='6'><form action='viewMyExp.php' method='post'>
					<b>Sort Data By:</b>&nbsp;&nbsp;&nbsp;
					<select name='srt'>
						<option value='name'>Name</option>
						<option value='amount'>Amount</option>
						<option value='dateadded'>Date</option>
						<option value='reason'>Reason</option>
					</select>&nbsp;&nbsp;&nbsp;
					<select name='ascdsc'>
						<option value='ASC'>Ascending Order</option>
						<option value='DESC'>Descending Order</option>
					</select>&nbsp;&nbsp;&nbsp;
					<input type='submit' name='sort' value='Go' />
					<input type='hidden' name='viewMyExp' value='submit' />
				</form></td></tr>");
				echo("<tr>");
					echo("<td><b>Expense&nbsp;For</b></td>");
					echo("<td><b>Credit (Rs.)</b></td>");
					echo("<td><b>Debit (Rs.)</b></td>");
					echo("<td><b>Date (YYYY-MM-DD)</b></td>");
					echo("<td width='100%'><b>Reason</b></td>");
					//echo("<td align='center'><b>Category</b></td>");
					echo("<td align='center'><b>Status</b></td>");
				echo("</tr>");
				$cnt = 0;
				$credit = 0;
				$debit = 0;
				$notCre = 0;
				$notDeb = 0;
				while ($row = mysql_fetch_array($rcSet)) {
					if($cnt%2 == 0) {
						$col = " bgcolor='#DDDDDD'";
					}
					else {
						$col = "";
					}
					if($row['clear'] == 0 && $row['amount'] < 0) {
						$col = " bgcolor='lightblue'";
						$status = "Lent";
					}
					else if($row['clear'] == 0 && $row['amount'] > 0) {
						$col = " bgcolor='coral'";
						$status = "Borrowed";
					}
					else {
						$status = "Clear";
					}
					echo("<tr".$col.">");
						echo("<td align='center'>".$row['name']."</td>");
						if($row['amount'] > 0) {
							echo("<td align='right'>".abs($row['amount'])."</td>");
							echo("<td align='center'>*</td>");
							if($row['clear'] == 1) {
								$credit += $row['amount'];
							}
							else {
								$notCre += $row['amount'];
							}
						}
						else {
							echo("<td align='center'>*</td>");
							echo("<td align='right'>".abs($row['amount'])."</td>");
							if($row['clear'] == 1) {
								$debit += $row['amount'];
							}
							else {
								$notDeb += $row['amount'];
							}
						}
						echo("<td align='center'>".$row['dateadded']."</td>");
						echo("<td width='100%'>".$row['reason']."</td>");
						//echo("<td align='center'>".$row['category']."</td>");
						echo("<td align='center'>".$status."</td>");
					echo("</tr>");
					$cnt++;
				}
				
				echo("<tr bgcolor='orange'>
						<td align='center'><b>Total</b></td>
						<td align='right'><b>".abs($credit)."</b><font size='1'><br>+".abs($notCre)."</font></td>
						<td align='right'><b>".abs($debit)."</b><font size='1'><br>+".abs($notDeb)."</font></td>
						<td align='center'><b>*</b></td>
						<td align='center'><b>*</b></td>
						<td align='center'><b>*</b></td>
					</tr>");
				echo("<tr><form action='viewMyExp.php' method='post'><td colspan='6' align='center'><input type='submit' value='Done' /></td></form></tr>");
				echo("</table>");
				mysql_close($con);
				echo("<br><br></td></tr>");
			}
			else {
				echo("<center><br><br><br><br><b>No Records Fetched</b><br><br><a href='viewMyExp.php'>OK</a></center>");
			}			
		}
		else if(isset($_POST['cancel'])) {
			header("Location:../welcome.php");
		}
		else {
	?>
		
			<tr>
				<td height="100%" valign="top">
					<h2><font color="#666600">View My Transactions For:</font></h2>
					<form name="viewMyExp" action="viewMyExp.php" method="post">
						<table border="0" cellpadding="5" align="center" width="60%">
							<tr>
								<td><b>Day:</b></td>
								<td width="100%">
									<select name="day">
										<option value='0'>-All Days-</option>
										<?php
											$sel = "";
											for($i=1; $i<=31; $i++) {
												if($i == date("d")) {
													$sel = "selected='selected'";
												}
												else {
													$sel = "";
												}
												echo("<option value='".$i."' ".$sel.">");
												echo($i);
												echo("</option>");
											}
										?>
									</select>
								</td>
							</tr>
							<tr>
								<td><b>Month:</b></td>
								<td width="100%">
									<select name="month">
										<option value='0'>-All Months-</option>
										<?php
											$sel = "";
											for($i=1; $i<=12; $i++) {
												if($i == date("m")) {
													$sel = "selected='selected'";
												}
												else {
													$sel = "";
												}
												$month = mktime(0,0,0,$i,$i,$i);
												echo("<option value='".$i."' ".$sel.">");
												echo(date("F",$month));
												echo("</option>");
											}
										?>
									</select>
								</td>
							</tr>
							<tr>
								<td><b>Year:</b></td>
								<td width="100%">
									<select name="year">
										<option value='0'>-All Years-</option>
										<?php
											$sel = "";
											for($i=2005; $i<2015; $i++) {
												if($i == date("Y")) {
													$sel = "selected='selected'";
												}
												else {
													$sel = "";
												}
												echo("<option value='".$i."' ".$sel.">");
												echo($i);
												echo("</option>");
											}
										?>
									</select>
								</td>
							</tr>
							<tr>
								<td colspan="2"><br><br></td>
							</tr>
							<tr>
								<td colspan="2">
									<input type="submit" name="viewMyExp" value="View Transactions" />&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
									<input type="submit" name="cancel" value="Cancel" />
								</td>
							</tr>
						</table>
					</form>
				</td>
			</tr>
			<?php } ?>
			<tr>
				<td valign="bottom">
					<?php
						$statusMsg = "Ready";
						$statusColor = "darkgreen";
						echo ("<table width='100%' cellpadding='5'><tr><td class='status'><font color='".$statusColor."'>".$statusMsg."</font></td></tr></table>");
					?>
				</td>
			</tr>
		</table>
	</body>
</html>