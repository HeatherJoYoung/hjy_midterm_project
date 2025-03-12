<?php 
	require_once(__DIR__ . '/api/config/Database.php');

	try {

		$database = new Database();
		$db = $database->connect();

		require_once(__DIR__ . '/Router.php');

	} catch (Exception $e) {

		echo array('status'=>'connection error', 'message'=>$e->getMessage());
		
	}
?>