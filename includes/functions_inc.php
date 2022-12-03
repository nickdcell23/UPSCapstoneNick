<?php 

//include_once "db_inc.php";

	//get vCenters

function getData($conn){
	$sql = "SELECT `vCenterName` FROM `vcenter`;";
	$result = mysqli_query($conn,$sql);
	$arr0 = array();
	if($result){
		while($row = mysqli_fetch_assoc($result)){
			$arr1 = array();
			$temp = $row['vCenterName'];
			//get clusters
			$sql2 = "SELECT `clusterName`, `totalHost`, `totalPysicalCores`, `totalVMs`, `totalPoweredOnVMs`, `totalCPUsAllocated`, `totalCPUsAvailable`, `totalMemory`, `totalMemoryUsed` FROM `cluster` WHERE vCenter = '$temp';";
			$result2 = mysqli_query($conn,$sql2);
			while($row2 = mysqli_fetch_assoc($result2)){
				array_push($arr1,$row2);
			}
			$arr0[$temp] = $arr1;
		}
		return json_encode($arr0);
	}
}
function getVMs($conn,$parentCluster){
	$sql = "SELECT `serverName`, `parentCluster`, `parentCenter`, `isPowered`, `guestOS`, `ipAddress`, `vCPU`, `memory`, `diskSpace`, `hardware`, `toolsStaus`, `toolsState`, `toolsVersion` FROM `server` WHERE parentCluster = '$parentCluster';";
	$result = mysqli_query($conn,$sql);
	$arr0 = array();
	if($result){
		while($row = mysqli_fetch_assoc($result)){
			array_push($arr0,$row);
		}
	}
	return json_encode($arr0);
}

function getClusterInfos($conn,$clusterName){
	$sql = "SELECT `totalHost`, `totalPysicalCores`, `totalVMs`, `totalPoweredOnVMs`, `totalCPUsAllocated`, `totalCPUsAvailable`, `totalMemory`, `totalMemoryUsed` FROM `cluster` WHERE clusterName = '$clusterName';";
	$result = mysqli_query($conn,$sql);
	if($result){
		return mysqli_fetch_assoc($result);
	}
	else{
		return "";
	}
}
//login functions:
//----------------


function isValidEmail($email){
	return (!filter_var($email, FILTER_VALIDATE_EMAIL))? false: true;
}
function isInUpsDomain($email){
	$part = explode("@",$email);
	return ($part[1] == "ups.com")?true:false;
}



function getAllVMs($conn){
	$arr = array();
	$sql = "SELECT `clusterName`, `totalMemory`, `totalHost` FROM `cluster`;";
	$result = mysqli_query($conn,$sql);
	while($row = mysqli_fetch_assoc($result)){
		$cluster = $row['clusterName'];
		$totalHost = intval($row['totalHost']);
		$totalMemory = intval($row['totalMemory']);
		$type = ($totalMemory/$totalHost == 560)? "Large" : "Small";
		$totalDisk = ($type == "Large") ? 18000*$totalHost:15000*$totalHost;
		$usedDisk = 0;
		$sql2 = "SELECT `diskSpace` FROM `server` WHERE parentCluster='$cluster'";
		$result2 = mysqli_query($conn,$sql2);
		while($row2 = mysqli_fetch_assoc($result2)){
			$usedDisk += intval($row2['diskSpace']);
		}
		array_push($arr,[$usedDisk,$totalDisk]);
	}
	return json_encode($arr);
}