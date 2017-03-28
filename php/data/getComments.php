<?php
	/*Script per l'ottenimento dei commenti e per il loro ordinamento*/
	require_once "../../config.php";
	require_once "../sessionManager.php";
	require_once "../lib.php";
	startSession();

	global $conn;

	$tab = $conn->secure($_GET['filter0']);
	$start = $conn->secure($_GET['filter1']);
	$filter = $conn->secure($_GET['filter2']);
	$order = $conn->secure($_GET['filter3']);

	if($filter !== 'undefined'){
		$comments = sortComments($filter, $order, $tab, $start);
	}
	else{
		switch($tab){
			case 'all'; //carico quelli del pannello di controllo generici
				$comments = fetchComments("SELECT * FROM comment ORDER BY dateComment DESC LIMIT ".$start.",20");
				break;
			case 'yours': //carico quelli miei del pannello di controllo
				$comments = fetchComments("SELECT * FROM comment WHERE post IN 
														(SELECT id
														FROM post
														WHERE author=".$_SESSION["id"].") ORDER BY dateComment DESC LIMIT ".$start.",20");
				break;
			case 'moderation':
				$comments = fetchComments("SELECT * FROM comment WHERE reported>=3 ORDER BY dateComment DESC LIMIT ".$start.",20");
				break;
		}
	}

	for($i = 0; $i < count($comments); $i++){
		$comments[$i]->author = fetchUsers("SELECT * FROM user WHERE id=".$comments[$i]->author)[0];
		$comments[$i]->post = fetchPosts("SELECT * FROM post WHERE id=".$comments[$i]->postId)[0];
		$comments[$i]->numberLikes = count(fetchLikes("SELECT * FROM `like` WHERE type='L' AND idcomment=".$comments[$i]->id));
		$comments[$i]->numberDislikes = count(fetchLikes("SELECT * FROM `like` WHERE type='D' AND idcomment=".$comments[$i]->id));
	}

	header("Content-Type: application/json");
	echo json_encode($comments);
	//echo json_last_error_msg();
?>