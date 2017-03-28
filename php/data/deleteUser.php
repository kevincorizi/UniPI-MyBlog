<?php
	/*Script per l'eliminazione degli utenti*/
	require_once "../../config.php";
	require_once "../sessionManager.php";
	require_once "../lib.php";
	startSession();

	global $conn;

	$user = fetchUsers("SELECT * FROM user WHERE id=".$conn->secure($_GET["filter0"]))[0];
	
	/*Un amministratore può eliminare tutti gli utenti, un moderatore solo gli utenti semplici*/
	if($_SESSION['role'] == 'admin' || ($_SESSION['role'] == 'mod' && $user->role == 'user')){
		$result = $conn->query("DELETE FROM user WHERE id=".$user->id);
	}
	else{
		$result = false;
	}

	header("Content-Type: application/json");
	echo json_encode($result);
?>