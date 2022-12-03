<?php 

require 'excelReader/excel_reader2.php';
require "excelReader/SpreadsheetReader.php";
require_once "includes/db_inc.php";


$reader = new SpreadsheetReader('excelFiles/ClusterInfos.xlsx');
$k = 0;
foreach ($reader as $key => $row) {
	if ($k == 0){
		$k = 1;
		continue;
	}
	$clusterName = $row[0];
	$vCenter = $row[1];
	$totalHost = $row[2];
	$totalPhysicalCores = $row[3];
	$totalVMs = $row[4];
	$totalPoweredOnVMs = $row[5];
	$totalCPUsAllocated = $row[6];
	$totalCPUsAvailable = $row[7];
	$totalMemory = $row[8];
	$totalMemoryUsed = $row[9];

	$sql2 = "SELECT `vCenterName`, `totalClusters` FROM `vcenter` WHERE vCenterName = '$vCenter';"; 
	$result2 = mysqli_query($conn,$sql2);
	$numRows = mysqli_num_rows($result2);
	if($numRows == 0){
		$sql3 = "INSERT INTO `vcenter`(`vCenterName`, `totalClusters`) VALUES ('$vCenter',1);"; 
		$result3 = mysqli_query($conn,$sql3);
		if(!$result3){
			echo "Error adding vCenter ".mysqli_error($conn);
			exit();
		}
	}
	else{
		$m = mysqli_fetch_assoc($result2);
		$temp = $m['totalClusters'] + 1;
		$sql4 = "UPDATE `vcenter` SET `totalClusters`='$temp' WHERE vCenterName = '$vCenter';"; 
		$result4 = mysqli_query($conn,$sql4);
		if(!$result4){
			echo "Error adding vCenter ".mysqli_error($conn);
			exit();
		}
	}
	$sql = "INSERT INTO `cluster`(`clusterName`, `vCenter`, `totalHost`, `totalPysicalCores`, `totalVMs`, `totalPoweredOnVMs`, `totalCPUsAllocated`, `totalCPUsAvailable`, `totalMemory`, `totalMemoryUsed`) VALUES ('$clusterName','$vCenter','$totalHost','$totalPhysicalCores','$totalVMs','$totalPoweredOnVMs','$totalCPUsAllocated','$totalCPUsAvailable',$totalMemory*1000,$totalMemoryUsed*1000)";
	$result = mysqli_query($conn,$sql);
	if($result){
		echo "successfully added a<br>";
	}
	else{
		echo "Error adding vCenter ".mysqli_error($conn);
		exit();
	}
}

