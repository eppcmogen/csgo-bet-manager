<?php

include 'database.php';

session_start();

if(!isset($_SESSION['username'])){
	header("Location: login.php");
}

if(isset($_GET['id'])){
	$roomid = stripslashes(strip_tags($_GET['id']));
}

if(isset($_POST['id'])){
	$roomid = stripslashes(strip_tags($_POST['id']));
}


if(!isset($roomid)){
	//die('empty room');
	header("Location: betting.php");
}

$dataconn = $conn->prepare('SELECT * FROM rooms WHERE id=?');
$dataconn->bindParam(1, $roomid);
$dataconn->execute();
$roomdata = $dataconn->fetch(PDO::FETCH_ASSOC);
if(count($roomdata) > 0 && $roomdata['id'] == $roomid){
	$roomname = $roomdata['name'];
	$roomowner = $roomdata['owner'];
	$roomdescription = $roomdata['description'];
	$roomdate = $roomdata['date'];
	$roomstartamount = $roomdata['start_amount'];
}

if($_SESSION['username'] != $roomowner){
	//die($_SESSION['username'] . '/' . $roomowner);
	header("Location: room.php?id=" . $roomid);
}


if(isset($_POST['betamount']) && isset($_POST['date']) && isset($_POST['team1']) && isset($_POST['team2']) && isset($_POST['odds'])){


	$insertdata = $conn->prepare('INSERT INTO bettingdata (roomid, date, team1, team2, odds, win, betamount, creationtimestamp) VALUES (?, ?, ?, ?, ?, ?, ?, ?)');
	$insertdata->bindParam(1, $roomid);
	$newdate = stripslashes(strip_tags($_POST['date']));
	$insertdata->bindParam(2, $newdate);
	$team1 = stripslashes(strip_tags($_POST['team1']));
	$team2 = stripslashes(strip_tags($_POST['team2']));
	$insertdata->bindParam(3, $team1);
	$insertdata->bindParam(4, $team2);
	$odds = stripslashes(strip_tags($_POST['odds']));
	$insertdata->bindParam(5, $odds);

	if(isset($_POST['win'])){
		$value1 = '1';
		$insertdata->bindParam(6, $value1);
	}else if(isset($_POST['lose'])){
		$value1 = '0';
		$insertdata->bindParam(6, $value1);
	}else{
		die('win/loss not recorded');
	}
	$betamount_new = stripslashes(strip_tags($_POST['betamount']));
	$insertdata->bindParam(7, $betamount_new);
	$thetime = time();
	$insertdata->bindParam(8, $thetime);

	if($insertdata->execute()){
		$redirect = 'Location: room.php?id=' . $roomid;
		header($redirect);
	}else{
		//die('did not execute @ pdo');
	}

}else{
	//echo('missing an input');
}
?>
<html>

<head>
	<title>Mog's CSGO - Add a game</title>
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-alpha.4/css/bootstrap.min.css">
	<link rel="stylesheet" href="styles.css">
	<meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta http-equiv="x-ua-compatible" content="ie=edge">
</head>

<body>

<div class="container">
	<div class="roomheader">
		<span><a href="betting.php" class="list-group-item active">Mog's CSGO</a></span>
		<span class="headeraccount"><?php if(isset($_SESSION['username'])){ echo $_SESSION['username'] . ' <a href="logout.php">Logout</a>'; } ?></span>
	</div>
	<div class="jumbotron">
		<div>
		<h1 class="display-4"><?php echo $roomname; ?></h1>
		<p class="lead"><?php echo $roomdescription; ?></p>
		</div>
	<?php if(isset($error)){
		echo '<h2>' . $error . '</h2>';
	} ?>
	</div>
	
	<div class="container">
		<div><h1 class="display-4">Add a bet</h1></div>

		<form style="padding-top: 20px" method="POST" action="addgame.php">
			<div class="form-group">
			<div class="input-group">
				<div class="input-group-addon bg-success">$</div>
			      <input type="text" class="form-control form-control-lg" name="betamount" placeholder="Amount">
			      <div class="input-group-addon">.00</div>
			</div>
			</div>
			<div class="form-group">
				<div class="input-group">
					<div class="input-group-addon ">Date</div>
					<input type="date" class="form-control" value="<?php echo date('Y-m-d'); ?>" name="date">
				</div>
			</div>
			<div class="form-group">
				<div class="input-group">
					<div class="input-group-addon bg-success">Team #1</div>
					<input type="text" class="form-control" placeholder="Hiko" name="team1">
				</div>
			</div>
			<div class="form-group">
					<div class="input-group">
					<div class="input-group-addon bg-danger">Team #2</div>
					<input type="text" class="form-control" placeholder="Blue Jays" name="team2">
				</div>
			</div>
			<div class="form-group">
					<div class="input-group">
					<div class="input-group-addon">Odds</div>
					<input type="text" class="form-control form-control-lg" placeholder="1.99" name="odds">
					<div class="input-group-addon">Decimal</div>
				</div>

			</div>
			<div class="form-group">
					<input type="hidden" name="id" value="<?php echo $roomid; ?>">
					<button type="submit" class="btn btn-outline-primary" name="win">My team rekt1!!</button>
					<button type="submit" class="btn btn-outline-primary" name="lose">Lost :(</button>
			</div>
		</form>

	</div>

</div> <!--container -->
	
<div class="footer"></div>


<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.0.0/jquery.min.js" integrity="sha384-THPy051/pYDQGanwU6poAc/hOdQxjnOEXzbT+OuUAFqNqFjL+4IGLBgCJC3ZOShY" crossorigin="anonymous"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/tether/1.2.0/js/tether.min.js" integrity="sha384-Plbmg8JY28KFelvJVai01l8WyZzrYWG825m+cZ0eDDS1f7d/js6ikvy1+X+guPIB" crossorigin="anonymous"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-alpha.4/js/bootstrap.min.js" integrity="sha384-VjEeINv9OSwtWFLAtmc4JCtEJXXBub00gtSnszmspDLCtC0I4z4nqz7rEFbIZLLU" crossorigin="anonymous"></script>
</div>