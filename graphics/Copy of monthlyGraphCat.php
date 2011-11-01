<?php
	session_start();
	
	function getMonth($m=0) {
		return (($m==0 ) ? date("M") : date("M", mktime(0,0,0,$m)));
	}
	
	include "LC/classes/libchart.php";

	require("../login.php");
	mysql_select_db("home", $con);
	
	$dataArray = array();
	
	if(!isset($_GET['year']))
		$year = date("Y");
	else
		$year = $_GET['year'];
		
	$noOfMonths = 12;
		
	// for($i=1;$i<=$noOfMonths;$i++) {
		// if ($i<10) {
			// $i = "0".$i;
		// }
		// $dataArray[$i] = 0;
	// }
	
	$d = date("Y",mktime(0,0,0,1,1,$year))."%";
	$query = "SELECT SUM(amount), dateadded, category FROM common WHERE dateadded LIKE '$d' AND amount>'0' AND approved='1' GROUP BY dateadded";
	$rcSet = mysql_query($query, $con);
		
	while ($row = mysql_fetch_assoc($rcSet)) {
		// $mnth = substr($row['dateadded'], 5, 2); //2007-09-29
		// $cat = $row['category'];
		// if($dataArray[$cat] == NULL) {
			// $dataArray[$cat] = array();
		// }
		// $catArray = $dataArray[$cat];
		// if($catArray[$mnth] == NULL) {
			// $catArray[$mnth] = 0;
		// }
		// $catArray[$mnth] = $catArray[$mnth] + abs($row['SUM(amount)']);
		
		echo($row['dateadded']." ".$row['category']." ".$row['SUM(amount)']);
		
		
		
		// if ($row['SUM(amount)'] == NULL) {
			// $amt = 0;
		// }
		// else {
			// $amt = abs($row['SUM(amount)']);
		// }
		// $dataArray[$mnth] = $dataArray[$mnth] + $amt;
	}
	
	
	$chart = new VerticalBarChart($_SESSION['width'], $_SESSION['height']);
	$dataSet = new XYSeriesDataSet();	
	
	// $query = "SELECT category FROM expcat";
	// $rcSet = mysql_query($query, $con);	

	// while ($row = mysql_fetch_array($rcSet)) {
		// $tmp = new XYDataSet();
		// for ($i=1; $i<=$noOfMonths; $i++) {
			// $tmp->addPoint(new Point(getMonth($i), ));
		// }
		// $dataSet->addSerie($row['category'], $tmp);
	// }
	
	// $rcSet = mysql_query($query, $con);
	// while ($row = mysql_fetch_array($rcSet)) {
	// }

	foreach($dataArray as $key => $val) {
		$tmp = new XYSeriesDataSet();
		foreach($val as $key1 => $val1) {
			$tmp->addPoint(new Point($key1,$val1));
		}
		$dataSet->addSerie($key,$tmp);
	}
	$chart->setDataSet($dataSet);
	$chart->getPlot()->setGraphCaptionRatio(0.65);

	$chart->setTitle("Monthly Statistics for HomeSoft v1.0 (".date("Y",mktime(0,0,0,1,1,$year)).")");
	$chart->render("monthlyChartCat.png");
?>

<html>
	<head>
		<title>Monthly Graph for HomeSoft v1.0</title>
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
					for($i=3; $i>=1; $i--) {
						$p = mktime(0,0,0,1,1,$year-$i);
						echo("&nbsp;&nbsp;<a href='monthlyGraph.php?year=".date("Y", $p)."'>".date("Y", $p)."</a>&nbsp;&nbsp;");
					}
					for($i=0; $i<=3; $i++) {
						$n = mktime(0,0,0,1,1,$year+$i);
						if($i==0) {
							echo("&nbsp;&nbsp;<a href='monthlyGraph.php?year=".date("Y", $n)."'><b>::&nbsp;".date("Y", $n)."&nbsp;::</b></a>&nbsp;&nbsp;");
						}
						else {
							echo("&nbsp;&nbsp;<a href='monthlyGraph.php?year=".date("Y", $n)."'>".date("Y", $n)."</a>&nbsp;&nbsp;");
						}
					}
				?>
				<br><br>
			</td>
		</tr>
		<tr align="center" valign="center">
		<td align='left' width="33%">
		<?php
			$pre = mktime(0,0,0,1,1,$year-1);
			echo("<a href='monthlyGraph.php?year=".date("Y",$pre)."'>Previous Year : ".date("Y",$pre)."</a>");
		?>
		</td>
		<td align='center' width="33%">
		<?php
			echo("<a href='monthlyGraph.php?year=".date("Y")."'>Current Year : ".date("Y")."</a>");
		?>
		</td>
		<td align='right' width="33%">
		<?php
			$nxt = mktime(0,0,0,1,1,$year+1);
			echo("<a href='monthlyGraph.php?year=".date("Y",$nxt)."'>Next Year : ".date("Y",$nxt)."</a>");
		?>
		</td>
		</tr>
		<tr align="center" valign="center">
			<td align="center" colspan="3"><img src="monthlyChartCat.png" /></td>
		</tr>
		</table>
			
	</body>
</html>
