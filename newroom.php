<?php

include 'database.php';

session_start();
if(!isset($_SESSION['username'])){
	header('Location: index.php');
}

if(isset($_POST['roomname'])){
	$roomname = stripslashes(strip_tags($_POST['roomname']));
}
if(isset($_POST['description'])){
	$description = stripslashes(strip_tags($_POST['description']));

}
if(isset($_POST['startingamount'])){
	$startingamount = stripslashes(strip_tags($_POST['startingamount']));
}

if(isset($_POST['roomname']) && isset($_POST['description']) && isset($_POST['startingamount'])){
	$createconn = $conn->prepare('INSERT INTO rooms (owner, name, description, date, start_amount) VALUES (?, ?, ?, ? ,?)');
	$createconn->bindParam(1, $_SESSION['username']);
	$createconn->bindParam(2, $roomname);
	$createconn->bindParam(3, $description);
	$createtime = time();
	$createconn->bindParam(4, $createtime);
	$createconn->bindParam(5, $startingamount);
	if($createconn->execute()){
		//done

		//$findroomconn = $conn->prepare('SELECT id FROM rooms WHERE owner=? SORT BY date DESC');
		//$findroomconn->bindParam(1, $_SESSION['username']);
		//$findroomconn->execute();
		//$idresults = $findroomconn->fetchAll();
		//foreach($idresults as $room){
			//header('Location: room.php?id=' . $room['id']); //redirect to room id
		//}
		header('Location: betting.php');
	}else{
		echo 'did not execute stmt';
		echo $_SESSION['username'] . $roomname . $description . $startingamount;
	}
}else{
	if(isset($roomname)){ echo 'missing values $roomname, $description, $startingamount'; }
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
		<span><a href="index.php" class="list-group-item active">Mog's CSGO</a></span>
	</div>
	<div class="jumbotron">
		<h1 class="display-1">Create a bet room</h1>
		<div align="left"><?php if(isset($_SESSION['username'])){ echo 'Signed in as '.$_SESSION['username'] . ' <a href="logout.php">Logout</a>'; }
		else{
			echo '<div align="right"><a href="login.php">Login</a></div>';
			} ?></div>


		<form action="newroom.php" method="POST">

			<div class="input-group" style="padding-top: 10px">
				<div class="input-group input-group-lg">
				  <span class="input-group-addon" id="sizing-addon1">@</span>
				  <input type="text" name="roomname" class="form-control" placeholder="Room name">
				</div>
				<div class="input-group"><br>
				  <input type="text" class="form-control" name="description" placeholder="Description">
				</div><br>
				<div class="input-group input-group-lg">
				  <span class="input-group-addon" id="sizing-addon1">$</span>
				  <input type="text" class="form-control" name="startingamount" placeholder="Starting amount">
				</div><br>
				<div class="input-group">
					<button type="submit" class="btn btn-outline-primary btn-lg">Create</button>
				</div>

			</div>

		</form>

	</div>


</div>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.0.0/jquery.min.js" integrity="sha384-THPy051/pYDQGanwU6poAc/hOdQxjnOEXzbT+OuUAFqNqFjL+4IGLBgCJC3ZOShY" crossorigin="anonymous"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/tether/1.2.0/js/tether.min.js" integrity="sha384-Plbmg8JY28KFelvJVai01l8WyZzrYWG825m+cZ0eDDS1f7d/js6ikvy1+X+guPIB" crossorigin="anonymous"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-alpha.4/js/bootstrap.min.js" integrity="sha384-VjEeINv9OSwtWFLAtmc4JCtEJXXBub00gtSnszmspDLCtC0I4z4nqz7rEFbIZLLU" crossorigin="anonymous"></script>
</body>

</html>