<?php
$fileatt = "C:\\Program Files\\EasyPHP 2.0b1\\www\\HomeSoftBalanceSheet.pdf"; // Path to the file
$fileatt_type = "application/octet-stream"; // File Type
$fileatt_name = "HomeSoftBalanceSheet.pdf"; // Filename that will be used for the file as the attachment
$email_from = "mihirgorecha@homesoft.com"; // Who the email is from
$email_subject = "test"; // The Subject of the email
$email_txt = "test"; // Message that the email has in it
$email_to = "mihirgorecha@gmail.com"; // Who the email is too
$headers = "From: ".$email_from;

$file = fopen($fileatt,'rb');
$data = fread($file,filesize($fileatt));
fclose($file);

$semi_rand = md5(time());
$mime_boundary = "==Multipart_Boundary_x{$semi_rand}x";

$headers .= "\nMIME-Version: 1.0\n"."Content-Type: multipart/mixed;\n"." boundary=\"{$mime_boundary}\"";

$email_message = "This is a multi-part message in MIME format.\n\n"."--{$mime_boundary}\n".
"Content-Type:text/html; charset=\"iso-8859-1\"\n"."Content-Transfer-Encoding: 7bit\n\n".$email_txt."\n\n";

$data = chunk_split(base64_encode($data));

$email_message .= "--{$mime_boundary}\n"."Content-Type: {$fileatt_type};\n"." name=\"{$fileatt_name}\"\n".
//"Content-Disposition: attachment;\n" .
//" filename=\"{$fileatt_name}\"\n" .
"Content-Transfer-Encoding: base64\n\n".$data."\n\n"."--{$mime_boundary}--\n";

$ok = @mail($email_to, $email_subject, $email_message, $headers);

if($ok) {
	echo "<font face=verdana size=2>The file was successfully sent!</font>";
} else {
	die("Sorry but the email could not be sent. Please go back and try again!");
}
?>