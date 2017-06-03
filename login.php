<?php

session_start();
if(isset($_SESSION['username'])){
	header('Location: index.php');
}
include 'database.php';

if(isset($_POST['username']) && isset($_POST['pin'])){

	$username = stripslashes(strip_tags($_POST['username']));
	$pin = stripslashes(strip_tags($_POST['pin']));

	$checkuser = $conn->prepare('SELECT id, username, pin FROM users WHERE username=?');
	$checkuser->bindParam(1, $username);

	$checkuser->execute();
	$checkuserdata = $checkuser->fetch(PDO::FETCH_ASSOC);

	if(count($checkuserdata > 0) && $checkuserdata['username'] == $username){

		if(password_verify($pin, $checkuserdata['pin'])){

			$_SESSION['userid'] = $checkuserdata['id'];
			$_SESSION['username'] = $checkuserdata['username'];
			header('Location: index.php');

		}else{
			$message = "Invalid pin. It looks like you're trying to 'hack > now'";
		}

	}else{
		$message = "Couldn't find that user";
	}

}
?>

<html>

<head>
	<title>Mog's CSGO - Login</title>
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-alpha.4/css/bootstrap.min.css">
	<link rel="stylesheet" href="styles.css">
	<meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta http-equiv="x-ua-compatible" content="ie=edge">
</head>

<body>
<div class="container">
<div class="roomheader">
		<span><a href="index.php" class="list-group-item active">Mog's CSGO</a></span>
	</div>
	<div class="jumbotron">
		<h1 class="display-1">Mog's CSGO</h1>
		<p class="lead">Making profits & competitive play easy</p>
	</div>
	
	<div class="container">
		<h2>Login</h2> or <a href="register.php"><button class="btn btn-outline-primary btn-sm">Register</button></a>
		<?php if(isset($message)){ echo '<h4>' . $message . '</h4>'; } ?>
		<form action="login.php" method="POST">
			<div class="form-group">
				<label>Username</label>
				<input type="text" name="username" class="form-control" maxlength="10">
			</div>
			<div class="form-group">
				<label>Pin</label>
				<input type="password" name="pin" class="form-control" maxlength="4">
			</div>
			<div class="form-group">
				<button class="btn btn-outline-primary">Login</button>
			</div>
		</form>
</div>
</body>

</html>