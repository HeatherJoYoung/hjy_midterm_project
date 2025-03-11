<?php 
	require_once(__DIR__ . '/api/config/Database.php');

	$database = new Database();
	$db = $database->connect();

	require_once(__DIR__ . '/Router.php');
?>