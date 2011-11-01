<?php
require("login.php");
mysql_select_db("home",$con);
echo("<html>\n");
echo("<head>\n");
echo("<title>Welcome to HomeSoft v1.0</title>\n");
echo("</head>\n");
echo("<body bgcolor='#EEEEEE'>\n");
echo("\n");

$font = "font-family:monospace; color:black; font-size:16;";

echo("<table border='0' height='100%' width='100%'>\n");

echo("\t<tr><td valign='top' height='100%'>\n");
echo("\t\t<table style='border-collapse: collapse;' border='1' align='center' width='60%' cellpadding='5'>\n");
echo("\t\t\t<tr bgcolor='lightgray'>\n");
echo("\t\t\t\t<td align='center' style='".$font."'>MD5SUM_PLACEHOLDER</td>\n");
echo("\t\t\t\t<td align='center' style='".$font."'>".date("Y-M-d D H:i:s")."</td>\n");
echo("\t\t\t</tr>\n");
echo("\t\t</table>\n");
echo("\t</td></tr>\n");

echo("\t<tr></tr><tr></tr><tr></tr>\n");

echo("\t<tr><td valign='top' height='100%'>\n");
echo("\t\t<table style='border-collapse: collapse;' border='1' align='center' width='60%' cellpadding='5'>\n");
echo("\t\t\t<tr bgcolor='lightblue'>\n");
echo("\t\t\t\t<td style='".$font."'><b><center>Username</center></b></td>\n");
echo("\t\t\t\t<td style='".$font."'><b><center>Adjustments</center></b></td>\n");
echo("\t\t\t\t<td style='".$font."'><b><center>Total</center></b></td>\n");
echo("\t\t\t</tr>\n\n");

$rcSet = mysql_query("select * from userpass order by username");
$total_adjust = 0;
$total_all = 0;
while($row = mysql_fetch_array($rcSet)) {
	$username = $row['username'];
	$enabled  = $row['enabled'];
	$adjust   = $row['adjustments'];
	$tmp = mysql_fetch_array(mysql_query("select sum(amount) from common where username='".$username."' and approved='0'"));
	$amt_all = $adjust + $tmp['sum(amount)'];
	$bgcolor = ($amt_all==0?"#DDDDDD":($amt_all>0?"#a7ff9e":"coral"));
	$adjustments = ($adjust==0?str_repeat("&nbsp;",7):($adjust<0?"Recieve":str_repeat("&nbsp;",4)."Pay"))." Rs. ".str_repeat("&nbsp;",7-strlen(abs($adjust))).abs($adjust);
	$total = ($amt_all==0?str_repeat("&nbsp;",7):($amt_all<0?"Recieve":str_repeat("&nbsp;",4)."Pay"))." Rs. ".str_repeat("&nbsp;",7-strlen(abs($amt_all))).abs($amt_all);
	if ($enabled || $adjust || $amt_all) {
		echo("\t\t\t<tr bgcolor='".$bgcolor."'>\n");
		echo("\t\t\t\t<td style='".$font."'><center>".$username."</center></td>\n");
		echo("\t\t\t\t<td style='".$font."'><center>".$adjustments."</center></td>\n");
		echo("\t\t\t\t<td style='".$font."'><center>".$total."</center></td>\n");
		echo("\t\t\t</tr>\n");
	}
	$total_adjust += $adjust;
	$total_all += $amt_all;
 }
$total_adjust = ($total_adjust==0?str_repeat("&nbsp;",7):($total_adjust<0?"Recieve":str_repeat("&nbsp;",4)."Pay"))." Rs. ".str_repeat("&nbsp;",7-strlen(abs($total_adjust))).abs($total_adjust);
$total_all = ($total_all==0?str_repeat("&nbsp;",7):($total_all<0?"Recieve":str_repeat("&nbsp;",4)."Pay"))." Rs. ".str_repeat("&nbsp;",7-strlen(abs($total_all))).abs($total_all);
echo("\n\t\t\t<tr bgcolor='orange'>\n");
echo("\t\t\t\t<td style='".$font."'><center><b>Total</b></center></td>\n");
echo("\t\t\t\t<td style='".$font."'><center><b>".$total_adjust."</b></center></td>\n");
echo("\t\t\t\t<td style='".$font."'><center><b>".$total_all."</b></center></td>\n");
echo("\t\t\t</tr>\n");
echo("\t\t</table>\n");
echo("\t</td></tr>\n");

echo("\t<tr></tr><tr></tr><tr></tr>\n");

$tmp = mysql_fetch_array(mysql_query("select sum(amount) from common where approved='1'"));
$common_approved = -$tmp['sum(amount)'];
$tmp = mysql_fetch_array(mysql_query("select sum(amount) from common where approved='1' or approved='0'"));
$common_all = -$tmp['sum(amount)'];
$tmp = mysql_fetch_array(mysql_query("select sum(adjustments) from userpass"));
$common_adjustment = $common_approved + $tmp['sum(adjustments)'];


echo("\t<tr><td valign='top' height='100%'>\n");
echo("\t\t<table style='border-collapse: collapse;' border='1' align='center' width='60%' cellpadding='5'>");
echo("\n\t\t\t<tr bgcolor='lightblue'>\n");
echo("\t\t\t\t<td style='".$font."'><center><b>Common Balance (Approved)</b></center></td>\n");
echo("\t\t\t\t<td style='".$font."'><center><b>Rs. ".$common_approved."</b></center></td>\n");
echo("\t\t\t</tr>\n");
echo("\n\t\t\t<tr bgcolor='lightblue'>\n");
echo("\t\t\t\t<td style='".$font."'><center><b>Common Balance (All)</b></center></td>\n");
echo("\t\t\t\t<td style='".$font."'><center><b>Rs. ".$common_all."</b></center></td>\n");
echo("\t\t\t</tr>\n");
echo("\n\t\t\t<tr bgcolor='lightblue'>\n");
echo("\t\t\t\t<td style='".$font."'><center><b>Common Balance (In Box)</b></center></td>\n");
echo("\t\t\t\t<td style='".$font."'><center><b>Rs. ".$common_adjustment."</b></center></td>\n");
echo("\t\t\t</tr>\n");
echo("\t\t</table>\n");
echo("\t</td></tr>\n");

echo("\t<tr></tr><tr></tr><tr></tr>\n");

$font = "font-family:verdana; color:black; font-size:12;";

echo("\t<tr><td valign='top' height='100%'>\n");
echo("\t\t<table style='border-collapse: collapse;' border='1' align='center' width='80%' cellpadding='5'>\n");
echo("\t\t\t<tr bgcolor='lightgreen' style='".$font."'>\n");
echo("\t\t\t\t<td><b><center>Username</center></b></td>\n");
echo("\t\t\t\t<td><b><center>Credit (Rs.)</center></b></td>\n");
echo("\t\t\t\t<td><b><center>Debit (Rs.)</center></b></td>\n");
echo("\t\t\t\t<td><b><center>Cumulative (Rs.)</center></b></td>\n");
echo("\t\t\t\t<td><b><center>Date (YYYY-MM-DD)</center></b></td>\n");
echo("\t\t\t\t<td><b><center>Reason</center></b></td>\n");
echo("\t\t\t\t<td><b><center>Category</center></b></td>\n");
echo("\t\t\t\t<td><b><center>Status</center></b></td>\n");
echo("\t\t\t</tr>\n\n");

$cumulative = $common_all;
$cr_td_app = $cr_td_not = $db_td_app = $db_td_not = 0;
$rcSet = mysql_query("select * from common order by expno desc");
while($row = mysql_fetch_array($rcSet)) {
       	$bgcolor = ($row['approved']==20?"coral":($row['approved']==1?"lightgray":"lightblue"));
	$status  = ($row['approved']==20?"Rejected":($row['approved']==1?"Approved":"Approval&nbsp;Pending"));
	$credit  = ($row['amount']>0?abs($row['amount']):"<center>*</center>");
	$debit   = ($row['amount']<0?abs($row['amount']):"<center>*</center>");
	echo("\t\t\t<tr bgcolor='".$bgcolor."' style='".$font."'>\n");
	echo("\t\t\t\t<td><center>".$row['username']."</center></td>\n");
	echo("\t\t\t\t<td align='right'>".$credit."</td>\n");
        echo("\t\t\t\t<td align='right'>".$debit."</td>\n");
        echo("\t\t\t\t<td align='right'>".($row['approved']==20?"":$cumulative)."</td>\n");
        echo("\t\t\t\t<td><center>".$row['dateadded']."</center></td>\n");
        echo("\t\t\t\t<td>".$row['reason']."</td>\n");
        echo("\t\t\t\t<td><center>".$row['category']."</center></td>\n");
        echo("\t\t\t\t<td><center>".$status."</center></td>\n");
	echo("\t\t\t</tr>\n");
	$cumulative += ($row['approved']==20?0:$row['amount']);
	$cr_td_app += ($row['approved']==1?($row['amount']>0?abs($row['amount']):0):0);
        $cr_td_not += ($row['approved']==0?($row['amount']>0?abs($row['amount']):0):0);
        $db_td_app += ($row['approved']==1?($row['amount']<0?abs($row['amount']):0):0);
        $db_td_not += ($row['approved']==0?($row['amount']<0?abs($row['amount']):0):0);
 }

echo("\n\t\t\t<tr bgcolor='orange' style='".$font."'>\n");
echo("\t\t\t\t<td><b><center>Total</center></b></td>\n");
echo("\t\t\t\t<td align='right'><b>".$cr_td_app."</b><font size='1'><br>+".$cr_td_not."</b></td>\n");
echo("\t\t\t\t<td align='right'><b>".$db_td_app."</b><font size='1'><br>+".$db_td_not."</b></td>\n");
echo("\t\t\t\t<td><b><center>*</center></b></td>\n");
echo("\t\t\t\t<td><b><center>*</center></b></td>\n");
echo("\t\t\t\t<td><b><center>*</center></b></td>\n");
echo("\t\t\t\t<td><b><center>*</center></b></td>\n");
echo("\t\t\t\t<td><b><center>*</center></b></td>\n");
echo("\t\t\t</tr>\n\n");

echo("\t\t</table>\n");
echo("\t</td></tr>\n");


echo("</table>\n\n");
echo("</body>\n");
echo("</html>\n");
?>
