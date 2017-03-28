<?php
	/*Script per l'ottenimento dei post e per il loro ordinamento*/
	require_once "../../config.php";
	require_once "../sessionManager.php";
	require_once "../lib.php";
	startSession();

	global $conn;

	$tab = $conn->secure($_GET['filter0']);
	$start = $conn->secure($_GET['filter1']);
	$filter = $conn->secure($_GET['filter2']);
	$order = $conn->secure($_GET['filter3']);

	//Nel caso debba mostrare i post successivi a post già ordinati, utilizzo la funzione di 
	//ordinamento con un parametro START aggiuntivo
	//FILTER è definito solo se sono nel pannello di controllo
	if($order !== 'undefined'){
		$posts = sortPosts($filter, $order, $tab, $start);
	}
	//Altrimenti la utilizzo con la data come default
	else{
		switch($tab){
			case '': //allora carico per il postflow della index
				$posts = fetchPosts("SELECT * FROM post ORDER BY dateLastModified DESC LIMIT ".$start.",20");
				break;
			case 'all'; //carico quelli del pannello di controllo generici
				$posts = fetchPosts("SELECT * FROM post ORDER BY dateLastModified DESC LIMIT ".$start.",20");
				break;
			case 'yours': //carico quelli miei del pannello di controllo
				$posts = fetchPosts("SELECT * FROM post WHERE author=".$_SESSION["id"]." ORDER BY dateLastModified DESC LIMIT ".$start.",20");
				break;
			case 'search'://non esiste
				$keywords = explode(" ", $filter);
				$query = "SELECT * FROM post P LEFT OUTER JOIN tag T ON P.id = T.idpost
						WHERE (P.title LIKE '%".$keywords[0]."%' ";
				for($i = 1; $i < count($keywords); $i++){
					$query.="OR P.title LIKE '%".$keywords[$i]."%' ";
				}
				$query.=") OR (T.tag LIKE '%".$keywords[0]."%' ";
				for($i = 1; $i < count($keywords); $i++){
					$query.="OR T.tag LIKE '%".$keywords[$i]."%' ";
				}
				$query.=") OR P.text LIKE '%".$keywords[0]."%' ";
				for($i = 1; $i < count($keywords); $i++){
					$query.="OR P.text LIKE '%".$keywords[$i]."%' ";
				}
				$query.="GROUP BY P.id ORDER BY dateLastModified DESC LIMIT ".$start.",20;";
				$posts = fetchPosts($query);
				break;
			case 'category':
				$posts = fetchPosts("SELECT * FROM post WHERE status!='draft' AND category='".$filter."' ORDER BY dateLastModified DESC LIMIT ".$start.",20");
				break;
		}
	}

	for($i = 0; $i < count($posts); $i++){
		$posts[$i]->visited = count(fetchVisits("SELECT * FROM views WHERE idpost=".$posts[$i]->id));
		$posts[$i]->tags = fetchTags("SELECT * FROM tag WHERE idpost=".$posts[$i]->id);
		$posts[$i]->numberLikes = count(fetchLikes("SELECT * FROM `like` WHERE type='L' AND idpost=".$posts[$i]->id));
		$posts[$i]->numberDislikes = count(fetchLikes("SELECT * FROM `like` WHERE type='D' AND idpost=".$posts[$i]->id));
		$posts[$i]->comments = count(fetchComments("SELECT * FROM comment WHERE post=".$posts[$i]->id));
	}

	header("Content-Type: application/json");
	echo json_encode($posts);
	//echo json_last_error_msg();
?>