<?php
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
	<?php
		//Connect to Database
		require("login.php");
		mysql_select_db("home",$con);
		
		$path = "C:\\Program Files\\EasyPHP 2.0b1\\www\\balanceSheet\\";
		$file = "HomeSoftBalanceSheet.pdf";
		
		$backuppath = "C:\\Program Files\\EasyPHP 2.0b1\\www\\backup\\backup.sql";
		
		$pdf = pdf_new();		
		pdf_open_file($pdf, $path.$file);
		$courier = PDF_load_font($pdf, "Courier","iso8859-1",""); 
		pdf_begin_page($pdf, 595, 842);
		pdf_setfont($pdf, $courier, 7);
		$left = 30;
		$left_name = $left;
		$left_crdt = $left_name + 40;
		$left_debt = $left_crdt + 30;
		$left_totl = $left_debt + 30;
		$left_date = $left_totl + 35;
		$left_rson = $left_date + 50;
		$left_catg = $left_rson + 250;
		$left_stus = $left_catg + 70;
		$top = 800;
		$bottom = 20;
		$line = 9;
		$current = $top;
		
		pdf_show_xy($pdf, "Adjustments :", $left, $current); 
		$current = $current - $line;
	
		$rcSet2 = mysql_query("SELECT username, adjustments FROM userpass ORDER BY username");
		$common_adj = 0;
		while($row2 = mysql_fetch_array($rcSet2)) {
			if ($row2['adjustments'] > 0) {
				$payrec = "    Pay Rs. ";
				pdf_setcolor($pdf, "fill", "rgb", 0, 0, 1, 1);
			}
			else if($row2['adjustments'] < 0) {
				$payrec = "Recieve Rs. ";
				pdf_setcolor($pdf, "fill", "rgb", 0, 0.7, 0, 1);
			}
			else {
				$payrec = "";
			}
			$common_adj = $common_adj + $row2['adjustments'];
			$name = $row2['username'];
			while(strlen($name) < 17) {
				$name = $name." ";
			}
			$str = abs($row2['adjustments']);
			while(strlen($str) < 7) {
				$str = " ".$str;
			}
			pdf_show_xy($pdf, $name." : ".$payrec.$str, $left, $current); 
			pdf_initGraphics($pdf);
			$current = $current - $line;
		}
		$str = $common_adj;
		while(strlen($str) < 7) {
			$str = " ".$str;
		}
		if ($common_adj < 0) {
			$adj_amt_total = "Recieve Rs. ".$str;
			pdf_setcolor($pdf, "fill", "rgb", 0, 0.7, 0, 1);
		}
		else {
			$adj_amt_total = "    Pay Rs. ".$str;
			pdf_setcolor($pdf, "fill", "rgb", 0, 0, 1, 1);
		}
		$str = $adj_amt_total;
		while(strlen($str) < 7) {
			$str = " ".$str;
		}
		pdf_show_xy($pdf, "Total Adjustment  : ".$str, $left, $current); 
		pdf_initGraphics($pdf);
		$current = $current - $line;
		$current = $current - $line;
		
		$rcSet = mysql_query("SELECT SUM(amount) FROM common WHERE approved='1'");		
		$row = mysql_fetch_array($rcSet);
		$common_app = -$row['SUM(amount)'];
		$rcSet = mysql_query("SELECT SUM(amount) FROM common WHERE approved='1' or approved='0'");		
		$row = mysql_fetch_array($rcSet);
		$common_all = -$row['SUM(amount)'];
		$common_box = $common_app + $common_adj;
		
		$str = $common_app;
		while(strlen($str) < 7) {
			$str = " ".$str;
		}
		pdf_show_xy($pdf, "Common Balance (Approved) : Rs. ".$str, $left, $current); 
		$current = $current - $line;
		
		$str = $common_all;
		while(strlen($str) < 7) {
			$str = " ".$str;
		}
		pdf_show_xy($pdf, "Common Balance (All)      : Rs. ".$str, $left, $current); 
		$current = $current - $line;
		
		$str = $common_box;
		while(strlen($str) < 7) {
			$str = " ".$str;
		}
		pdf_show_xy($pdf, "Common Balance (In Box)   : Rs. ".$str, $left, $current); 
		$current = $current - $line;
		$current = $current - $line;
		$current = $current - $line;
		
		$rcSet = mysql_query("SELECT * FROM common WHERE approved <> '20' ORDER BY expno ASC");
		if(mysql_num_rows($rcSet) > 0) {
			$total = 0;
			while ($row = mysql_fetch_array($rcSet)) {
				if($row['approved'] == 0) {
					$status = "Pending";
					pdf_setcolor($pdf, "fill", "rgb", 0, 0, 1, 1);
					//pdf_set_value($pdf, "textrendering", 0);
				}
				else {
					$status = "Approved";
					pdf_setcolor($pdf, "fill", "rgb", 0, 0, 0, 0);
					//pdf_set_value($pdf, "textrendering", 0);
				}
				pdf_show_xy($pdf, $row['username'],   $left_name, $current);
				$amt = abs($row['amount']);
				while(strlen($amt) < 7) {
					$amt = " ".$amt;
				}
				$total = $total - $row['amount'];
				$tot_str = "".$total;
				while(strlen($tot_str) < 7) {
					$tot_str = " ".$tot_str;
				}
				if($row['amount'] > 0) { // Credit
					pdf_show_xy($pdf, $amt, $left_crdt, $current);
				}
				else { // Debit
					pdf_show_xy($pdf, $amt, $left_debt, $current);
				}
				pdf_show_xy($pdf, $tot_str,           $left_totl, $current);
				pdf_show_xy($pdf, $row['dateadded'],  $left_date, $current);
				
				pdf_show_xy($pdf, $row['reason'],     $left_rson, $current);
				// $reason_text = $row['reason'];
				// $reason_left = pdf_show_boxed($pdf, $reason_text, $left_rson, $current, $left_catg-$left_rson-100, $line, "left", "");
				// $count = 1;
				// pdf_show_xy($pdf, $reason_left,  0, $current);
				// while($reason_left > 0 && $count<3) {
					// $reason_text = substr($reason_text, -$reason_left);
					// $reason_left = pdf_show_boxed($pdf, $reason_text." ".$reason_left, $left_rson, $current - ($count * $line), $left_catg-$left_rson-100, $line, "left", "");
					// $count++;
				// }
				
				pdf_show_xy($pdf, $row['category'],   $left_catg, $current);
				pdf_show_xy($pdf, $status,            $left_stus, $current);
				$current = $current - $line;
				if($current <= $bottom) {
					pdf_end_page($pdf);
					pdf_begin_page($pdf, 595, 842);
					pdf_setfont($pdf, $courier, 7);
					$current = $top;					
				}
				pdf_initGraphics($pdf);
			}				
		}
		else {
			pdf_show_xy($pdf, "No Records fetched", $left, $current);
			$current = $current - $line;
		}
		mysql_close($con);
		pdf_end_page($pdf);
		pdf_close($pdf);
		
		//email
		$email_from = "bot@homesoft.fw.nu"; // Who the email is from
		$email_subject = "HomeSoft Balance Sheet ".date("d-M-Y g:i a"); // The Subject of the email
		$email_message = "This is an auto generated mail from HomeSoft. Please find attached the Balance Sheet for ".date("d-M-Y g:i:s a");  // Message that the email has in it
		$email_to1 = "mihirgorecha@gmail.com";
		$email_to2 = "aakashv2.1@gmail.com";
		
		$headers = "From: ".$email_from;
		$semi_rand = md5(time());
		$mime_boundary = "==Multipart_Boundary_x{$semi_rand}x";
		$headers .= "\nMIME-Version: 1.0\n"."Content-Type: multipart/mixed;\n"." boundary=\"{$mime_boundary}\"";
		$email_message .= "This is a multi-part message in MIME format.\n\n"."--{$mime_boundary}\n"."Content-Type:text/html; charset=\"iso-8859-1\"\n"."Content-Transfer-Encoding: 7bit\n\n".$email_message."\n\n";
		/********************************************** First File ********************************************/
		$fileatt = $path.$file; // Path to the file
		$fileatt_type = "application/octet-stream"; // File Type
		$fileatt_name = "HomeSoftBalanceSheet.pdf"; // Filename that will be used for the file as the attachment
		$fp = fopen($fileatt,'rb');
		$data = fread($fp,filesize($fileatt));
		fclose($fp);
		$data = chunk_split(base64_encode($data));
		$email_message .= "--{$mime_boundary}\n"."Content-Type: {$fileatt_type};\n".
		" name=\"{$fileatt_name}\"\n" .
		//"Content-Disposition: attachment;\n" .
		//" filename=\"{$fileatt_name}\"\n" .
		"Content-Transfer-Encoding: base64\n\n".$data . "\n\n"."--{$mime_boundary}\n";
		unset($data);
		unset($fp);
		unset($fileatt);
		unset($fileatt_type);
		unset($fileatt_name);
		/********************************************** Second File ********************************************/
		$fileatt = $backuppath; // Path to the file
		$fileatt_type = "text/sql"; // File Type
		$fileatt_name = "backup.sql"; // Filename that will be used for the file as the attachment
		$fp = fopen($fileatt,'rb');
		$data = fread($fp,filesize($fileatt));
		fclose($fp);
		$data = chunk_split(base64_encode($data));
		$email_message .= "--{$mime_boundary}\n"."Content-Type: {$fileatt_type};\n".
		" name=\"{$fileatt_name}\"\n".
		//"Content-Disposition: attachment;\n" .
		//" filename=\"{$fileatt_name}\"\n" .
		"Content-Transfer-Encoding: base64\n\n".$data . "\n\n"."--{$mime_boundary}\n";
		unset($data);
		unset($fp);
		unset($fileatt);
		unset($fileatt_type);
		unset($fileatt_name);


		$ok1 = @mail($email_to1, $email_subject, $email_message, $headers);
		$ok2 = @mail($email_to2, $email_subject, $email_message, $headers);

		if($ok1) {
			echo "<font face=verdana size=2>The file was successfully sent to ".$email_to1."!</font>";
		} else {
			echo "<font face=verdana size=2>Sorry but the email could not be sent to ".$email_to1.". Please go back and try again!</font>";
		}
		echo "<br>";
		if($ok2) {
			echo "<font face=verdana size=2>The file was successfully sent to ".$email_to2."!</font>";
		} else {
			echo "<font face=verdana size=2>Sorry but the email could not be sent to ".$email_to2.". Please go back and try again!</font>";
		}
		
		
		//header("Location:".$file);
	?>
	</body>
</html>