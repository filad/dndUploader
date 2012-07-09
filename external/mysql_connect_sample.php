<?php
$db = 'dnduploader';
$host = 'localhost';
$user = 'YOUR_HOSTNAME';
$pass = 'YOUR_PASS';
mysql_connect($host, $user, $pass) or die('Could not connect to MySQL. Error:' . mysql_error());
mysql_select_db($db) or die('Could not find Database. Please check if the database information is correct.');		

?>
