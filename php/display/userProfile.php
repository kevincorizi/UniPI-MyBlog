<?php
	/*Script per la visualizzazione di un profilo utente*/
	require_once DIR_OBJECTS."user.php";

	if(!isset($_GET["users"])){
		$userId = $_SESSION['id'];
	}
	else if(ctype_digit($conn->secure($_GET["users"])))
		$userId = $conn->secure($_GET['users']);
	else{
		errorMessage("Si è verificato un errore...", "La pagina che cerchi non esiste!");
		die();
	}
	if(isset(fetchUsers("SELECT * FROM user WHERE id=".$userId)[0])){
		$user = fetchUsers("SELECT * FROM user WHERE id=".$userId)[0];	

		$user->posts = count(fetchPosts("SELECT * FROM post WHERE author=".$userId));
		$user->comments = count(fetchComments("SELECT * FROM comment WHERE author=".$userId));
		$user->likesSet = count(fetchLikes("SELECT * FROM `like` WHERE type='L' AND iduser=".$userId));
		$user->dislikesSet = count(fetchLikes("SELECT * FROM `like` WHERE type='D' AND iduser=".$userId));

		$user->showProfile();
	}
	else{
		errorMessage("Si è verificato un errore...", "L'utente che cerchi non esiste!");
	}
?>