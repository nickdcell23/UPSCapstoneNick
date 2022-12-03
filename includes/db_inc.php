<?php 

$serverName = "localhost";
$user = "root";
$password = "";
$dbName = "ups";

$conn = mysqli_connect($serverName,$user,$password,$dbName);

if(!$conn){
	echo "Error : ".mysqli_connect_error();
	exit();
}