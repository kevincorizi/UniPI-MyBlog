<?php
	/*Script per la visualizzazione degli utenti nel pannello di controllo*/
	require_once DIR_OBJECTS."user.php";

	if(!isset($_GET['users']))
		redirect("../../panel.php");
	if($conn->secure($_GET["users"]) == "staff")
		$users = fetchUsers("SELECT * FROM user WHERE role!='user'");
	else if($conn->secure($_GET['users']) == 'all')
		$users = fetchUsers("SELECT * FROM user");
	else{
		errorMessage("Si è verificato un errore...", "La pagina che cerchi non esiste");
		die();
	}
					
	for($i = 0; $i < count($users); $i++){
		$users[$i]->posts = count(fetchPosts("SELECT * FROM post WHERE author=".$users[$i]->id));
		$users[$i]->comments = count(fetchComments("SELECT * FROM comment WHERE author=".$users[$i]->id));
		$users[$i]->likesSet = count(fetchLikes("SELECT * FROM `like` WHERE type='L' AND iduser=".$users[$i]->id));
		$users[$i]->dislikesSet = count(fetchLikes("SELECT * FROM `like` WHERE type='D' AND iduser=".$users[$i]->id));
	}

	for($i = 0; $i < count($users); $i++){
		$users[$i]->showCard();
	}
?>
