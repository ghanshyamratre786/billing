<?php
error_reporting(0);
ini_set('memory_limit', '512M'); 
date_default_timezone_set("Asia/Kolkata");
if($_SERVER["SERVER_NAME"]=="localhost" || $_SERVER["SERVER_NAME"]=="Komal"|| $_SERVER["SERVER_NAME"]=="Komal")
{
	// $host_name="localhost";
	// $db_name="pet_diagnostics"; 
	// $db_user="root";
	// $db_pwd="";
	$host_name="csgi-discorso.cqqqfbf6vzmy.ap-south-1.rds.amazonaws.com";
	$db_name="billing";
	$db_user="csgi_discorso";
	$db_pwd="awscsgi123";
}
else
{
	$host_name="csgi-discorso.cqqqfbf6vzmy.ap-south-1.rds.amazonaws.com";
	$db_name="billing";
	$db_user="csgi_discorso";
	$db_pwd="awscsgi123";
	
}
$conn = mysqli_connect("$host_name","$db_user","$db_pwd",$db_name);

?>