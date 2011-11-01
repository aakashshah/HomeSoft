<?php
	session_start();
	
	include "LC/classes/libchart.php";
	
	function getWeek($f_month,$f_year) {
		return date("W", mktime(0,0,0,$f_month,15,$f_year));
	}
	function getStartDate($f_month, $f_year, $f_weeksDisp) {
		$week = getWeek($f_month,$f_year);
		$lastWeekLastYear = getLastWeek($f_year-1);
		$lastWeekCurrYear = getLastWeek($f_year);
		$firstWeekDisp = $week-$f_weeksDisp;
		$firstWeekDisp = ($firstWeekDisp<=0?$lastWeekLastYear+$firstWeekDisp:$firstWeekDisp);
		$startDate = mktime(0,0,0,$f_month,(15-(($f_weeksDisp+2)*7)),$f_year);
		while(1) {
			if(($firstWeekDisp) == date("W",mktime(0,0,$startDate,1,1,1970))) {
				break;
			}
			$startDate += (24*60*60);
		}
		return $startDate;
	}
	function getEndDate($f_startDate, $f_weeksDisp) {
		return ($f_startDate+((((($f_weeksDisp*2)+1)*7)-1)*24*60*60));
	}
	function getLastWeek($f_year) {
		$max = 0;
		for($i=20;$i<=31;$i++) {
			$tmp = date("W",mktime(0,0,0,12,$i,$f_year));
			if($tmp>$max)
				$max = $tmp;
		}
		return $max;
	}
	
	require("../login.php");
	mysql_select_db("home", $con);
	$dataArray = array();
	$dateArray = array();
	
	if(!isset($_GET['month']))
		$month = date("m");
	else
		$month = $_GET['month'];
		
	if(!isset($_GET['year']))
		$year = date("Y");
	else
		$year = $_GET['year'];
		
	$week = getWeek($month,$year);
	$lastWeekLastYear = getLastWeek($year-1);
	$lastWeekCurrYear = getLastWeek($year);
	$noOfWeeksDisp = 8;
	
	$startDate = getStartDate($month, $year, $noOfWeeksDisp);
	
	for($i=$week-$noOfWeeksDisp,$j=0;$i<=$week+$noOfWeeksDisp;$i++,$j++) {
		if($i<=0) {
			$index = $lastWeekLastYear+$i;
		} else if($i>$lastWeekCurrYear) {
			$index = $i-$lastWeekCurrYear;
		} else {
			$index = $i;
		}
		$dataArray[$index] = 0;
		$sDay = $startDate+($j*7*24*60*60);
		$eDay = $startDate+((($j*7)+6)*24*60*60);
		$dateArray[$index] = date("d-M",$sDay)."\n".date("d-M",$eDay);
		//$dateArray[$index] = date("W",$sDay)." ".date("d-M",$sDay)."\n".date("d-M",$eDay);
	}
	$rangeFirDay = date("d-M-Y",$startDate);
	$rangeLasDay = date("d-M-Y",getEndDate($startDate,$noOfWeeksDisp));//$startDate+((((($noOfWeeksDisp*2)+1)*7)-1)*24*60*60));
	
	$d1 = date("Y-m",mktime(0,0,0,$month,1,$year))."%";
	$d2 = date("Y-m",mktime(0,0,0,$month+1,1,$year))."%";
	$d3 = date("Y-m",mktime(0,0,0,$month-1,1,$year))."%";
	$d4 = date("Y-m",mktime(0,0,0,$month+2,1,$year))."%";
	$d5 = date("Y-m",mktime(0,0,0,$month-2,1,$year))."%";
	$query = "SELECT SUM(amount), dateadded FROM common WHERE (dateadded LIKE '$d1' OR dateadded LIKE '$d2' OR dateadded LIKE '$d3' OR dateadded LIKE '$d4' OR dateadded LIKE '$d5') AND amount>'0' AND approved='1' GROUP BY dateadded";
	$rcSet = mysql_query($query, $con);
		
	while ($row = mysql_fetch_assoc($rcSet)) {
		$dt = $row['dateadded'];
		$rcDate = substr($dt, 8, 2);
		$rcMonth = substr($dt, 5, 2);
		$rcYear = substr($dt, 0, 4);
		
		$rcWeek = date("W", mktime(0, 0, 0, $rcMonth, $rcDate, $rcYear));
		$amt = $row['SUM(amount)'];
		if(!isset($dataArray[$rcWeek]))
			$dataArray[$rcWeek] = 0;
		$dataArray[$rcWeek] += $amt;
	}
	
	mysql_close($con);
	
	$chart = new VerticalBarChart($_SESSION['width'], $_SESSION['height']);

	$dataSet = new XYDataSet();
	foreach($dateArray as $key => $val) {
		$dataSet->addPoint(new Point($val, $dataArray[$key]));
	}
	$chart->setDataSet($dataSet);

	$chart->setTitle("Weekly Statistics for HomeSoft v1.0 (".$rangeFirDay." to ".$rangeLasDay.")");
	$chart->render("weeklyChart.png");
?>

<html>
	<head>
		<title>Weekly Graph for HomeSoft v1.0</title>
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
						echo("&nbsp;&nbsp;<a href='weeklyGraph.php?month=".date("m", $p)."&year=".date("Y", $p)."'>".date("M/Y", $p)."</a>&nbsp;&nbsp;");
					}
					for($i=0; $i<=5; $i++) {
						$n = mktime(0,0,0,$month+$i,1,$year);
						if($i==0) {
							echo("&nbsp;&nbsp;<a href='weeklyGraph.php?month=".date("m", $n)."&year=".date("Y", $n)."'><b>::&nbsp;".date("M/Y", $n)."&nbsp;::</b></a>&nbsp;&nbsp;");
						}
						else {
							echo("&nbsp;&nbsp;<a href='weeklyGraph.php?month=".date("m", $n)."&year=".date("Y", $n)."'>".date("M/Y", $n)."</a>&nbsp;&nbsp;");
						}
					}
				?>
				<br><br>
			</td>
		</tr>
		<tr align="center" valign="center">
		<td align='left' width="33%">
		<?php
			$pre = mktime(0,0,0,$month-1,date("d"),$year);
			$sd = getStartDate($month-1, $year, $noOfWeeksDisp);
			$ed = getEndDate($sd, $noOfWeeksDisp);
			echo("<a href='weeklyGraph.php?month=".date("m",$pre)."&year=".date("Y",$pre)."'>Previous Weeks (".date("d-M-Y",$sd)." - ".date("d-M-Y",$ed).")</a>");
		?>
		</td>
		<td align='center' width="33%">
		<?php
			$sd = getStartDate(date("m"), date("Y"), $noOfWeeksDisp);
			$ed = getEndDate($sd, $noOfWeeksDisp);
			echo("<a href='weeklyGraph.php?month=".date("m")."&year=".date("Y")."'>Current Weeks (".date("d-M-Y",$sd)." - ".date("d-M-Y",$ed).")</a>");
		?>
		</td>
		<td align='right' width="33%">
		<?php
			$pre = mktime(0,0,0,$month+1,date("d"),$year);
			$sd = getStartDate($month+1, $year, $noOfWeeksDisp);
			$ed = getEndDate($sd, $noOfWeeksDisp);
			echo("<a href='weeklyGraph.php?month=".date("m",$pre)."&year=".date("Y",$pre)."'>Next Weeks (".date("d-M-Y",$sd)." - ".date("d-M-Y",$ed).")</a>");
		?>
		</td>
		</tr>
		<tr align="center" valign="center">
			<td align="center" colspan="3"><img src="weeklyChart.png" /></td>
		</tr>
		</table>
			
	</body>
</html>
