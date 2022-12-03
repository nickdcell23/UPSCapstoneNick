<?php
session_start();
if(!isset($_SESSION['auth']) && !isset($_SESSION['email'])) {
	header('Location: login.php');
	exit();
}
?>
<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="stylesheet" type="text/css" href="css/dashboard.css">
	<title>Dashboard</title>
</head>
<script type="text/javascript">
	var x = 0;
	var newVMs = {};
</script>
<body>
	<?php 
		include_once "includes/db_inc.php";
		include_once "includes/functions_inc.php"; 
		$data = getData($conn);
		$vms = getAllVMs($conn);

	?>
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
		<div class="addItems">
			<button onclick="addVMstoAll()">Add VMs to All Clusters</button>
		</div>
		<div class="resourcesMax">
			<h2>Recources max</h2>
			<div>
				<label>CPU %:</label>
				<input type="number" name="cpuMax" id="cpuMax" placeholder="100" required>
			</div>
			<div>
				<label>Memory %:</label>
				<input type="number" name="memoryMax" id="memoryMax" placeholder="100" required>
			</div>
			<div>
				<label>Disk %:</label>
				<input type="number" name="diskMax" id="diskMax" placeholder="70" required>
			</div>
		</div>
		<div id="bottom">
			<img src="images/employee_image.jfif">
			<div id="welcome">
				<h4>Welcome,</h4>
				<h3><?= $_SESSION['azureemail'];?></h3>
			</div>
			<a href="includes/signOut_inc.php" >Log Out</a>
		</div>
	</div>
	<div class="data">
		<div class="container">
			<div class="top">
				<button id="left" onclick="previousVCenter()"><img src="images/arrowPrev.png"></button>
				<h1></h1>
				<button id="right" onclick="nextVCenter()"><img src="images/arrowNext.png"></button>
			</div>
		</div>
	</div>
	<script type="text/javascript" src="js/functions.js"></script>
	<script type="text/javascript">
		data = JSON.parse('<?= $data;?>');
		vms = JSON.parse('<?= $vms;?>');
		console.log(vms)
		let size = Object.keys(data).length;
		let counter = 0;
		var cpuMaxDefault;var memoryMaxDefault;var diskMaxDefault;
		// localStorage.setItem('cpuMaxDefault',cpuMaxDefault);
		// localStorage.setItem('memoryMaxDefault',memoryMaxDefault);
		// localStorage.setItem('diskMaxDefault',diskMaxDefault);
		//console.log(data);
		let parent = document.querySelector('.container');
		parent.children[0].children[1].innerHTML = Object.keys(data)[0];
		Object.values(data).forEach(cluster=>{
			//console.log(cluster)
			//th
			let table = document.createElement('table');
			table.setAttribute('id',counter)
			let thead = document.createElement('thead');
			let th = document.createElement('th');
			th.innerHTML = "Cluster Name";
			let th1 = document.createElement('th');
			th1.innerHTML = "Total Nodes";
			let th2 = document.createElement('th');
			th2.innerHTML = "Total Physical cores";
			let th3 = document.createElement('th');
			th3.innerHTML = "Total VMs";
			let th4 = document.createElement('th');
			th4.innerHTML = "Total Powered on VMs";
			let th5 = document.createElement('th');
			th5.innerHTML = "Total vCPUs";
			let th6 = document.createElement('th');
			th6.innerHTML = "Total vCPUs available";
			let th7 = document.createElement('th');
			th7.innerHTML = "Total Memory(TB)";
			let th8 = document.createElement('th');
			th8.innerHTML = "Memory Used(TB)";
			thead.appendChild(th);thead.appendChild(th1);thead.appendChild(th2);thead.appendChild(th3);
			thead.appendChild(th4);thead.appendChild(th5);
			thead.appendChild(th6);thead.appendChild(th7);thead.appendChild(th8);
			table.appendChild(thead);
			//trs
			let tbody = document.createElement('tbody');
			for(let i=0;i<cluster.length;i++){
				let tr = document.createElement('tr');
				tr.setAttribute('id',cluster[i].clusterName)
				let td = document.createElement('td');
				td.setAttribute('id','clusterName');
				td.innerHTML = cluster[i].clusterName
				let td1 = document.createElement('td');
				td1.innerHTML = cluster[i].totalHost;
				let td2 = document.createElement('td');
				td2.innerHTML = cluster[i].totalPysicalCores;
				let td3 = document.createElement('td');
				td3.innerHTML = cluster[i].totalVMs;
				let td4 = document.createElement('td');
				td4.innerHTML = cluster[i].totalPoweredOnVMs;
				let td5 = document.createElement('td');
				td5.innerHTML = cluster[i].totalCPUsAllocated;
				let td6 = document.createElement('td');
				td6.innerHTML = cluster[i].totalCPUsAvailable
				let td7 = document.createElement('td');
				td7.innerHTML = Number(cluster[i].totalMemory)/1000;
				let td8 = document.createElement('td');
				td8.innerHTML = Number(cluster[i].totalMemoryUsed)/1000;

				tr.appendChild(td);
				tr.appendChild(td1);
				tr.appendChild(td2);tr.appendChild(td3);
				tr.appendChild(td4);tr.appendChild(td5);
				tr.appendChild(td6);tr.appendChild(td7);tr.appendChild(td8);
				tbody.appendChild(tr);
			}
			table.appendChild(tbody);
			if(counter != 0){
				table.style.display = 'none';
			}
			parent.appendChild(table);
			counter++;
		})
		//eventListener
		let trs = document.querySelectorAll('tr');
		trs.forEach(tr=>{
			let temp = tr.id;
			tr.addEventListener('click',function(){
				location.replace("VMs.php?building="+temp);
			})
		})

		function nextVCenter(){
				let o = (x+1)%size
				let q = document.getElementById(x)
				q.style.display = 'none'
				let l = document.getElementById(o)
				l.style.display = 'initial';
				l.style.margin = '20px auto';
				parent.children[0].children[1].innerHTML = Object.keys(data)[o];
				x = o
		}
		function previousVCenter(){
			let o = 0 
			if(x == 0){
				o = size-1
			}
			else{
			 	o = x-1
			}
			let q = document.getElementById(x)
			q.style.display = 'none'
			let l = document.getElementById(o)
			l.style.display = 'initial';
			l.style.margin = '20px auto';
			parent.children[0].children[1].innerHTML = Object.keys(data)[o];
			x = o
		}
		changeMaxResources(cpuMaxDefault,memoryMaxDefault,diskMaxDefault)
	</script>
</body>
</html>