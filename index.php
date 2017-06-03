<?php

session_start();
include 'database.php';

$newconn = $conn->prepare('INSERT INTO data (ip, timestamp) VALUES (?, ?)');
if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
    $ip = $_SERVER['HTTP_CLIENT_IP'];
} elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
    $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
} else {
    $ip = $_SERVER['REMOTE_ADDR'];
}
$newconn->bindParam(1, $ip);
//timestmap
$thistime = date('Y-M-d g:ia');
$newconn->bindParam(2, $thistime);
if($newconn->execute()){
}
else
{
	echo 'DB set error';
}

?>

<html>

<head>
	<title>Mog's Betting</title>
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-alpha.4/css/bootstrap.min.css">
	<link rel="stylesheet" href="styles.css">
	<meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta http-equiv="x-ua-compatible" content="ie=edge">
</head>

<body>
<div class="container">
<div class="roomheader">
		<span class="list-group-item active">Mog's CSGO</span>
	</div>
	<div class="jumbotron">
		<h1 class="display-1">Mog's CSGO</h1>
		<p class="lead">Making profits & competitive play easy</p>
		<div align="right"><?php if(isset($_SESSION['username'])){ echo $_SESSION['username'] . ' <a href="logout.php">Logout</a>'; }
		else{
			echo '<div align="right"><a href="login.php">Login</a></div>';
			} ?></div>
	</div>

	<center>
	<div><a href="betting.php" class="btn btn-primary">Betting</a> <a href="comp.php" class="btn btn-secondary">Competitive</a></div><br><br>
	<img src="hiko.jpg" class="img-fluid" alt="Hiko"></img>
	<small class="form-text text-muted">Hiko, Cloud9</small>
	</center>
</div>

<div class="footer">
</div>

</body>

</html>