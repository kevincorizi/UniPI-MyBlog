<?php
	/*Script per la segnalazione di commenti*/
	require_once "../../config.php";
	require_once "../sessionManager.php";
	require_once "../lib.php";
	startSession();

	global $conn;

	$result = $conn->query("UPDATE comment SET reported = reported + 1 WHERE id = ".$conn->secure($_GET["filter0"]));

	header("Content-Type: application/json");
	echo json_encode($result);
?>