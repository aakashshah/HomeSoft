<?php
require("login.php");
mysql_select_db("home",$con);

function addMobile() {
	$usage = "Usage - 'ADDMOBILE username password'";
	$username = strtok(" ");
	$password = strtok(" ");
	if ($username == "")
		return "Error - No username given. ".$usage;
	if ($password == "")
		return "Error - No password given. ".$usage;
	if (!isset($_GET['mobile']))
		return "Error - No mobile given. ".$usage;
	if (mysql_num_rows(mysql_query("SELECT * from userpass where username='".
				       mysql_real_escape_string($username).
				       "' AND password='".
				       md5($password).
				       "'"))) {
		if (mysql_query("UPDATE userpass SET mobile='".
				mysql_real_escape_string($_GET['mobile']).
				"' WHERE username='".
				mysql_real_escape_string($username).
				"'")) {
			adminEmail("Mobile ".mysql_real_escape_string($_GET['mobile']).
				   " added for ".$username,
				   "");
			return "Success - Mobile ".
				mysql_real_escape_string($_GET['mobile']).
				" added for ".$username;
		} else {
			adminEmail("Error addMobile sql insert",
				   "Username: (".$username.")".
				   " Mobile: (".$_GET['mobile'].")");
			return "Error while sql insert.";
		}
	} else {
		return "Error - Invalid user/pass. ".$usage;
	}
}
function addExp() {
	$usage = "Usage - 'ADDEXP {B|C|F|O|W} amount reason'";
	$cat = strtok(" ");
	$category = "";
	$amount = strtok(" ");
	$reason = strtok("");
	$username = mobile2username();
	if ($username == "") {
		return "Error - mobile/username not found. Register with 'ADDMOBILE username password'";
	}
	switch(strtoupper($cat)) {
	case "B":
		$category = "Bills";
		break;
	case "C":
		$category = "Consumables";
		break;
	case "F":
		$category = "Food";
		break;
	case "O":
		$category = "Other Expenses";
		break;
	case "W":
		$category = "Water";
		break;
	case "D":
		$category = "Deposits";
		break;
	case "H":
		$category = "House Rent";
		break;
	default:
		return "Error - Invalid category. ".$usage;
	}
	if (!filter_var($amount, FILTER_VALIDATE_INT) || $amount <= 0) {
		return "Error - Invalid amount. ".$usage;
	}
	if ($reason == "") {
		return "Error - No reason given. ".$usage;
	}
	$d = date("Y-m-d");
	$outstanding_before = outstanding($username);
	if (mysql_query("INSERT INTO common VALUES ('', '$username', '$amount', '$reason', '$d', '0', '$category')")) {
		$outstanding_after = outstanding($username);
		adminEmail("ADDEXP $username $category $amount $reason","");
		adminSmsExp($username, $category, $amount, $reason);
		return "Success ADDEXP - Prev (".
			$outstanding_before.
			") now (".
			$outstanding_after.
			")";
	} else {
		adminEmail("Error addExp sql insert",
			   "Username: (".$username.")".
			   " Mobile: (".$_GET['mobile'].")".
			   " Cat: (".$cat.")".
			   " Category: (".$category.")".
			   " Amount: (".$amount.")".
			   " Reason: (".$reason.")");
		return "Error while sql insert.";
	}
}
function mobile2username() {
	if (isset($_GET['mobile'])) {
		$rcSet = mysql_query("SELECT username from userpass WHERE mobile='".
				     mysql_real_escape_string($_GET['mobile'])."'");
		if (mysql_num_rows($rcSet) == 1) {
			$row = mysql_fetch_array($rcSet);
			return $row['username'];
		}
	}
	return "";
}
function outstanding($username) {
	$rcSet = mysql_query("SELECT SUM(amount) FROM common ".
			     "WHERE username='$username' ".
			     "AND approved='0'");
	$row = mysql_fetch_array($rcSet);
	$pending = $row['SUM(amount)'];

	$rcSet = mysql_query("SELECT adjustments FROM userpass ".
			     "WHERE username='$username' ");
	$row = mysql_fetch_array($rcSet);
	$adjustments = $row['adjustments'];

	$total = $pending + $adjustments;
	if ($total < 0) {
		return "Pay Rs. ".abs($total);
	} else if ($total > 0) {
		return "Rcv Rs. ".abs($total);
	} else {
		return "0";
	}
}
function adminEmail($subject, $body) {
	system("echo $body | ".
	       "nail -r sms -s \"${subject}\" mihirgorecha@gmail.com");
}
function adminSms($sms) {
	system("echo ${sms} | nail eMailSMS+9886396753@dta.rr.nu");
}
function adminSmsExp($username, $category, $amount, $reason) {
	adminSms("Mobile ".
		 $username." ".
		 $category." ".
		 $amount." ".
		 escapeshellcmd(str_replace("\n",
					    " ",
					    str_replace("\r\n",
							" ",
							$reason))).
		 " !!!");
}

$args = $_GET['args'];
$type = strtok($args," ");
switch (strtoupper($type)) {
 case "ADDMOBILE":
	 echo(addMobile());
	 break;
 case "ADDEXP":
	 echo(addExp());
	 break;
 default:
	 echo("Usage: {ADDMOBILE|ADDEXP}");
	 break;
 }
?>
