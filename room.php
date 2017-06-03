<?php

include 'database.php';

session_start();
if(isset($_GET['id'])){
	$roomid = stripslashes(strip_tags($_GET['id']));
}else{
	header('Location: betting.php');
}

if(isset($_GET['modify']) && $_GET['modify'] == 1){
	$modify = true;
}else if(isset($_GET['modify']) && $_GET['modify'] == 0){
	$modify = false;
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


	$read_room_data = $conn->prepare('SELECT * FROM bettingdata WHERE roomid=? ORDER BY date ASC');
	$read_room_data->bindParam(1, $roomid);
	$read_room_data->execute();
	$array_room_data = $read_room_data->fetchAll();

	$profit = 0;
	$money = $roomstartamount;
	$biggestwin = 0;
	$worstloss = 0;
	$streak = 0;
	$lastupdate = 0;

	foreach($array_room_data as $row){

		$lastupdate = $row['creationtimestamp'];

		$betmoney = $row['betamount'];
		$money -= $betmoney;
		if($row['win'] == 1){
			$money += $betmoney;
			$betmoney = $betmoney * $row['odds'];
			if($betmoney > $biggestwin){
				$biggestwin = $betmoney; //set best win
			}
			$money += $betmoney;
			$streak +=1;
		}else{
			if($worstloss < $betmoney){
				$worstloss = $betmoney; //set worst loss
			}
			$streak = 0;
		}
	}



}else{
	$error = 'This room does not exist';
}

?>

<html>
<head>
	<title>Mog's CSGO - <?php echo $roomname; ?></title>
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-alpha.4/css/bootstrap.min.css">
	<link rel="stylesheet" href="styles.css">
	<link href="https://maxcdn.bootstrapcdn.com/font-awesome/4.6.3/css/font-awesome.min.css" rel="stylesheet" integrity="sha384-T8Gy5hrqNKT+hzMclPo118YTQO6cYprQmhrYwIiQ/3axmI1hQomh7Ud2hPOy8SP1" crossorigin="anonymous">
	<meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta http-equiv="x-ua-compatible" content="ie=edge">
</head>

<body>
	<div>
	<div class="roomheader">
		<span><a href="betting.php" class="list-group-item active">Mog's CSGO</a></span>
		<span class="headeraccount"><?php if(isset($_SESSION['username'])){ echo $_SESSION['username'] . ' <a href="logout.php">Logout</a>'; } ?></span>
	</div>
	<div class="jumbotron bg-csgo1">
		<div class="container">
		<div>
		<h1 class="display-4"><?php echo $roomname; ?></h1>
		<p class="lead"><?php echo $roomdescription; ?></p>
		</div>
		<div>Created by <?php echo $roomowner; ?></div>
		<div>Last updated <b><?php echo date('D M d Y', $lastupdate + 0) . '</b> <i>at ' . date('g:i a', $lastupdate + 0); ?></div>
	<?php if(isset($error)){
		echo '<h2>' . $error . '</h2>';
	} ?>
	</div></div>
	<center><div class="container">
		<div class="row">
			<div class="col-sm-4 betoverview">
				Bank (total)<br>
				<h1 class="display-4">$<?php echo $money ?></h1>
			</div>
			<div class="col-sm-4 betoverview">
				Actual Profit<br>
				<h1 class="display-4"><?php if($money > $roomstartamount){ echo '+$'.($money-$roomstartamount); }else{ echo '-$'.($roomstartamount - $money);} ?></h1>
			</div>
			<div class="col-sm-4 betoverview">
				Best Win<br>
				<h1 class="display-4">$<?php echo $biggestwin; ?></h1>
			</div>
		</div>

		<div class="row">
			<div class="col-sm-4 betoverview">
				Win Streak<br>
				<h1 class="display-4"><?php echo $streak; ?></h1>
			</div>
			<div class="col-sm-4 betoverview">
				Minimuim Wage Hours Earned<br>
				<h1 class="display-4"><?php echo round($money / 11.40, 0); ?>h</h1>
				@ $11.40/h
			</div>
			<div class="col-sm-4 betoverview">
				Worst Loss<br>
				<h1 class="display-4"><?php echo '-$'.$worstloss; ?></h1>
			</div>
		</div>
	</center>

	<div class="container">
		<div class="container">

	<div>
		<blockquote class="blockquote">Starting amount: $<?php echo $roomstartamount; ?></blockquote>
	</div>

	<?php if(strtolower($roomowner) == strtolower($_SESSION['username'])){
		echo '<form action="room.php" method="GET"><div align="right">
		<a href="addgame.php?id=' . $roomid . '"><button type="button" class="btn btn-outline-primary">+ New Bet</button></a> ';

		if(isset($modify) && $modify == true){
			echo '<input type="hidden" name="id" value="' . $roomid . '"><button type="submit" name="modify" value="0" class="btn btn-danger">Modify</button></div></form>';
		}else if(isset($modify) && $modify == false){
			echo '<input type="hidden" name="id" value="' . $roomid . '"><button type="submit" name="modify" value="1" class="btn btn-outline-danger">Modify</button></div></form>';
		}else{
			echo '<input type="hidden" name="id" value="' . $roomid . '"><button type="submit" name="modify" value="1" class="btn btn-outline-danger">Modify</button></div></form>';
		}

	} ?></div>
	<br><table class="table table-hover">
		<thead>
			<tr>
				<th>Date</th>
				<th>Match</th>
				<th>Bet</th>
				<th>Odds</th>
				<th>Diff</th>
				<th>Bank</th>
			</tr>
		</thead>
	</tbody>
	<?php

		$read_room_data = null;
		$read_room_data = $conn->prepare('SELECT * FROM bettingdata WHERE roomid=? ORDER BY creationtimestamp ASC');
		$read_room_data->bindParam(1, $roomid);
		$read_room_data->execute();
		$array_room_data = $read_room_data->fetchAll();

		$money = $roomstartamount;
		$moneybeforesub = $money;
		foreach($array_room_data as $row){

			$money -= $row['betamount'];
			if($row['win'] == 1){
				$money += $row['betamount'];
				$money += ($row['betamount'] * $row['odds']);
			}else{
				//already removed money
			}

			if($row['win'] == 1){
				echo '<tr class="bg-success table-hover">';
			}else{
				echo '<tr class="bg-danger table-hover">';
			}

			echo '<td>';
			
			if(isset($modify) && $modify == true){
				echo '<a href="deletegame.php?id=' . $row['roomid'] . '&data=' . $row['creationtimestamp'] . '"><i class="fa fa-minus-circle" style="font-size: 110%; color: #d50000" aria-hidden="true"></i></a> ';
			}
			echo  $row['date'].'</td>';
			echo '<td><b>'.$row['team1'].'</b> vs ' . $row['team2'] . '</td>';
			echo '<td>$'. round($row['betamount'], 2).'</td>';
			echo '<td>'.$row['odds'].'</td>';

			echo '<td>'; //difference
			if($row['win'] == 1){
				echo "+";
				echo ($row['betamount'] * $row['odds']);
			}else{
				echo "-" . $row['betamount'];
			}
			echo '</td>';
			echo '<td>$'.$money.'</td>'; //bank

			echo '</tr>';
		}

	?>
	</tbody>
	</table>
	</div>
	</div>

</div> <!--end of countainer -->

<div class="footer">

</div>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.0.0/jquery.min.js" integrity="sha384-THPy051/pYDQGanwU6poAc/hOdQxjnOEXzbT+OuUAFqNqFjL+4IGLBgCJC3ZOShY" crossorigin="anonymous"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/tether/1.2.0/js/tether.min.js" integrity="sha384-Plbmg8JY28KFelvJVai01l8WyZzrYWG825m+cZ0eDDS1f7d/js6ikvy1+X+guPIB" crossorigin="anonymous"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-alpha.4/js/bootstrap.min.js" integrity="sha384-VjEeINv9OSwtWFLAtmc4JCtEJXXBub00gtSnszmspDLCtC0I4z4nqz7rEFbIZLLU" crossorigin="anonymous"></script>
</body>

</html>