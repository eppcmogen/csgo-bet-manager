<?php

include 'database.php';

session_start();
if(!isset($_SESSION['username'])){
	header('Location: login.php');
}


$loaddata = $conn->prepare('SELECT * FROM rooms');
$loaddata->execute();
$data = $loaddata->fetchAll();

$loadrecentbets = $conn->prepare('SELECT * FROM bettingdata ORDER BY creationtimestamp DESC LIMIT 8');
$loadrecentbets->execute();
$recentbets = $loadrecentbets->fetchAll();

//var_dump($recentbets);

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
		<h1 class="display-1">Mog's CSGO</h1>
		<p class="lead">Making profits & competitive play easy</p>
		<div align="right"><?php if(isset($_SESSION['username'])){ echo $_SESSION['username'] . ' <a href="logout.php">Logout</a>'; }
		else{
			echo '<div align="right"><a href="login.php">Login</a></div>';
			} ?></div>
	</div>

<div class="list-group">
		<a href="newroom.php" class="list-group-item list-group-item-action active">
    		<h5 class="list-group-item-heading">+New Bet Room</h5>
  		</a>
<?php
foreach ($data as $room){
	echo '<a href="room.php?id=' . $room['id'] . '" class="list-group-item list-group-item-action" style="">
	    <h4 class="list-group-item-heading">' .
	    $room['name'].
	    '</h4>
	    <p class="list-group-item-text">'.
	    $room['description'].
	    '<p align="right" class="">'.
	    date('M dS', $room['date'] + 0) . ' @ ' . date('g:ia', $room['date'] + 0) .
	    '</p></a>';
}?>

</div>

<div class="list-group" style="margin-top: 20px;">
	<div class="list-group-item list-group-item-action bg-success">
		<h5>Recently placed</h5>
	</div>
	<!--<a href="room.php?u=6" class="list-group-item list-group-item-action">
		<p><span class="playername">@kimchikeane:</span> $20 on Astralis</p>
		<div align="right" class="teamname"><span class="bettime">5 minutes ago</span> Astralis VS. Cloud9</span></div>
	</a> -->
	<?php 

	foreach($recentbets as $bet){

		$getownername = null;
		$getownername = $conn->prepare('SELECT id, owner FROM rooms WHERE id=?');
		$getownername->bindParam(1, $bet['roomid']);
		$getownername->execute();
		$data = $getownername->fetch(PDO::FETCH_ASSOC);

		//var_dump($data);

		$bet_time = $bet['creationtimestamp'];
		$time_diff = time() - $bet_time;
		//echo $bet_time . '|' . $time_diff;

		if($time_diff >= 0 && $time_diff < 60){
			$timestring = $time_diff . ' seconds ago';
		}else if($time_diff >= 60 && $time_diff < 120){
			$timestring = '1 minute ago';
		}else if($time_diff >= 120 && $time_diff < 3600){
			$timestring = round(($time_diff / 60), 0) . ' minutes ago';
		}else if($time_diff >= 3600 && $time_diff < 7200){
			$timestring = '1 hour ago';
		}else if($time_diff >= 7200 && $time_diff < 172800){
			$timestring = round((($time_diff / 60) / 60), 0) . ' hours ago';
		}else{
			$timestring = round(((($time_diff / 60) / 60) / 24), 0) . ' day(s) ago';
		}

		echo '<a href="room.php?id=' . $bet['roomid'] . '" class="list-group-item list-group-item-action">';
		echo '<p><span class="playername">@' . $data['owner'] .':</span> $' . $bet['betamount'] . ' on ' . $bet['team1'] . '</p>';
		echo '<div align="right" class="teamname"><span class="bettime">' . $timestring . '</span> ' . $bet['team1'] . ' vs. ' . $bet['team2'] . '</span></div>';
		echo '</a>';
	}

	?>
</div>

</div>

<div class="footer"></div>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.0.0/jquery.min.js" integrity="sha384-THPy051/pYDQGanwU6poAc/hOdQxjnOEXzbT+OuUAFqNqFjL+4IGLBgCJC3ZOShY" crossorigin="anonymous"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/tether/1.2.0/js/tether.min.js" integrity="sha384-Plbmg8JY28KFelvJVai01l8WyZzrYWG825m+cZ0eDDS1f7d/js6ikvy1+X+guPIB" crossorigin="anonymous"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-alpha.4/js/bootstrap.min.js" integrity="sha384-VjEeINv9OSwtWFLAtmc4JCtEJXXBub00gtSnszmspDLCtC0I4z4nqz7rEFbIZLLU" crossorigin="anonymous"></script>
</body>

</html>