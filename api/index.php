<?php 
	require_once(__DIR__ . '/config/Database.php');

	echo 'In index.php at root level!<br>';
	echo 'Request URI: ' . $_SERVER['REQUEST_URI'] . '<br>';
	echo 'SERVER HTTP HOST: ' . $_SERVER['HTTP_HOST'] . '<br>';
	echo 'DIR: ' . __DIR__ . '<br>';
	echo 'FILE: ' . __FILE__ . '<br>';

	$db = null;

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