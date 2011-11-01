<?php
	session_start();
	
	include "LC/classes/libchart.php";

	require("../login.php");
	mysql_select_db("home", $con);
	$dataArray = array();
	
	if(!isset($_GET['month']))
		$month = date("m");
	else
		$month = $_GET['month'];
		
	if(!isset($_GET['year']))
		$year = date("Y");
	else
		$year = $_GET['year'];
		
	$noOfDays = cal_days_in_month(CAL_GREGORIAN,$month,$year);
		
	for($i=1;$i<=$noOfDays;$i++) {
		$sampDate = date("Y-m-d", mktime(0,0,0,$month,$i,$year));
		$dataArray[$sampDate] = 0;
	}
	
	$d = date("Y-m",mktime(0,0,0,$month,1,$year))."%";
	$query = "SELECT SUM(amount), dateadded FROM common WHERE dateadded LIKE '$d' AND amount>'0' AND approved='1' GROUP BY dateadded";
	$rcSet = mysql_query($query, $con);
		
	while ($row = mysql_fetch_assoc($rcSet)) {
		$dt = $row['dateadded'];
		if ($row['SUM(amount)'] == NULL) {
			$amt = 0;
		}
		else {
			$amt = abs($row['SUM(amount)']);
		}
		$dataArray[$dt] = $amt;
	}
	
	mysql_close($con);
	
	$chart = new VerticalBarChart($_SESSION['width'], $_SESSION['height']);

	$dataSet = new XYDataSet();
	foreach($dataArray as $key => $val) {
		$dataSet->addPoint(new Point($key, $val));
	}
	$chart->setDataSet($dataSet);

	$chart->setTitle("Daily Statistics for HomeSoft v1.0 (".date("M/Y",mktime(0,0,0,$month,1,$year)).")");
	$chart->render("dailyChart.png");
?>

<html>
	<head>
		<title>Daily Graph for HomeSoft v1.0</title>
		<link rel="stylesheet" type="text/css" href="../design.css" />
	</head>
	<body bgcolor="#EEEEEE">
		<?php
			require("../mainBar.php");
		?>
		<br>
		<table cellpadding="3" align="center" border="0">
		<tr align="center">
			<td colspan="3"><b>Timeline</b><br>
				<?php
					for($i=5; $i>=1; $i--) {
						$p = mktime(0,0,0,$month-$i,1,$year);
						echo("&nbsp;&nbsp;<a href='dailyGraph.php?month=".date("m", $p)."&year=".date("Y", $p)."'>".date("M/Y", $p)."</a>&nbsp;&nbsp;");
					}
					for($i=0; $i<=5; $i++) {
						$n = mktime(0,0,0,$month+$i,1,$year);
						if($i==0) {
							echo("&nbsp;&nbsp;<a href='dailyGraph.php?month=".date("m", $n)."&year=".date("Y", $n)."'><b>::&nbsp;".date("M/Y", $n)."&nbsp;::</b></a>&nbsp;&nbsp;");
						}
						else {
							echo("&nbsp;&nbsp;<a href='dailyGraph.php?month=".date("m", $n)."&year=".date("Y", $n)."'>".date("M/Y", $n)."</a>&nbsp;&nbsp;");
						}
					}
				?>
				<br><br>
			</td>
		</tr>
		<tr align="center" valign="center">
		<td align='left' width="33%">
		<?php
			$pre = mktime(0,0,0,$month-1,1,$year);
			echo("<a href='dailyGraph.php?month=".date("m",$pre)."&year=".date("Y",$pre)."'>Previous Month : ".date("M/Y",$pre)."</a>");
		?>
		</td>
		<td align='center' width="33%">
		<?php
			echo("<a href='dailyGraph.php?month=".date("m")."&year=".date("Y")."'>Current Month : ".date("M/Y")."</a>");
		?>
		</td>
		<td align='right' width="33%">
		<?php
			$nxt = mktime(0,0,0,$month+1,1,$year);
			echo("<a href='dailyGraph.php?month=".date("m",$nxt)."&year=".date("Y",$nxt)."'>Next Month : ".date("M/Y",$nxt)."</a>");
		?>
		</td>
		</tr>
		<tr align="center" valign="center">
			<td align="center" colspan="3"><img src="dailyChart.png" /></td>
		</tr>
		</table>
			
	</body>
</html>
