
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
		if(Math.round((totalMemoryUsed*100)/newTotalMemory)>100){
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

//make the table dynamic		
let parent = document.getElementById('vms');
//trs
let tbody = document.createElement('tbody');
tbody.setAttribute('id','VMsBody')
for(let i=0;i<vms.length;i++){
	let tr = document.createElement('tr');

	let td0 = document.createElement('td');
	let innertd0 = document.createElement('input')
	innertd0.setAttribute('type','checkbox')
	innertd0.setAttribute('id',i)
	td0.appendChild(innertd0)
	let td = document.createElement('td');
	td.innerHTML = vms[i].serverName
	let td1 = document.createElement('td');
	td1.innerHTML = vms[i].parentCluster;
	let td2 = document.createElement('td');
	td2.innerHTML = vms[i].parentCenter;
	let td3 = document.createElement('td');
	let innerdiv = document.createElement('div');
	if(vms[i].isPowered == '1'){
		innerdiv.setAttribute('style',"height: 25px;width: 25px;background-color: #77dd77;margin: auto;border-radius: 50%;");
	}else{
		innerdiv.setAttribute('style',"height: 25px;width: 25px;background-color: #A70D2A;margin: auto;border-radius: 50%;");
	}
	td3.appendChild(innerdiv);
	let td4 = document.createElement('td');
	td4.innerHTML = vms[i].guestOS;
	let td5 = document.createElement('td');
	td5.innerHTML = vms[i].ipAddress;
	let td6 = document.createElement('td');
	td6.innerHTML = vms[i].vCPU
	let td7 = document.createElement('td');
	td7.innerHTML = vms[i].memory;
	let td8 = document.createElement('td');
	td8.innerHTML = vms[i].diskSpace;
	let td9 = document.createElement('td');
	td9.innerHTML = vms[i].hardware;
	let td10 = document.createElement('td');
	td10.innerHTML = vms[i].toolsStaus;
	let td11 = document.createElement('td');
	td11.innerHTML = vms[i].toolsState;
	let td12 = document.createElement('td');
	td12.innerHTML = vms[i].toolsVersion;
	

	tr.appendChild(td0);
	tr.appendChild(td);
	//tr.appendChild(td1);
	//tr.appendChild(td2);
	tr.appendChild(td3);
	tr.appendChild(td4);
	tr.appendChild(td5);
	tr.appendChild(td6);
	tr.appendChild(td7);
	tr.appendChild(td8);
	tr.appendChild(td9);
	//tr.appendChild(td10);
	//tr.appendChild(td11);
	tr.appendChild(td12);
	tbody.appendChild(tr);
}

//add the VMs that already added to all Clusters
if(newVMs){
	totCPU = 0;totMemory = 0;totDisk = 0;counter = 0;
	Object.keys(newVMs).forEach(key => {
		totCPU += parseInt(newVMs[key][0])
		totMemory += parseInt(newVMs[key][1])
		totDisk += parseInt(newVMs[key][2])
		counter++;
	 	let tr = document.createElement('tr');
	 	let td0 = document.createElement('td');
		let innertd0 = document.createElement('input')
		innertd0.setAttribute('type','checkbox')
		td0.appendChild(innertd0)
		let td = document.createElement('td');
		td.innerHTML = key
		let td1 = document.createElement('td');
		td1.innerHTML = ""
		let td2 = document.createElement('td');
		td2.innerHTML = ""
		let td3 = document.createElement('td');
		let innerdiv = document.createElement('div');
		innerdiv.setAttribute('style',"height: 25px;width: 25px;background-color: #A70D2A;margin: auto;border-radius: 50%;");
		td3.appendChild(innerdiv);
		let td4 = document.createElement('td');
		td4.innerHTML = ""
		let td5 = document.createElement('td');
		td5.innerHTML = ""
		let td6 = document.createElement('td');
		td6.innerHTML = newVMs[key][0]
		let td7 = document.createElement('td');
		td7.innerHTML = newVMs[key][1]
		let td8 = document.createElement('td');
		td8.innerHTML = newVMs[key][2]
		let td9 = document.createElement('td');
		td9.innerHTML = ""
		let td10 = document.createElement('td');
		td10.innerHTML = ""
		let td11 = document.createElement('td');
		td11.innerHTML = ""
		let td12 = document.createElement('td');
		td12.innerHTML = ""


		tr.appendChild(td0);
		tr.appendChild(td);
		//tr.appendChild(td1);
		//tr.appendChild(td2);
		tr.appendChild(td3);
		tr.appendChild(td4);
		tr.appendChild(td5);
		tr.appendChild(td6);
		tr.appendChild(td7);
		tr.appendChild(td8);
		tr.appendChild(td9);
		//tr.appendChild(td10);
		//tr.appendChild(td11);
		tr.appendChild(td12);
		tbody.appendChild(tr);
	});
	
	// Update the updated table 

	let t = document.querySelector('.clusterInformationsUpdated')
	// console.log("total VMs : "+totalVMs)
	// console.log("counter: "+counter)
	totalVMs += counter
	t.children[1].children[3].lastChild.innerHTML = totalVMs
	//console.log(totalVMs)
	console.log("totalCPUsAllocated : "+totalCPUsAllocated)
	console.log("totalCPUsAvailable : "+totalCPUsAvailable)
	totalCPUsAllocated += totCPU
	t.children[1].children[4].lastChild.innerHTML = Math.round(totalCPUsAllocated*100/totalCPUsAvailable)+'%'
	console.log("totalCPUsAllocated : "+totalCPUsAllocated)
	console.log("totalCPUsAvailable : "+totalCPUsAvailable)

	totalMemoryUsed += totMemory
	t.children[1].children[5].lastChild.innerHTML = Math.round(totalMemoryUsed*100/totalMemory)+'%'
	
	usedDisk += totDisk
	t.children[1].children[6].lastChild.innerHTML = Math.round(usedDisk*100/totalDisk)+'%'
	
	s = checkStatus()
	console.log("status: "+s)
	t.children[1].children[7].lastChild.innerHTML = (s.length > 0) ? "Warning<br>"+s : "Good"
	t.children[1].children[7].lastChild.style.color = (s.length > 0) ? "red" : "#77dd77"

}


// table.appendChild(tbody);
parent.appendChild(tbody);

