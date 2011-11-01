<?php
	session_start();
	if(!isset($_SESSION['uname']) || $_SESSION['uname']=="") { //Check for session
		header("Location:index.php");
	}
	if($_SESSION['uname'] != "admin") {
		header("Location:logout.php");
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
				var nameArray = new Array(2);
				var arrIndex = 0;
				
				var table = document.getElementById("datatable");
				var lines = table.getElementsByTagName("tr");
				
				var amttotal = document.getElementById("amttotal");
				amttotal.innerHTML = "";
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
							break;//nameArray[j][1] = parseInt(nameArray[j][1]) + parseInt(amt);// - parseInt(amtEntered[0].value);
						} else {
							nameArray[arrIndex] = new Array();
							nameArray[arrIndex][0] = name;
							nameArray[arrIndex][1] = 0;//amt;
							arrIndex = arrIndex + 1;
						}
					}
				}
				
				var adjtable = document.getElementById("adjtable");
				var adjcells = adjtable.getElementsByTagName("td");
				
				for(var i = 0; i < adjcells.length; i++) {
					var adjname = adjcells[i].getAttribute("id");
					var adjamt = adjcells[i].getAttribute("name");
					
					for(var j=0; j<arrIndex; j++) {
						if(nameArray[j][0] == adjname)
							break;
					}
					if(j<arrIndex)
						nameArray[j][1] = parseInt(nameArray[j][1]) + parseInt(adjamt);
				}
				
				var txttab = document.getElementsByTagName("input");
				for (var x = 0; x < txttab.length; x++) {
					if(txttab[x].type == "text" && txttab[x].disabled == false && txttab[x].value.length != 0) {
						var usrname = txttab[x].getAttribute("id");
						var amtEntered = txttab[x].value;
						
						//document.write(usrname + " " + amtEntered);
						
						for (var y=0; y<arrIndex; y++) {
							if(nameArray[y][0] == usrname) {
								break;
							}
						}
						if(y<arrIndex) {
							nameArray[y][1] = parseInt(nameArray[y][1]) + parseInt(amtEntered);
						}
					}
				}
				
				nameArray.sort();
				var tab = "";
				if(arrIndex>0) {
					tab = "<br /><center><table border='1' class='coll' cellpadding='5' width='330'><tr align='center' bgcolor='coral'><td><b>Name</b></td><td><b>Status</b></td><td align='right'><b>Amount</b></td></tr>";
					for(var i=0;i<arrIndex;i++) {
						var pay = "";
						var col = "";
						if(parseInt(nameArray[i][1])<0) {
							pay = "Recieve";
							col = "bgcolor='lightgreen'";
						} else if(parseInt(nameArray[i][1])>0) {
							pay = "Pay";
							col = "bgcolor='lightblue'";
						} else {
							pay = "";
							col = "";
						}
						tab = tab + "<tr " + col + "><td align='center'>" + nameArray[i][0] + "</td>";
						tab = tab + "<td align='center'>" + pay + "</td><td align='right'>" + Math.abs(parseInt(nameArray[i][1])) + "</td></tr>";					
					}
					tab = tab + "</table></center>";
				}
				amttotal.innerHTML = tab;
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
				calcTotal();
			}
			
			function chkAndFill(obj) {
				if (obj.value.length == 0) {
					obj.value = 0;
				}
				calcTotal();
			}
		//-->
		</script>
	</head>
	<body bgcolor="#EEEEEE">
		<table border="0" width="100%" height="100%">
		<tr>
			<td>
			<?php		
			//Connect to Database
			require("login.php");
			mysql_select_db("home",$con);
			
			$names_arr = NULL;
			$names = "";
			if(isset($_POST['aprvSel']) && isset($_POST['app'])) {
				foreach ($_POST['app'] as $aprv) {
					$query = "SELECT username, amount FROM common WHERE expno='$aprv'";
					$rcSet = mysql_query($query, $con);
					$row = mysql_fetch_array($rcSet);
					
					$unam = $row['username'];
					$amt = $row['amount'];
					
					$passval = "pass".$aprv;
					$pass = md5($_POST[$passval]);
										
					$query = "SELECT password, adjustments FROM userpass WHERE username='$unam'";
					$rcSet = mysql_query($query, $con);
					$row = mysql_fetch_array($rcSet);
					
					$amt += $row['adjustments'];
					$appamtval = "appamt".$aprv;
					$amtDiff = $amt - $_POST[$appamtval];
					
					if($row['password'] == $pass) {
						$query = "UPDATE userpass SET adjustments='$amtDiff' WHERE username='$unam'";
						mysql_query($query, $con);
						$query = "UPDATE common SET approved='1' WHERE expno='$aprv'";
						mysql_query($query,$con);
					}
					else {
						$names_arr[$unam] = $unam;
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
				header("Location:pending.php");
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
			require("login.php");
			mysql_select_db("home", $con);
			$rcSet = mysql_query("SELECT * FROM common WHERE approved='0' ORDER BY ".$orderby." ".$order, $con);
			if(mysql_num_rows($rcSet) > 0) {
			?>
			<div id="amttotal">
			</div>
			<br>
			<?php
			
			//For Adjustments
			$rcSet2 = mysql_query("SELECT username, adjustments FROM userpass ORDER BY username", $con);
			$noOfRows = mysql_num_rows($rcSet2);
			if ($noOfRows < 4) {
				$colSpn = (($noOfRows % 4)*2);
			}
			else {
				$colSpn = 8;
			}
			echo("
				<table id='adjtable' class='coll' border='1' align='center' width='90%' cellpadding='5'>
				<tr>
					<td align='left' colspan='".$colSpn."' bgcolor='orange'><b>Adjustment Amount (Rs.)</b></td>
				</tr>");
				
			$cntr = 0;
			while($row2 = mysql_fetch_array($rcSet2)) {
				if($cntr == 0) {
					echo("<tr>");
				}
				if ($row2['adjustments'] > 0) {
					$fntclr = "lightgreen";
				}
				else if($row2['adjustments'] < 0) {
					$fntclr = "coral";
				}
				else {
					$fntclr = "lightblue";
				}
				echo("<td bgcolor='".$fntclr."'><b>".$row2['username']."</b></td>");
				echo("<td id='".$row2['username']."' name='".$row2['adjustments']."' align='right' bgcolor='".$fntclr."'>".$row2['adjustments']."</td>");
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
			
			echo("<br><br><table id='datatable' class='coll' border='1' align='center' width='90%' cellpadding='5'>");
			
			echo("<tr><td colspan='9'><form action='pending.php' method='post'>
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
						
			echo("<form name='pendingdata' action='pending.php' method='post'><tr>");
				echo("<td align='center'><b>Select</b><br><a href='javascript:void(0);' onClick='checkAll();'>All</a>&nbsp;/&nbsp;<a href='javascript:void(0);' onClick='unCheckAll();'>None</a></td>");
				echo("<td align='center'><b>Username</b></td>");
				echo("<td align='center'><b>Credit (Rs.)</b></td>");
				echo("<td align='center'><b>Debit (Rs.)</b></td>");
				echo("<td align='center'><b>Date (YYYY-MM-DD)</b></td>");
				echo("<td width='100%'><b>Reason</b></td>");
				echo("<td align='center'><b>Category</b></td>");
				echo("<td align='center'><b>Transaction Amount</b></td>");
				echo("<td align='center'><b>Password for Approval</b></td>");
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
					echo("<td align='center'><input type='checkbox' id='chkbx' name='app[]' value='$row[expno]' onClick='document.pendingdata.pass".$row['expno'].".disabled = !this.checked; document.pendingdata.appamt".$row['expno'].".disabled = !this.checked; calcTotal();'/></td>");
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
					echo("<td align='center'><input type='text' name='appamt".$row['expno']."' id='".$row['username']."' value='".$row['amount']."' size='5' maxlength='5' style='text-align:right' onKeyUp='calcTotal();' onBlur='chkAndFill(this);' DISABLED /></td>");
					echo("<td><input type='password' name='pass".$row['expno']."' id='".$row['username']."' onKeyUp='fillPassword(this);' DISABLED /></td>");
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
						<td align='center'><b>*</b></td>
						<td align='center'><b>*</b></td>
					</tr>");
					
			echo("<tr><td colspan='9' align='center'><input type='submit' name='aprvSel' value='Approve Selected' />&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type='submit' name='rejSel' value='Reject Selected' />&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type='submit' name='cancel' value='Done' /></td></tr></form>");
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