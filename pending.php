<?php
	session_start();
	if(!isset($_SESSION['uname']) || $_SESSION['uname']=="") { //Check for session
		header("Location:index.php");
	}
	if($_SESSION['uname'] != "admin") {
		header("Location:logout.php");
	}
	
	if(isset($_POST['cancel'])) {
		header("Location:welcome.php");
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
		<!--
		
		
		function calcTotal() {
				var nameArray = new Array();
				var arrIndex = 0;
				var debugText = "";
				var flag = 0;
				
				var adjtable = document.getElementById("adjtable");
				var adjcells = adjtable.getElementsByTagName("td");
				
				debugText = debugText + "<br>" + nameArray.length;
				debugText = debugText + "<br>" + adjcells.length;
				
				for(var i = 0; i < adjcells.length; i++) {
					var namepass = new String(adjcells[i].getAttribute("id"));
					var adjname = namepass.substr(0,namepass.indexOf("|||"));
					var adjpass = namepass.substr(namepass.indexOf("|||")+3);
					var adjamt = adjcells[i].getAttribute("name");
					if(adjname != null && adjamt != null) {
						var j=0;
						for(j=0; j<arrIndex; j++) {
							if(nameArray[j][0] == adjname)
								break;
						}
						if(j<arrIndex) {
							nameArray[j][1] = parseInt(nameArray[j][1], 10) + parseInt(adjamt, 10);
						} else {
							nameArray[arrIndex] = new Array();
							nameArray[arrIndex][0] = adjname;
							nameArray[arrIndex][1] = parseInt(adjamt, 10);
							nameArray[arrIndex][2] = "";
							nameArray[arrIndex][3] = adjpass;
							arrIndex = arrIndex + 1;
						}
					}
				}
				
				debugText = debugText + "<br>" + nameArray.length;
				
				var amttotal = document.getElementById("amttotal");
				amttotal.innerHTML = "";
				
				var table = document.getElementById("datatable");
				if(table != null) {
					var lines = table.getElementsByTagName("tr");
					
					for (var i = 0; i < lines.length; i++) {
						var name = lines[i].getAttribute("id");
						var amt = lines[i].getAttribute("name");
						var cells = lines[i].getElementsByTagName("td");
						var inp = cells[0].getElementsByTagName("input");
						
						if(inp.length != 0 && inp[0].type == "checkbox" && inp[0].checked == true) {
							var j;
							for(j=0;j<arrIndex;j++) {
								if(nameArray[j][0] == name)
									break;
							}
							if(j<arrIndex) {
								nameArray[j][1] = parseInt(nameArray[j][1], 10) + parseInt(amt, 10);// - parseInt(amtEntered[0].value, 10);
							} else {
								nameArray[arrIndex] = new Array();
								nameArray[arrIndex][0] = name;
								nameArray[arrIndex][1] = amt;
								nameArray[arrIndex][3] = "xxx0password";
								arrIndex = arrIndex + 1;
							}
							nameArray[j][2] = nameArray[j][2] + " " + inp[0].value;
						}
					}
				}
				
				nameArray.sort();
				var tab = "";
				if(arrIndex>0) {
					tab = "<br /><center><form name='mainapprove' action='pending.php' method='post' onsubmit='return chkSubmit();'><table border='1' class='coll' cellpadding='5' width='330'><tr align='center' bgcolor='orange'><td><b>Name</b></td><td><b>Status</b></td><td align='right'><b>Amount</b></td><td align='center'><b>Pay/Recieve</b></td><td align='center'><b>Password</b></td></tr>";
					for(var i=0;i<arrIndex;i++) {
						var pay = "";
						var col = "";
						if(parseInt(nameArray[i][1], 10) < 0) {
							pay = "Recieve";
							col = "bgcolor='lightgreen'";
						} else if(parseInt(nameArray[i][1], 10) > 0) {
							pay = "Pay";
							col = "bgcolor='lightblue'";
						} else {
							pay = "";
							col = "bgcolor='#DDDDDD'";
						}
						tab = tab + "<tr " + col + "><td onClick='document.mainapprove.pass" + nameArray[i][0] + ".disabled=!document.mainapprove.pass" + nameArray[i][0] + ".disabled;document.mainapprove.amt" + nameArray[i][0] + ".disabled=!document.mainapprove.amt" + nameArray[i][0] + ".disabled;chkSubmit();' align='center'>" + nameArray[i][0] + "</td>";
						tab = tab + "<td align='center'>" + pay + "</td><td align='right'>" + Math.abs(parseInt(nameArray[i][1]), 10) + "</td>";
						tab = tab + "<input type='hidden' name='sub[]' value='" + nameArray[i][0] + "'/>";
						tab = tab + "<input type='hidden' name='selected" + nameArray[i][0] + "' value='" + nameArray[i][2] + "'/>";
						tab = tab + "<input type='hidden' name='total" + nameArray[i][0] + "' value='" + nameArray[i][1] + "'/>";
						tab = tab + "<td align='center'><input onKeyUp='chkSubmit();' type='text' size='6' maxlength='6' style='text-align:right' name='amt" + nameArray[i][0] + "' value='" + parseInt(nameArray[i][1], 10) + "' DISABLED /></td>";
						var inpid = "password" + nameArray[i][0] + "." + nameArray[i][3];
						var inpname = "pass" + nameArray[i][0];
						tab = tab + "<td align='center'><input onKeyUp='chkSubmit();' type='password' id='" + inpid + "' name='" + inpname + "' DISABLED /></td>";
						tab = tab + "</tr>";
					}
					tab = tab + "<tr><td align='right' colspan='4'><div id='mainAprvReason'><div></td><td align='left' colspan='1'><input id='mainAprvTotalIgnore' type='checkbox' onClick='chkSubmit();'/>Ignore total</td></tr>";
					tab = tab + "<tr><td align='center' colspan='5'><input type='submit' name='mainAprv' value='Approve'/></td></tr>";
					tab = tab + "</table></form></center>";
				}
				amttotal.innerHTML = tab;// + "<br>" + debugText;
				
				flag = 0;
				for(i=0; i < document.pendingdata.elements.length; i++) {
					if(document.pendingdata.elements[i].id == "chkbx") {
						if(document.pendingdata.elements[i].checked == true) {
							flag = 1;
							break;
						}
					}
				}
				if(flag==1) {
					document.getElementById("rejsel").disabled = false;
				}
				else {
					document.getElementById("rejsel").disabled = true;
				}
				chkSubmit();
			}
			
			function fillPassword(obj) {
				for(i=0; i < document.pendingdata.elements.length; i++) {
					if (document.pendingdata.elements[i].id == obj.id) {
						document.pendingdata.elements[i].value = obj.value;
					}
				}
			}
			
			function checkAll() {
				for(var i=0; i < document.pendingdata.elements.length; i++) {
					if (document.pendingdata.elements[i].type == "checkbox") {
						document.pendingdata.elements[i].checked = true;
					}
					if (document.pendingdata.elements[i].type == "password" || document.pendingdata.elements[i].type == "text") {
						document.pendingdata.elements[i].disabled = false;
					}
				}
				document.getElementById("rejsel").disabled = false;
				calcTotal();
			}
			
			function unCheckAll() {
				for(var i=0; i < document.pendingdata.elements.length; i++) {
					if (document.pendingdata.elements[i].type == "checkbox") {
						document.pendingdata.elements[i].checked = false;
					}
					if (document.pendingdata.elements[i].type == "password" || document.pendingdata.elements[i].type == "text") {
						document.pendingdata.elements[i].disabled = true;
					}
				}
				document.getElementById("rejsel").disabled = true;
				calcTotal();
			}
			
			function chkAndFill(obj) {
				if (obj.value.length == 0) {
					obj.value = 0;
				}
				calcTotal();
			}
function correctPassword(obj) {
	var passmd5 = obj.getAttribute("id").substr(obj.getAttribute("id").indexOf(".")+1); //password.abcd-md5
	var calcmd5 = calcMD5(obj.value);
	return (calcmd5 == passmd5);
}
function chkSubmit() {
	var i, total = 0, passwordcorrect = 0, passwordfail = 0, enabled = 0, amountincorrect = 0;
	var elms = document.mainapprove.getElementsByTagName("input");
	var reason = "", inpcol, inpsty;
	//	document.getElementById("debug").innerHTML = "";
	for (i=0; i<elms.length; i++) {
		inpcol = inpsty = "";
		if (!elms[i].getAttribute("name")) {
			continue;
		}
		if (elms[i].getAttribute("name").substr(0,3) == "amt") {
			if (!elms[i].disabled) {
				if (elms[i].value == null || !elms[i].value.toString().match(/^[-]?\d+$/)) {
					inpcol = "red";
					inpsty = "solid";
					amountincorrect += 1;
				} else {
					total += parseInt(elms[i].value, 10);
				}
				enabled += 1;
			}
			elms[i].style.borderColor = inpcol;
			elms[i].style.borderStyle = inpsty;
		}
		inpcol = inpsty = "";
		if (elms[i].getAttribute("name").substr(0,4) == "pass") {
			if (!elms[i].disabled) {
				if (correctPassword(elms[i])) {
					passwordcorrect += 1;
				} else {
					inpcol = "red";
					inpsty = "solid";
					passwordfail += 1;
				}
			}
			elms[i].style.borderColor = inpcol;
			elms[i].style.borderStyle = inpsty;
		}
	}
	reason += "<font color='" + (total==0?"green":"red") + "'>Total: Rs. " + total + "</font>";
	/*
	document.mainapprove.mainAprv.title  = "enabled = " + enabled;
	document.mainapprove.mainAprv.title += " | passwordcorrect = " + passwordcorrect;
	document.mainapprove.mainAprv.title += " | passwordfail = " + passwordfail;
	document.mainapprove.mainAprv.title += " | total = " + total;
	document.mainapprove.mainAprv.title += " | amountincorrect = " + amountincorrect;
	*/

	document.getElementById("mainAprvReason").innerHTML = reason;

	if (enabled == 0 ||
	    enabled != passwordcorrect ||
	    passwordfail != 0 ||
	    (total != 0 && !document.mainapprove.mainAprvTotalIgnore.checked) ||
	    amountincorrect != 0) {
		document.mainapprove.mainAprv.style.color = "red";
		return false;
	} else {
		document.mainapprove.mainAprv.style.color = "green";
		return true;
	}
	//	document.getElementById("debug").innerHTML += "enabled = " + enabled + " | passwordcorrect = " + passwordcorrect + " | passwordfail = " + passwordfail + " | total = " + total;//" " + elms[i].getAttribute("name");
}
/*
 * A JavaScript implementation of the RSA Data Security, Inc. MD5 Message
 * Digest Algorithm, as defined in RFC 1321.
 * Copyright (C) Paul Johnston 1999 - 2000.
 * Updated by Greg Holt 2000 - 2001.
 * See http://pajhome.org.uk/site/legal.html for details.
 */

/*
 * Convert a 32-bit number to a hex string with ls-byte first
 */
var hex_chr = "0123456789abcdef";
function rhex(num)
{
	str = "";
	for(j = 0; j <= 3; j++)
		str += hex_chr.charAt((num >> (j * 8 + 4)) & 0x0F) +
			hex_chr.charAt((num >> (j * 8)) & 0x0F);
	return str;
}

/*
 * Convert a string to a sequence of 16-word blocks, stored as an array.
 * Append padding bits and the length, as described in the MD5 standard.
 */
function str2blks_MD5(str)
{
	nblk = ((str.length + 8) >> 6) + 1;
	blks = new Array(nblk * 16);
	for(i = 0; i < nblk * 16; i++) blks[i] = 0;
	for(i = 0; i < str.length; i++)
		blks[i >> 2] |= str.charCodeAt(i) << ((i % 4) * 8);
	blks[i >> 2] |= 0x80 << ((i % 4) * 8);
	blks[nblk * 16 - 2] = str.length * 8;
	return blks;
}

/*
 * Add integers, wrapping at 2^32. This uses 16-bit operations internally 
 * to work around bugs in some JS interpreters.
 */
function add(x, y)
{
	var lsw = (x & 0xFFFF) + (y & 0xFFFF);
	var msw = (x >> 16) + (y >> 16) + (lsw >> 16);
	return (msw << 16) | (lsw & 0xFFFF);
}

/*
 * Bitwise rotate a 32-bit number to the left
 */
function rol(num, cnt)
{
	return (num << cnt) | (num >>> (32 - cnt));
}

/*
 * These functions implement the basic operation for each round of the
 * algorithm.
 */
function cmn(q, a, b, x, s, t)
{
	return add(rol(add(add(a, q), add(x, t)), s), b);
}
function ff(a, b, c, d, x, s, t)
{
	return cmn((b & c) | ((~b) & d), a, b, x, s, t);
}
function gg(a, b, c, d, x, s, t)
{
	return cmn((b & d) | (c & (~d)), a, b, x, s, t);
}
function hh(a, b, c, d, x, s, t)
{
	return cmn(b ^ c ^ d, a, b, x, s, t);
}
function ii(a, b, c, d, x, s, t)
{
	return cmn(c ^ (b | (~d)), a, b, x, s, t);
}

/*
 * Take a string and return the hex representation of its MD5.
 */
function calcMD5(str)
{
	x = str2blks_MD5(str);
	a =  1732584193;
	b = -271733879;
	c = -1732584194;
	d =  271733878;

	for(i = 0; i < x.length; i += 16)
		{
			olda = a;
			oldb = b;
			oldc = c;
			oldd = d;

			a = ff(a, b, c, d, x[i+ 0], 7 , -680876936);
			d = ff(d, a, b, c, x[i+ 1], 12, -389564586);
			c = ff(c, d, a, b, x[i+ 2], 17,  606105819);
			b = ff(b, c, d, a, x[i+ 3], 22, -1044525330);
			a = ff(a, b, c, d, x[i+ 4], 7 , -176418897);
			d = ff(d, a, b, c, x[i+ 5], 12,  1200080426);
			c = ff(c, d, a, b, x[i+ 6], 17, -1473231341);
			b = ff(b, c, d, a, x[i+ 7], 22, -45705983);
			a = ff(a, b, c, d, x[i+ 8], 7 ,  1770035416);
			d = ff(d, a, b, c, x[i+ 9], 12, -1958414417);
			c = ff(c, d, a, b, x[i+10], 17, -42063);
			b = ff(b, c, d, a, x[i+11], 22, -1990404162);
			a = ff(a, b, c, d, x[i+12], 7 ,  1804603682);
			d = ff(d, a, b, c, x[i+13], 12, -40341101);
			c = ff(c, d, a, b, x[i+14], 17, -1502002290);
			b = ff(b, c, d, a, x[i+15], 22,  1236535329);    

			a = gg(a, b, c, d, x[i+ 1], 5 , -165796510);
			d = gg(d, a, b, c, x[i+ 6], 9 , -1069501632);
			c = gg(c, d, a, b, x[i+11], 14,  643717713);
			b = gg(b, c, d, a, x[i+ 0], 20, -373897302);
			a = gg(a, b, c, d, x[i+ 5], 5 , -701558691);
			d = gg(d, a, b, c, x[i+10], 9 ,  38016083);
			c = gg(c, d, a, b, x[i+15], 14, -660478335);
			b = gg(b, c, d, a, x[i+ 4], 20, -405537848);
			a = gg(a, b, c, d, x[i+ 9], 5 ,  568446438);
			d = gg(d, a, b, c, x[i+14], 9 , -1019803690);
			c = gg(c, d, a, b, x[i+ 3], 14, -187363961);
			b = gg(b, c, d, a, x[i+ 8], 20,  1163531501);
			a = gg(a, b, c, d, x[i+13], 5 , -1444681467);
			d = gg(d, a, b, c, x[i+ 2], 9 , -51403784);
			c = gg(c, d, a, b, x[i+ 7], 14,  1735328473);
			b = gg(b, c, d, a, x[i+12], 20, -1926607734);
    
			a = hh(a, b, c, d, x[i+ 5], 4 , -378558);
			d = hh(d, a, b, c, x[i+ 8], 11, -2022574463);
			c = hh(c, d, a, b, x[i+11], 16,  1839030562);
			b = hh(b, c, d, a, x[i+14], 23, -35309556);
			a = hh(a, b, c, d, x[i+ 1], 4 , -1530992060);
			d = hh(d, a, b, c, x[i+ 4], 11,  1272893353);
			c = hh(c, d, a, b, x[i+ 7], 16, -155497632);
			b = hh(b, c, d, a, x[i+10], 23, -1094730640);
			a = hh(a, b, c, d, x[i+13], 4 ,  681279174);
			d = hh(d, a, b, c, x[i+ 0], 11, -358537222);
			c = hh(c, d, a, b, x[i+ 3], 16, -722521979);
			b = hh(b, c, d, a, x[i+ 6], 23,  76029189);
			a = hh(a, b, c, d, x[i+ 9], 4 , -640364487);
			d = hh(d, a, b, c, x[i+12], 11, -421815835);
			c = hh(c, d, a, b, x[i+15], 16,  530742520);
			b = hh(b, c, d, a, x[i+ 2], 23, -995338651);

			a = ii(a, b, c, d, x[i+ 0], 6 , -198630844);
			d = ii(d, a, b, c, x[i+ 7], 10,  1126891415);
			c = ii(c, d, a, b, x[i+14], 15, -1416354905);
			b = ii(b, c, d, a, x[i+ 5], 21, -57434055);
			a = ii(a, b, c, d, x[i+12], 6 ,  1700485571);
			d = ii(d, a, b, c, x[i+ 3], 10, -1894986606);
			c = ii(c, d, a, b, x[i+10], 15, -1051523);
			b = ii(b, c, d, a, x[i+ 1], 21, -2054922799);
			a = ii(a, b, c, d, x[i+ 8], 6 ,  1873313359);
			d = ii(d, a, b, c, x[i+15], 10, -30611744);
			c = ii(c, d, a, b, x[i+ 6], 15, -1560198380);
			b = ii(b, c, d, a, x[i+13], 21,  1309151649);
			a = ii(a, b, c, d, x[i+ 4], 6 , -145523070);
			d = ii(d, a, b, c, x[i+11], 10, -1120210379);
			c = ii(c, d, a, b, x[i+ 2], 15,  718787259);
			b = ii(b, c, d, a, x[i+ 9], 21, -343485551);

			a = add(a, olda);
			b = add(b, oldb);
			c = add(c, oldc);
			d = add(d, oldd);
		}
	return rhex(a) + rhex(b) + rhex(c) + rhex(d);
}

		//-->
		</script>
	</head>
	<body bgcolor="#EEEEEE" onLoad="calcTotal();">
		<table border="0" width="100%" height="100%">
		<tr>
			<td>
			<?php
			
			//Connect to Database
			require("login.php");
			mysql_select_db("home",$con);
			
			$names_arr = NULL;
			$names = "";
			
			if(isset($_POST['mainAprv'])) {
				foreach($_POST['sub'] as $user) {
					$var_total    = "total".$user;
					$var_selected = "selected".$user;
					$var_amount   = "amt".$user;
					$var_password = "pass".$user;
					if(isset($_POST[$var_selected]) && isset($_POST[$var_amount]) && isset($_POST[$var_password])) {
						$total    = trim($_POST[$var_total]);
						$selected = trim($_POST[$var_selected]);
						$amount   = trim($_POST[$var_amount]);
						$password = trim($_POST[$var_password]);
						if($password != "" && (filter_var($amount, FILTER_VALIDATE_INT) || $amount=="0")) {
							$expno = explode(" ",$selected);
							//print_r($expno);//."<br>");
							$query = "SELECT password FROM userpass WHERE username='$user'";
							$rcSet = mysql_query($query,$con);
							$row = mysql_fetch_array($rcSet);
							//echo($user." - ");
							if($row['password'] == md5($password)) {
								$diff = $total - $amount;
								foreach($expno as $e) {
									$e = trim($e);
									$query = "UPDATE common SET approved='1' WHERE expno='$e'";
									mysql_query($query,$con);
								}
								$query = "UPDATE userpass SET adjustments='$diff' WHERE username='$user'";
								mysql_query($query,$con);
							} else {
								$names_arr[$user] = $user;
							}
							//echo("<br>");
						} else {
							$names_arr[$user] = $user;
						}
					}
				}
				
				if($names_arr != NULL) {
					foreach($names_arr as $n) {
						$names .= "<u>".$n."</u>&nbsp;&nbsp;"; 
					}
				}
			}
			if(isset($_POST['rejSel'])) {
				foreach ($_POST['app'] as $aprv) {
					$query = "SELECT username FROM common WHERE expno='$aprv'";
					$rcSet = mysql_query($query,$con);
					$row = mysql_fetch_array($rcSet);
					if($row['username'] == "common") {
						$query = "DELETE FROM common WHERE expno='$aprv'";
						mysql_query($query,$con);
					}
					else {
						$query = "UPDATE common SET approved='20' WHERE expno='$aprv'";
						mysql_query($query,$con);
					}
				}
			}
			
			if(isset($_POST['srt'])) {
				$orderby = $_POST['srt'];
				$order = $_POST['ascdsc'];
			}
			else {			
				$orderby = "expno";
				$order = "DESC";
			}
			mysql_close($con);
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
			if($names != "") {
				echo ("<br><center><font color='red'>Invalid Password Entered for: <b>".$names."</b></font></center>");
			}
//echo("<div id='debug'></div><br />");
			require("login.php");
			mysql_select_db("home", $con);
			$rcSet = mysql_query("SELECT * FROM common WHERE approved='0' ORDER BY ".$orderby." ".$order, $con);
			?>
				<div id="amttotal">
				</div>
				<br>				
			<?php
			//For Adjustments
			$rcSet2 = mysql_query("SELECT username, password, adjustments FROM userpass WHERE enabled='1' ORDER BY username", $con);
			$noOfRows = mysql_num_rows($rcSet2);
			if ($noOfRows < 4) {
				$colSpn = (($noOfRows % 4)*2);
			}
			else {
				$colSpn = 8;
			}
			$tdwidth = (100/$colSpn)."%";
			if (($common_adj-$_SESSION['common']) < 0) {
				$adj_amt_total = "Recieve&nbsp;Rs.&nbsp;".abs($common_adj-$_SESSION['common']);
			}
			else {
				$adj_amt_total = "Pay&nbsp;Rs.&nbsp;".abs($common_adj-$_SESSION['common']);
			}
			echo("
				<table id='adjtable' class='coll' border='1' align='center' width='80%' cellpadding='5'>
				<tr>
					<td align='left' colspan='".$colSpn."' bgcolor='orange'><b>Adjustment Amount (".$adj_amt_total." in Total)</b></td>
				</tr>");
				
			$cntr = 0;
			while($row2 = mysql_fetch_array($rcSet2)) {
				if($cntr == 0) {
					echo("<tr>");
				}
				if ($row2['adjustments'] > 0) {
					$fntclr = "lightblue";
					$payrec = "Pay&nbsp;Rs.&nbsp;";
				}
				else if($row2['adjustments'] < 0) {
					$fntclr = "lightgreen";
					$payrec = "Recieve&nbsp;Rs.&nbsp;";
				}
				else {
					$fntclr = "#DDDDDD";
					$payrec = "";
				}
				echo("<td width='".$tdwidth."' bgcolor='".$fntclr."'><b>".$row2['username']."</b></td>");
				echo("<td width='".$tdwidth."' id='".$row2['username']."|||".$row2['password']."' name='".$row2['adjustments']."' align='right' bgcolor='".$fntclr."'>".$payrec.abs($row2['adjustments'])."</td>");
				$cntr++;
				if($cntr == 4) {
					echo("</tr>");
					$cntr = 0;
				}
			}
			echo("
				</table>
			");
			//End of For Adjustments
			if(mysql_num_rows($rcSet) > 0) {			
			echo("<br><br><table id='datatable' class='coll' border='1' align='center' width='80%' cellpadding='5'>");
			
			echo("<tr><td colspan='7'><form action='pending.php' method='post'>
				<b>Sort Data By:</b>&nbsp;&nbsp;&nbsp;
				<select name='srt'>
					<option value='username'>Name</option>
					<option value='amount'>Amount</option>
					<option value='dateadded'>Date</option>
					<option value='reason'>Reason</option>
					<option value='expno'>Expense</option>
				</select>&nbsp;&nbsp;&nbsp;
				<select name='ascdsc'>
					<option value='ASC'>Ascending Order</option>
					<option value='DESC'>Descending Order</option>
				</select>&nbsp;&nbsp;&nbsp;
				<input type='submit' name='sort' value='Go' />
			</form></td></tr>");
						
			echo("<form name='pendingdata' action='pending.php' method='post'><tr>");
				echo("<td align='center'><b>Select</b><br><a href='javascript:void(0);' onClick='checkAll();'>All</a>&nbsp;/&nbsp;<a href='javascript:void(0);' onClick='unCheckAll();'>None</a></td>");
				echo("<td align='center'><b>Username</b></td>");
				echo("<td align='center'><b>Credit (Rs.)</b></td>");
				echo("<td align='center'><b>Debit (Rs.)</b></td>");
				echo("<td align='center'><b>Date (YYYY-MM-DD)</b></td>");
				echo("<td width='100%'><b>Reason</b></td>");
				echo("<td align='center'><b>Category</b></td>");
				//echo("<td align='center'><b>Transaction Amount</b></td>");
				//echo("<td align='center'><b>Password for Approval</b></td>");
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
				echo("<tr".$col." id='$row[username]' name='$row[amount]'>");
					echo("<td align='center'><input type='checkbox' id='chkbx' name='app[]' value='$row[expno]' onClick='calcTotal();'/></td>");
					echo("<td align='center'>".$row['username']."</td>");
					if($row['amount'] > 0) {
						echo("<td align='right'>".abs($row['amount'])."</td>");
						echo("<td align='center'>*</td>");
						$credit += $row['amount'];
					}
					else {
						echo("<td align='center'>*</td>");
						echo("<td align='right'>".abs($row['amount'])."</td>");
						$debit += $row['amount'];
					}
					echo("<td align='center'>".$row['dateadded']."</td>");
					echo("<td width='100%'>".$row['reason']."</td>");
					echo("<td align='center'>".$row['category']."</td>");
					//echo("<td align='center'><input type='text' name='appamt".$row['expno']."' id='".$row['username']."' value='".$row['amount']."' size='5' maxlength='5' style='text-align:right' onKeyUp='calcTotal();' onBlur='chkAndFill(this);' DISABLED /></td>");
					//echo("<td><input type='password' name='pass".$row['expno']."' id='".$row['username']."' onKeyUp='fillPassword(this);' DISABLED /></td>");
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
						<td align='center'><b>*</b></td>
					</tr>");
					
			echo("<tr><td colspan='7' align='center'><input type='submit' id='rejsel' name='rejSel' value='Reject Selected' DISABLED />&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type='submit' name='cancel' value='Done' /></td></tr></form>");
			echo("</form></table>");
			}
			else {
				echo("<center><br><br><br><br><b>No Approvals Pending</b><br><br><a href='welcome.php'>OK</a></center>");
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