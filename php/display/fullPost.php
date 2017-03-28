<?php
	/*Script per la visualizzazione dell'articolo a pagina piena*/
	require_once DIR_OBJECTS."post.php";

	if(!isset($_GET['post']))
		redirect("../../index.php");
	if(ctype_digit($conn->secure($_GET["post"])))
		$postId = $conn->secure($_GET["post"]);
	else{
		errorMessage("Si è verificato un errore...", "L'articolo che cerchi non esiste!");
		die();
	}

	if(!isset(fetchPosts("SELECT * FROM post WHERE id=".$postId)[0])){
		errorMessage("Si è verificato un errore...", "L'articolo che cerchi non esiste!");
		die();
	}
	
	$post = fetchPosts("SELECT * FROM post WHERE id=".$postId)[0];

	if($post->status != 'published' && (!isset($_SESSION['role']) || $_SESSION['role'] == 'user')){
		errorMessage("Si è verificato un errore...", "Non sei autorizzato a visualizzare questo post perchè è ancora in fase di lavorazione. Riprova più tardi!");
		die();
	}
	else{
		addVisit($postId);
		$post->tags = fetchTags("SELECT * FROM tag WHERE idpost=".$post->id);
		$post->likes = fetchLikes("SELECT * FROM `like` WHERE idpost=".$post->id);
		$post->comments = fetchComments("SELECT * FROM comment WHERE post=".$post->id." ORDER BY dateComment DESC");
		$post->visited = fetchVisits("SELECT * FROM views WHERE idpost=".$post->id);
		$post->numberLikes = count(fetchLikes("SELECT * FROM `like` WHERE type='L' AND idpost=".$post->id));
		$post->numberDislikes = count(fetchLikes("SELECT * FROM `like` WHERE type='D' AND idpost=".$post->id));
		$post->author = fetchUsers("SELECT * FROM user WHERE id=".$post->author)[0];

		for($i = 0; $i < count($post->comments); $i++){
			$post->comments[$i]->author = fetchUsers("SELECT * FROM user WHERE id=".$post->comments[$i]->author)[0];
			$post->comments[$i]->likes = fetchLikes("SELECT * FROM `like` WHERE idcomment=".$post->comments[$i]->id);
			$post->comments[$i]->numberLikes = count(fetchLikes("SELECT * FROM `like` WHERE type='L' AND idcomment=".$post->comments[$i]->id));
			$post->comments[$i]->numberDislikes = count(fetchLikes("SELECT * FROM `like` WHERE type='D' AND idcomment=".$post->comments[$i]->id));
		}

		$post->showFull();
	}
?>

