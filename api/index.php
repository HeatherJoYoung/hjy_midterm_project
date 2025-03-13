<?php 
	require_once(__DIR__ . '/config/Database.php');

	echo 'In index.php at root level!';

	try {

		$database = new Database();
		$db = $database->connect();

		$status = $db->getAttribute(PDO::ATTR_CONNECTION_STATUS);
		echo "Database connection status: " . $status;

		require_once(__DIR__ . '/Router.php');

	} catch (Exception $e) {

		echo array('status'=>'connection error', 'message'=>$e->getMessage());
		
	}
?>