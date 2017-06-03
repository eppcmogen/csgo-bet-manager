<?php

#log errors
ini_set('log_errors', 1);
ini_set('error_log', dirname(__FILE__) . 'log.txt');
error_reporting(E_ALL);

	$server ='localhost';
	$username = 'root';
	$password = '';
	$database = 'csgo_1';

try{
	$conn = new PDO("mysql:host=$server;dbname=$database", $username, $password);

}catch(PDOException $e){
	die("Connection failed: " . $e->getMessage());
}

?>