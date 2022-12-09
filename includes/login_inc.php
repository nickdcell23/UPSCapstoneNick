<?php 

include_once "db_inc.php";
include_once "functions_inc.php";
if(isset($_POST['login'])){
	$email = $_POST['email'];
	$password = $_POST['password'];
	
	if(!isValidEmail($email)){
		header("Location: ../login.php?error=emailNotValid");
	 	exit();
	}
	if(!isInUpsDomain($email)){
		header("Location: ../login.php?error=notInUpsDomain");
	 	exit();
	}
	//query
	$sql = "SELECT * FROM `user` WHERE email='$email';";
	$result = mysqli_query($conn,$sql);
	if($result){
		$row = mysqli_fetch_assoc($result);
		if(password_verify($password, $row['password'])){
			session_start();
			$_SESSION['email'] = $email;
			$_SESSION['azureemail'];
			header("Location: ../dashboard.php");
			exit();
		}
		else{
			header("Location: ../login.php?error=passwordIncorrect");
			exit();
		}
	}
	else{
		header("Location: ../login.php?error=recordUnavailable");
		exit();
	}
}
else{
	echo "data is not received<br>";
	header("location: ../login.php");
	exit();
}