<?php
error_reporting(0);
ini_set('memory_limit', '512M'); 
date_default_timezone_set("Asia/Kolkata");
if($_SERVER["SERVER_NAME"]=="localhost" || $_SERVER["SERVER_NAME"]=="Komal"|| $_SERVER["SERVER_NAME"]=="Komal")
{
	$host_name="localhost";
	$db_name="pet_diagnostics"; 
	$db_user="root";
	$db_pwd="";
}
else
{
	$host_name="localhost";
	$db_name="u786450031_test";
	$db_user="u786450031_test";
	$db_pwd="w4K[]tRc";
	
}
$conn = mysqli_connect("$host_name","$db_user","$db_pwd",$db_name);

?>