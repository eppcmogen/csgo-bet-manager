<?php

session_start();
ob_start();
include 'database.php';

if(isset($_GET['id'])){
	$id = stripslashes(strip_tags($_GET['id']));
}else{
	header('Location: betting.php');
}
if(isset($_GET['data'])){
	$data = stripslashes(strip_tags($_GET['data']));
}else{
	header('Location: betting.php');
}

if(!empty($data) && !empty($id)){

	echo $id . $data;
	$conndata = $conn->prepare('SELECT * FROM bettingdata WHERE creationtimestamp=? AND roomid=?');
	$conndata->bindParam(1, $data);
	$conndata->bindParam(2, $id);

	$conndata->execute();
	$array = $conndata->fetch(PDO::FETCH_ASSOC);
	if(count($array) > 0){

		$arrayid = $array['roomid'];
		echo $array['date'];
		$conn2 = $conn->prepare('SELECT * FROM rooms WHERE id=?');
		$conn2->bindParam(1, $arrayid);
		$conn2->execute();
		$conn2_data = $conn2->fetch(PDO::FETCH_ASSOC);
		if($conn2_data['id'] == $arrayid){
			if($conn2_data['owner'] == $_SESSION['username']){
				//this is the user's room

				$deleteconn = $conn->prepare('DELETE FROM bettingdata WHERE roomid=? AND creationtimestamp=?');
				$deleteconn->bindParam(1, $id);
				$deleteconn->bindParam(2, $data);
				if($deleteconn->execute()){
					echo 'done';
					$redirect = 'Location: room.php?id=' . $id;
					header($redirect);
				}
			}else{
				echo $conn2_data['owner'];
				echo 'Insufficient permissions';
				header('Location: betting.php');
			}

		}else{
			echo 'could not find room';
			header('Location: betting.php');
		}

	}else{
		echo 'could not find id and data in database';
		header('Location: betting.php');
	}


}else{
	header('Location: betting.php');
}

?>