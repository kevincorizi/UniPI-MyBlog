<?php
	/*Script per l'eliminazione dei commenti*/
	require_once "../../config.php";
	require_once "../sessionManager.php";
	require_once "../lib.php";
	startSession();

	global $conn;

	$commentId = $conn->secure($_GET["filter0"]);
	$comment = fetchComments("SELECT * FROM comment WHERE id=".$commentId)[0];

	/*Il commento può essere eliminato o da un moderatore in caso sia in moderazione, o dall'autore stesso*/
	if(($_SESSION['role'] != 'user' && $comment->numberReported >= 3) || $_SESSION['id'] == $comment->author){
		$result = $conn->query("DELETE FROM comment WHERE id=".$comment->id);
	}
	else{
		$result = false;
	}

	header("Content-Type: application/json");
	echo json_encode($result);
?>