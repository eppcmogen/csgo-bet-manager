<?php

session_start();
if(isset($_SESSION['username'])){
	header('Location: index.php');
}

include 'database.php';

if(isset($_POST['username']) && isset($_POST['pin'])){

	$username = strip_tags(stripslashes($_POST['username']));
	$pin = strip_tags(stripslashes($_POST['pin']));
	$message = '';

		if(strlen($pin) == 4){
			//create user

			//check if username exists already
			$checkexists = $conn->prepare('SELECT username FROM users WHERE username=?');
			$checkexists->bindParam(1, $username);
			$checkexists->execute();
			$existsdata = $checkexists->fetch(PDO::FETCH_ASSOC);
			if($existsdata['username'] == $username){ $message= "Boi this username exists"; }else{

				//username does not exist
				$pin = password_hash($pin, PASSWORD_BCRYPT);

				$createuserdb = $conn->prepare('INSERT INTO users (username, pin, ip) VALUES (?, ?, ?)');
				$createuserdb->bindParam(1, $username);
				$createuserdb->bindParam(2, $pin);
				$createuserdb->bindParam(3, $_SERVER['REMOTE_ADDR']);

				if($createuserdb->execute() ){
					//created, goto login
					header('Location: login.php');
				}else{
					$message = 'Could not create user @ execute';
				}
			}
		}else{
			$message = "Your pin has to be 4 characters long <b><u>boi</b></u>";
		}
}

?>

<html>

<head>
	<title>Mog's CSGO - Register</title>
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
		<h2>Create a new account</h2> or <a href="login.php"><button class="btn btn-outline-primary btn-sm">Login</button></a>
		<?php if(isset($message)){ echo '<h4>' . $message . '</h4>'; } ?>
		<form action="register.php" method="POST">
			<div class="form-group">
				<label>Username</label>
				<input type="text" name="username" class="form-control" maxlength="10">
				<small class="form-text text-muted">Cupgang will see this username</small>
			</div>
			<div class="form-group">
				<label>Pin</label>
				<input type="password" name="pin" class="form-control" maxlength="4" autocomplete="off">
				<small class="form-text text-muted">Maximum 4 digits. It's encrypted as in I can't hack it at all.</small>
			</div>
			<div class="form-group">
				<button class="btn btn-outline-primary">Register</button>
			</div>
		</form>
	</div>

</div>
</body>

</html>