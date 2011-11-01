<?php
  //0 = not approved
  //1 = approved
  //20 = rejected

	//Calculate Approved Amount for Common
	require("login.php");
	mysql_select_db("home",$con);
		$rcSet = mysql_query("SELECT SUM(amount) FROM common WHERE approved='1'",$con);
		$row = mysql_fetch_array($rcSet);
		$common_amount = -$row['SUM(amount)'];

		//Add Adjustments
		$query = "SELECT SUM(adjustments) FROM userpass";
		$rcSet = mysql_query($query, $con);
		$row = mysql_fetch_array($rcSet);
		$common_adj = $common_amount + $row['SUM(adjustments)'];

		$rcSet = mysql_query("SELECT SUM(amount) FROM common WHERE approved='0' OR approved='1'",$con);
		$row = mysql_fetch_array($rcSet);
		$common_total = -$row['SUM(amount)'];

		if ($common_amount == NULL) {
			$common_amount = 0;
		}
		$_SESSION['common'] = $common_amount;

                $common_string = "Pool:&nbsp;Rs.&nbsp;";
                $common_string = $common_string."(Approved:&nbsp;".$common_amount.")&nbsp;";
                $common_string = $common_string."<b>(All:&nbsp;".$common_total.")</b>&nbsp;";
                $common_string = $common_string."(Box:&nbsp;".$common_adj.")";
                if ($common_total >= 0) {
                	$color = "darkgreen";
                 } else {
                	$color = "red";
                 }

		//Calculate Amount for Credit/Debit
		$dt = (date("Y")-1)."-".date("m")."-01";
		$rcSet = mysql_query("SELECT SUM(amount) FROM common WHERE username='$_SESSION[uname]' AND amount > '0' AND dateadded > '$dt' AND approved='0'",$con);
		$row = mysql_fetch_array($rcSet);
		if($row['SUM(amount)'] != NULL) {
			$amtCredit = $row['SUM(amount)'];
		}
		else {
			$amtCredit = 0;
		}

		$rcSet = mysql_query("SELECT SUM(amount) FROM common WHERE username='$_SESSION[uname]' AND amount < '0' AND dateadded > '$dt' AND approved='0'",$con);
		$row = mysql_fetch_array($rcSet);
		if($row['SUM(amount)'] != NULL) {
			$amtDebit = abs($row['SUM(amount)']);
		}
		else {
			$amtDebit = 0;
		}

		$amt2Collect = $amtCredit - $amtDebit;
		$rcSet = mysql_query("SELECT adjustments FROM userpass WHERE username='$_SESSION[uname]'", $con);
		$row = mysql_fetch_array($rcSet);
		$amt2Collect += $row['adjustments'];
		if($row['adjustments'] < 0) {
			$adjustmentAmt = "<td class='status'><font color='red'>Adjustment:&nbsp;Rs.&nbsp;".$row['adjustments']."</font></td>\n";
		}
		else {
			$adjustmentAmt = "<td class='status'><font color='darkgreen'>Adjustment:&nbsp;Rs.&nbsp;".$row['adjustments']."</font></td>\n";
		}

		//Decide if its credit or debit
		if($amt2Collect < 0) {
			$credeb = "<td class='status'><font color='red'>Pay:&nbsp;Rs.&nbsp;".abs($amt2Collect)."</font></td>\n";
		}
		else if($amt2Collect > 0) {
			$credeb = "<td class='status'><font color='darkgreen'>Recieve:&nbsp;Rs.&nbsp;".abs($amt2Collect)."</font></td>\n";
		}
		else {
			$credeb = "";
		}
		$msg = "<td class='status'><font color='darkgreen'>Cr:&nbsp;Rs.&nbsp;".$amtCredit."</font></td>\n<td class='status'><font color='red'>Db:&nbsp;Rs.&nbsp;".$amtDebit."</font></td>\n".$credeb;

		//Display approval messages only if the user is admin
		if($_SESSION['uname'] == "admin") {
			$row = mysql_fetch_array(mysql_query("SELECT COUNT(*) FROM common WHERE approved='0'",$con));
			$pending = "<td class='status'><a href='/pending.php'>".$row['COUNT(*)']."&nbsp;Approval".($row['COUNT(*)']==1?"":"s")."&nbsp;Pending</a></td>\n";
		}
		else {
			$pending = "";
		}

		//Display rejected message
		$row = mysql_fetch_array(mysql_query("SELECT COUNT(*) FROM common WHERE username='$_SESSION[uname]' AND approved='20'",$con));
$rej_count = $row['COUNT(*)'];
$rejected = "<td class='status'><a href='/rejected.php'>".$rej_count."&nbsp;Rejection".($rej_count==1?"":"s")."</a></td>\n";
	mysql_close($con);
if ($rej_count && basename($_SERVER['PHP_SELF'])!="rejected.php")
	header("Location:rejected.php");
	//Display all the main bar
echo("<table border='0' cellpadding='5' width='100%'>\n");
echo("<tr>\n");
echo("<td class='status'><a href='/welcome.php'>Home</a></td>\n");
echo("<td class='status' width='100%'>Welcome <b>".strtoupper($_SESSION['uname'])."</b></td>\n");
echo($adjustmentAmt);
echo($msg);
echo($pending);
echo($rejected);
echo("<td class='status'><font color='".$color."'>".$common_string."</font></td>\n");
echo("<td class='status'><a href=\"/logout.php\" onclick = \"if (! confirm('Are you sure?')) return false;\">Logout</a></td>\n");
echo("</tr>\n");
//echo("<tr><td colspan='100%' align='center'><font color='red' size='6'><blink>Made some changes to HomeSoft.<br/>Double check what you enter.</blink></font></td></tr>\n");
echo("</table>\n");
?>