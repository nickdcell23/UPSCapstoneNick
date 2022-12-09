<?php
session_start();
if(!isset($_SESSION['email'])){
	header('Location: login.php');
	exit();
}
?>
<?php 
	include_once "includes/db_inc.php";
	include_once "includes/functions_inc.php"; 
	$vms = getVMs($conn,$_GET['building']);
	
	$clusterInfos = getClusterInfos($conn,$_GET['building']);
	$totalVMs = $clusterInfos['totalVMs'];
	$totalCPUsAllocated = intval($clusterInfos['totalCPUsAllocated']);
	$totalCPUsAvailable = intval($clusterInfos['totalCPUsAvailable']);
	$totalMemoryUsed = intval($clusterInfos['totalMemoryUsed']);
	$totalMemory = intval($clusterInfos['totalMemory']);
	$totalHost = intval($clusterInfos['totalHost']);
	$type = ($totalMemory/$totalHost == 560)? "Large" : "Small";
	$totalDisk = ($type == "Large") ? 18000*$totalHost:15000*$totalHost;
	$usedDisk = 0;
	foreach(json_decode($vms) as $vm){
		$usedDisk += intval($vm->diskSpace);
	}
	$diskUsage = round($usedDisk*100/$totalDisk);
?>
<!DOCTYPE html>
<html>
<script type="text/javascript">
	var clusterName = "<?= $_GET['building'];?>";
	var newVMs = JSON.parse(localStorage.getItem("newVMs"));
	var deletedVMs = ""
	var type = "<?= $type;?>";
	var vms = JSON.parse('<?=$vms;?>')
	var totalHost = "<?=$totalHost;?>"
	var totalVMs = parseInt("<?= $totalVMs;?>");
	var totalCPUsAvailable = parseInt("<?= $totalCPUsAvailable;?>");
	var totalMemoryUsed = parseInt("<?= $totalMemoryUsed;?>");
	var totalDisk = parseInt("<?=$totalDisk;?>");
	var totalCPUsAllocated = parseInt("<?= $totalCPUsAllocated;?>");
	var totalMemory = parseInt("<?= $totalMemory;?>");
	var usedDisk = parseInt("<?=$usedDisk;?>");

	// console.log("CPU available : ",totalCPUsAvailable)
	// console.log("Memory available : ",totalMemory)
	// console.log("Disk available : ",totalDisk)
	// console.log("type : "+type)
	// console.log("Used CPU available : ",totalCPUsAllocated)
	// console.log("Used Memory available : ",totalMemoryUsed)
	// console.log("Used Disk available : ",usedDisk)
	// console.log('newVMs',newVMs)
</script>
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="stylesheet" type="text/css" href="css/VMs.css">
	<!-- <script src="js/table2excel.js"></script> -->
	<title>VMs</title>
</head>
<body>
	<div class="infos">
		<div id="infos_top">
			<img src="images/logo2.png">
			<h2>Cluster Capacity Planning Tool</h2>
			<button><img src="images/menu.png"></button>
		</div>
		<div class="dashBoard">
			<a href="dashboard.php">
			<img src="images/house.png">
			<h2>Dashboard</h2>
			</a>
		</div>
		<div class="newTask">
			<img src="images/bolt2.png">
			<h2>Start a New Task</h2>
			<button onclick="displayAddItems()"><img src="images/arrowDown.png"></button>
		</div>
		<div class="addItems">
			<button onclick="addNodes()">Add Node</button>
			<button onclick="addVMs()">Add VM</button>
		</div>
		<div id="bottom">
			<img src="images/employee_image.jfif">
			<div id="welcome">
				<h4>Welcome,</h4>
				<h3><?= $_SESSION['azureemail'];?></h3>
			</div>
			<a href="includes/signOut_inc.php">Log Out</a>
		</div>
	</div>
	<div class="data">
		
		<div class="container">
			<div class="top">

				<div id="infos">
					<script type="text/javascript">
						let tableOriginal = document.createElement('table')
						tableOriginal.setAttribute('class','clusterInformations')
						let thead = document.createElement('thead')
						let td = document.createElement('td')
						td.setAttribute('colspan','2')
						td.innerHTML = "Cluster Original Informations"
						thead.appendChild(td)

						let tbody1 = document.createElement('tbody')
						let tr1 = document.createElement('tr')
						let td1 = document.createElement('td')
						td1.innerHTML = "Cluster Name"
						let td11 = document.createElement('td')
						td11.innerHTML = clusterName

						tr1.appendChild(td1);tr1.appendChild(td11);

						let tr2 = document.createElement('tr')
						let td2 = document.createElement('td')
						td2.innerHTML = "Total Nodes"
						let td12 = document.createElement('td')
						td12.innerHTML = totalHost

						tr2.appendChild(td2);tr2.appendChild(td12);

						let tr3 = document.createElement('tr')
						let td3 = document.createElement('td')
						td3.innerHTML = "Nodes Type"
						let td13 = document.createElement('td')
						td13.innerHTML = type

						tr3.appendChild(td3);tr3.appendChild(td13);

						let tr4 = document.createElement('tr')
						let td4 = document.createElement('td')
						td4.innerHTML = "Total VMs"
						let td14 = document.createElement('td')
						td14.innerHTML = totalVMs

						tr4.appendChild(td4);tr4.appendChild(td14);

						let tr5 = document.createElement('tr')
						let td5 = document.createElement('td')
						td5.innerHTML = "CPUs Usage"
						let td15 = document.createElement('td')
						td15.innerHTML = Math.round((totalCPUsAllocated*100)/totalCPUsAvailable)+"%"

						tr5.appendChild(td5);tr5.appendChild(td15);

						let tr6 = document.createElement('tr')
						let td6 = document.createElement('td')
						td6.innerHTML = "Memory Usage"
						let td16 = document.createElement('td')
						td16.innerHTML = Math.round((totalMemoryUsed*100)/totalMemory)+"%"

						tr6.appendChild(td6);tr6.appendChild(td16);

						let tr7 = document.createElement('tr')
						let td7 = document.createElement('td')
						td7.innerHTML = "Disk Usage"
						let td17 = document.createElement('td')
						td17.innerHTML = Math.round((usedDisk*100)/totalDisk)+"%"

						tr7.appendChild(td7);tr7.appendChild(td17);

						let tr8 = document.createElement('tr')
						let td8 = document.createElement('td')
						td8.innerHTML = "Status"
						let td18 = document.createElement('td')
						s = checkStatus()
						td18.innerHTML = (s.length > 0) ? "Warning<br>"+s : "Good"
						td18.style.color = (s.length > 0) ? "red" : "#77dd77"


						tr8.appendChild(td8);tr8.appendChild(td18);

						tbody1.appendChild(tr1);tbody1.appendChild(tr2);tbody1.appendChild(tr3);tbody1.appendChild(tr4);tbody1.appendChild(tr5);
						tbody1.appendChild(tr6);tbody1.appendChild(tr7);tbody1.appendChild(tr8);

						let par = document.getElementById('infos')
						tableOriginal.appendChild(thead);tableOriginal.appendChild(tbody1)
						par.appendChild(tableOriginal)


						function checkStatus(){
							// console.log("old CPU totalCPUsAvailable : "+ totalCPUsAvailable)
							// console.log("old Memory totalCPUsAvailable : "+ totalMemory)
							// console.log("old Disk totalCPUsAvailable : "+ totalDisk)

							// console.log("new CPU totalCPUsAvailable : "+ newTotalCPUsAvailable)
							// console.log("new Memory totalCPUsAvailable : "+ newTotalMemory)
							// console.log("new Disk totalCPUsAvailable : "+ newTotalDisk)
							let status = ""
							// Slack space for disk
							if(localStorage.diskMaxDefault && Math.round((usedDisk*100)/totalDisk)>localStorage.diskMaxDefault){
								status+="Disk exceeded "+localStorage.diskMaxDefault+"%<br>"
							}
							else {
								if(Math.round((usedDisk*100)/totalDisk)>70){
									status+="Disk exceeded 70%<br>"
								}
							}
							// Slack space for Memory
							if(localStorage.memoryMaxDefault && Math.round((totalMemoryUsed*100)/totalMemory)>localStorage.memoryMaxDefault){
								status+="Memory exceeded "+localStorage.memoryMaxDefault+"%<br>"
							}
							else{
								if(Math.round((totalMemoryUsed*100)/totalMemory)>100){
									status+="Memory exceeded 100%<br>"
								}
							}
							if(localStorage.cpuMaxDefault && Math.round((totalCPUsAllocated*100)/totalCPUsAvailable)>localStorage.cpuMaxDefault){
								status+="\nCPU exceeded "+localStorage.cpuMaxDefault+"%"
							}
							else{
								if(Math.round((totalCPUsAllocated*100)/totalCPUsAvailable)>100){
									status+="CPU exceeded 100%<br>"
								}
							}
							return status
						}
					</script>
					

				</div>
				<div id="infos">
					<script type="text/javascript">
						let tableUpdated = document.createElement('table')
						tableUpdated.setAttribute('class','clusterInformationsUpdated clusterInformations')
						let theadUpdated = document.createElement('thead')
						let td0 = document.createElement('td')
						td0.setAttribute('colspan','2')
						td0.innerHTML = "Cluster Updated Informations"
						theadUpdated.appendChild(td0)

						let tbody10 = document.createElement('tbody')
						let tr10 = document.createElement('tr')
						let td10 = document.createElement('td')
						td10.innerHTML = "Cluster Name"
						let td110 = document.createElement('td')
						td110.innerHTML = clusterName

						tr10.appendChild(td10);tr10.appendChild(td110);

						let tr20 = document.createElement('tr')
						let td20 = document.createElement('td')
						td20.innerHTML = "Total Nodes"
						let td120 = document.createElement('td')
						td120.innerHTML = totalHost

						tr20.appendChild(td20);tr20.appendChild(td120);

						let tr30 = document.createElement('tr')
						let td30 = document.createElement('td')
						td30.innerHTML = "Nodes Type"
						let td130 = document.createElement('td')
						td130.innerHTML = type

						tr30.appendChild(td30);tr30.appendChild(td130);

						let tr40 = document.createElement('tr')
						let td40 = document.createElement('td')
						td40.innerHTML = "Total VMs"
						let td140 = document.createElement('td')
						td140.innerHTML = totalVMs

						tr40.appendChild(td40);tr40.appendChild(td140);

						let tr50 = document.createElement('tr')
						let td50 = document.createElement('td')
						td50.innerHTML = "CPUs Usage"
						let td150 = document.createElement('td')
						td150.innerHTML = Math.round((totalCPUsAllocated*100)/totalCPUsAvailable)+"%"

						tr50.appendChild(td50);tr50.appendChild(td150);

						let tr60 = document.createElement('tr')
						let td60 = document.createElement('td')
						td60.innerHTML = "Memory Usage"
						let td160 = document.createElement('td')
						td160.innerHTML = Math.round((totalMemoryUsed*100)/totalMemory)+"%"

						tr60.appendChild(td60);tr60.appendChild(td160);

						let tr70 = document.createElement('tr')
						let td70 = document.createElement('td')
						td70.innerHTML = "Disk Usage"
						let td170 = document.createElement('td')
						td170.innerHTML = Math.round((usedDisk*100)/totalDisk)+"%"

						tr70.appendChild(td70);tr70.appendChild(td170);

						let tr80 = document.createElement('tr')
						let td80 = document.createElement('td')
						td80.innerHTML = "Status"
						let td180 = document.createElement('td')
						s = checkStatus()
						td180.innerHTML = (s.length > 0) ? "Warning<br>"+s : "Good"
						td180.style.color = (s.length > 0) ? "red" : "#77dd77"


						tr80.appendChild(td80);tr80.appendChild(td180);

						tbody10.appendChild(tr10);tbody10.appendChild(tr20);tbody10.appendChild(tr30);tbody10.appendChild(tr40);tbody10.appendChild(tr50);
						tbody10.appendChild(tr60);tbody10.appendChild(tr70);tbody10.appendChild(tr80);

						let par0 = document.getElementById('infos')
						tableUpdated.appendChild(theadUpdated);tableUpdated.appendChild(tbody10)
						par0.appendChild(tableUpdated)
					</script>
				</div>
				<div id="whatIf">
					<script type="text/javascript">
						let tableWhatIf = document.createElement('table')
						tableWhatIf.setAttribute('class','clusterInformations')
						let theadW = document.createElement('thead')
						let tdW = document.createElement('td')
						tdW.setAttribute('colspan','2')
						tdW.innerHTML = "What If?"
						theadW.appendChild(tdW)

						let tbody1W = document.createElement('tbody')
						let tr1W = document.createElement('tr')
						let td1W = document.createElement('td')
						td1W.setAttribute('colspan','2')
						td1W.innerHTML = "N - 1"
						tr1W.appendChild(td1W);

						newTotalHost =totalHost-1;
						newTotalCPUsAvailable = totalCPUsAvailable - 32;
						newTotalMemory = totalMemory - ((type == 'Large')? 560:384);
						newTotalDisk = totalDisk - ((type == 'Large')? 18000:15000);
						//newDiskUsage = Math.round(usedDisk*100/newTotalDisk);

						let tr2W = document.createElement('tr')
						let td2W = document.createElement('td')
						td2W.innerHTML = "Total Nodes"
						let td12W = document.createElement('td')
						td12W.innerHTML = newTotalHost

						tr2W.appendChild(td2W);tr2W.appendChild(td12W);


						let tr5W = document.createElement('tr')
						let td5W = document.createElement('td')
						td5W.innerHTML = "CPUs Usage"
						let td15W = document.createElement('td')
						td15W.innerHTML = Math.round((totalCPUsAllocated*100)/newTotalCPUsAvailable)+"%"

						tr5W.appendChild(td5W);tr5W.appendChild(td15W);

						let tr6W = document.createElement('tr')
						let td6W = document.createElement('td')
						td6W.innerHTML = "Memory Usage"
						let td16W = document.createElement('td')
						td16W.innerHTML = Math.round((totalMemoryUsed*100)/newTotalMemory)+"%"

						tr6W.appendChild(td6W);tr6W.appendChild(td16W);

						let tr7W = document.createElement('tr')
						let td7W = document.createElement('td')
						td7W.innerHTML = "Disk Usage"
						let td17W = document.createElement('td')
						td17W.innerHTML = Math.round((usedDisk*100)/newTotalDisk)+"%"

						tr7W.appendChild(td7W);tr7W.appendChild(td17W);

						let tr8W = document.createElement('tr')
						let td8W = document.createElement('td')
						td8W.innerHTML = "Status"
						let td18W = document.createElement('td')
						s = checkWhatIf()
						td18W.innerHTML = (s.length > 0) ? "Warning<br>"+s : "Good"
						td18W.style.color = (s.length > 0) ? "red" : "#77dd77"


						tr8W.appendChild(td8W);
						tr8W.appendChild(td18W);

						tbody1W.appendChild(tr1W);tbody1W.appendChild(tr2W);tbody1W.appendChild(tr5W);
						tbody1W.appendChild(tr6W);tbody1W.appendChild(tr7W);
						tbody1W.appendChild(tr8W);

						let parW = document.getElementById('whatIf')
						tableWhatIf.appendChild(theadW);tableWhatIf.appendChild(tbody1W)
						parW.appendChild(tableWhatIf)

						if (newTotalHost >3){
							let tableWhatIf2 = document.createElement('table')
							tableWhatIf2.setAttribute('class','clusterInformations')
							let theadW2 = document.createElement('thead')
							let tdW2 = document.createElement('td')
							tdW2.setAttribute('colspan','2')
							tdW2.innerHTML = "What If?"
							theadW2.appendChild(tdW2)

							let tbody1W2 = document.createElement('tbody')
							let tr1W2 = document.createElement('tr')
							let td1W2 = document.createElement('td')
							td1W2.setAttribute('colspan','2')
							td1W2.innerHTML = "N - 2"
							tr1W2.appendChild(td1W2);

							newTotalHost -=1;
							newTotalCPUsAvailable -= 32;
							newTotalMemory -= ((type == 'Large')? 560:384);
							newTotalDisk -= ((type == 'Large')? 18000:15000);
							//$newDiskUsage = round($usedDisk*100/$newTotalDisk);

							let tr2W2 = document.createElement('tr')
							let td2W2 = document.createElement('td')
							td2W2.innerHTML = "Total Nodes"
							let td12W2 = document.createElement('td')
							td12W2.innerHTML = newTotalHost

							tr2W2.appendChild(td2W2);tr2W2.appendChild(td12W2);


							let tr5W2 = document.createElement('tr')
							let td5W2 = document.createElement('td')
							td5W2.innerHTML = "CPUs Usage"
							let td15W2 = document.createElement('td')
							td15W2.innerHTML = Math.round((totalCPUsAllocated*100)/newTotalCPUsAvailable)+"%"

							tr5W2.appendChild(td5W2);tr5W2.appendChild(td15W2);

							let tr6W2 = document.createElement('tr')
							let td6W2 = document.createElement('td')
							td6W2.innerHTML = "Memory Usage"
							let td16W2 = document.createElement('td')
							td16W2.innerHTML = Math.round((totalMemoryUsed*100)/newTotalMemory)+"%"

							tr6W2.appendChild(td6W2);tr6W2.appendChild(td16W2);

							let tr7W2 = document.createElement('tr')
							let td7W2 = document.createElement('td')
							td7W2.innerHTML = "Disk Usage"
							let td17W2 = document.createElement('td')
							td17W2.innerHTML = Math.round((usedDisk*100)/newTotalDisk)+"%"

							tr7W2.appendChild(td7W2);tr7W2.appendChild(td17W2);

							let tr8W2 = document.createElement('tr')
							let td8W2 = document.createElement('td')
							td8W2.innerHTML = "Status"
							let td18W2 = document.createElement('td')
							s = checkWhatIf()
							td18W2.innerHTML = (s.length > 0) ? "Warning<br>"+s : "Good"
							td18W2.style.color = (s.length > 0) ? "red" : "#77dd77"


							tr8W2.appendChild(td8W2);tr8W2.appendChild(td18W2);

							tbody1W2.appendChild(tr1W2);tbody1W2.appendChild(tr2W2);tbody1W2.appendChild(tr5W2);
							tbody1W2.appendChild(tr6W2);tbody1W2.appendChild(tr7W2);tbody1W2.appendChild(tr8W2);

							let parW2 = document.getElementById('whatIf')
							tableWhatIf2.appendChild(theadW2);tableWhatIf2.appendChild(tbody1W2)
							parW2.appendChild(tableWhatIf2)

						}
						function checkWhatIf(){
							// console.log("old CPU totalCPUsAvailable : "+ totalCPUsAvailable)
							// console.log("old Memory totalCPUsAvailable : "+ totalMemory)
							// console.log("old Disk totalCPUsAvailable : "+ totalDisk)

							// console.log("new CPU totalCPUsAvailable : "+ newTotalCPUsAvailable)
							// console.log("new Memory totalCPUsAvailable : "+ newTotalMemory)
							// console.log("new Disk totalCPUsAvailable : "+ newTotalDisk)
							let status = ""
							// Slack space for disk
							if(localStorage.diskMaxDefault && Math.round((usedDisk*100)/newTotalDisk)>localStorage.diskMaxDefault){
								status+="Disk exceeded "+localStorage.diskMaxDefault+"%<br>"
							}
							else {
								if(Math.round((usedDisk*100)/newTotalDisk)>70){
									status+="Disk exceeded 70%<br>"
								}
							}
							// Slack space for Memory
							if(localStorage.memoryMaxDefault && Math.round((totalMemoryUsed*100)/newTotalMemory)>localStorage.memoryMaxDefault){
								status+="Memory exceeded "+localStorage.memoryMaxDefault+"%<br>"
							}
							else{
								if(Math.round((totalMemoryUsed*100)/newTotalMemory)>100){
									status+="Memory exceeded 100%<br>"
								}
							}
							if(localStorage.cpuMaxDefault && Math.round((totalCPUsAllocated*100)/newTotalCPUsAvailable)>localStorage.cpuMaxDefault){
								status+="\nCPU exceeded "+localStorage.cpuMaxDefault+"%"
							}
							else{
								if(Math.round((totalCPUsAllocated*100)/newTotalCPUsAvailable)>100){
									status+="CPU exceeded 100%<br>"
								}
							}
							return status
						}
					</script>
				</div>
			</div>
			<div id="afterTop" style="visibility: hidden;">
				<h1>AFTER ADDING X NODES</h1>
				<button onclick="closeNewStats()">X</button>
			</div>
			<div class="middle">
				<div id="buttons">
					<button onclick="update()" id="delete">Delete Selected VMs</button>
					<button onclick="location.reload()" id="reload">Reload</button>
					<button  onclick="exportPDF()" id="pdf"><img src="images/pdf.png"></button>
					<button  onclick="exportEXCEL()" id="excel"><img src="images/excel.png"></button>
				</div>
			</div>
			<table id="vms">
				<thead>
					<th>Select</th>
					<th>Server Name</th>
					<!-- <th>Parent Cluster</th>
					<th>Parent Center</th> -->
					<th>isPowered</th>
					<th>Guest OS</th>
					<th>IP address</th>
					<th>vCPUs</th>
					<th>Memory(GB)</th>
					<th>Disk space(GB)</th>
					<th>Hardware</th>
					<!-- <th>Tools Status</th>
					<th>Tools State</th> -->
					<th>Tools Version</th>
				</thead>
			</table>
		</div>
	</div>
	<script type="text/javascript" src="js/fillVMs.js"></script>
	<script type="text/javascript" src="js/functions.js"></script>
</body>
</html>