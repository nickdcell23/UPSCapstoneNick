<?php 

// get our authentication from Azure AD. We skip the login screen for now
// if the user logs out, they get redirected here, and have to use the DB login
// for some reason, OAuth refuses to log me out correctly on Azure's side

session_start();
include './includes/auth.php';
$Auth = new modAuth();
?>
<!DOCTYPE html> 
<html>
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="stylesheet" type="text/css" href="css/login.css">
	<title>Login</title>
</head>
<body>
	<div class="wrapper"></div>
	<div id="content">
		<img class="resize" src="images/logo.png">
        <h1>Cluster Capacity Planning Tool</h1><br>
		<label id="error"></label>
		<form action="includes/login_inc.php" method="POST">
			<div id="innerContainer">
				<img src="images/person.png">
                <br>
				<input type="text" name="email" placeholder="Email Address">
			</div>
			<div id="innerContainer">
				<img src="images/lock.png">
				<input type="password" name="password" placeholder="Password">
			</div>
			<button type="submit" name="login">Login</button>
		</form>
	</div>

</body>
<script type="text/javascript">
	let element = document.getElementById('error');
	let err = "<?= $_GET['error'];?>";
	console.log(err);
	console.log(element);
	if(err === 'passwordIncorrect'){
		display("Password incorrect");
	}
	else if(err === 'recordUnavailable'){
		display("Record unavailable");
	}
	else if(err === 'notInUpsDomain'){
		display("Not in Ups UPS Domain");
	}
	else if(err === 'emailNotValid'){
		display("Invalid Email");
	}

	function display(error){
		element.innerHTML = error;
		time = 1;
		var interval = setInterval(()=>{
			if(time <6){
				element.style.opacity = 1-(time*0.2);
				time++;
				if(time == 5){
					element.style.display = 'none';
				}
			}
			else{
				clearInterval(interval);
			}
		},1000)
	}
</script>
</html>