<?php
//Connect XAMPP
$connection = mysql_connect("localhost", "root", "");
mysql_query("SET NAMES UTF8",$connection);	
$db = mysql_select_db("ordereas_db", $connection);

//Connect HOST
// $connection = mysql_connect("localhost", "ordereas", "6N1tjRj40l");
// mysql_query("SET NAMES UTF8",$connection);	
// $db = mysql_select_db("ordereas_db", $connection);

?>