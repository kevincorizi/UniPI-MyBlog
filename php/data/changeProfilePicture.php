<?php
	/*Script per la gestione dell'immagine del profilo*/
	require_once "../../config.php";
	require_once "../sessionManager.php";
	require_once "../lib.php";
	startSession();

	global $conn;

	if(isset($_POST["changePicture"])){
		global $conn;
		$newPicture = $conn->secure($_POST["newPicture"]);

		/*Controlla se l'URL inserito è valido*/
		if(getimagesize($newPicture)){
			$query = "UPDATE user SET profilePicture='".htmlspecialchars($newPicture)."' WHERE id=".$_SESSION['id'];
			$conn->query($query);
			/*Ricarico la sessione per aggiorname l'immagine*/
			$user = fetchUsers("SELECT * FROM user wHERE id=".$_SESSION['id']);
			sessionFields($user);
			redirect(explode("?", $_SERVER['HTTP_REFERER'])[0]);
		}
		else{
			redirect(explode("?", $_SERVER['HTTP_REFERER'])[0]."?error=invalidpicture");
		}
	}
?>