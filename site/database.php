<?php

#log errors
ini_set('log_errors', 1);
ini_set('error_log', dirname(__FILE__) . 'log.txt');
error_reporting(E_ALL);

	$server ='localhost'; //host
	$username = ''; //enter database username/pw
	$password = '';
	$database = 'mogen100_csgo1';

try{
	$conn = new PDO("mysql:host=$server;dbname=$database", $username, $password); //initiate connection

}catch(PDOException $e){
	die("Connection failed: " . $e->getMessage());
}

?>
