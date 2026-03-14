<?php

date_default_timezone_set('Asia/Ho_Chi_Minh');

$servername = "localhost";
$dBUsername = "root";
$dbPassword = "";
$dBName = "hr_management";

$conn = mysqli_connect($servername, $dBUsername, $dbPassword, $dBName);

if (!$conn) {
	echo "Databese Connection Failed";
}

?>