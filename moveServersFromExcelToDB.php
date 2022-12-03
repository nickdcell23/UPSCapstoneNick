<?php 

require 'excelReader/excel_reader2.php';
require "excelReader/SpreadsheetReader.php";
require_once "includes/db_inc.php";


$reader = new SpreadsheetReader('excelFiles/VMsInfos.csv');
$k = 0;
foreach ($reader as $key => $row) {
	if ($k == 0){
		$k = 1;
		continue;
	}
	$serverName = $row[0];
	$parentCluster = $row[1];
	$parentCenter = $row[2];
	$guestOS = $row[4];
	$ipAddress = $row[5];
	$vCPU = $row[6];
	$memory = $row[7];
	$diskSpace = intval(str_replace(",","",$row[8]));
	$hardware = $row[9];
	$toolsStaus = $row[10];
	$toolsState = $row[11];
	$toolsVersion = $row[12];
	$isPowered = false;
	if($row[3] === "Powered On"){
		$isPowered = true;
	}

	$sql = "INSERT INTO `server`(`serverName`, `parentCluster`, `parentCenter`, `isPowered`, `guestOS`, `ipAddress`, `vCPU`, `memory`, `diskSpace`, `hardware`, `toolsStaus`, `toolsState`, `toolsVersion`) VALUES ('$serverName','$parentCluster','$parentCenter','$isPowered','$guestOS','$ipAddress','$vCPU','$memory','$diskSpace','$hardware','$toolsStaus','$toolsState','$toolsVersion');";
	$result = mysqli_query($conn,$sql);
	if($result){
		echo "successfully added a<br>";
	}
	else{
		echo "Error adding server ".mysqli_error($conn);
		exit();
	}
}

