<?php
	/*Script per la visualizzazione della lista di articoli in homepage*/
	require_once DIR_OBJECTS."post.php";

	if(isset($_GET["category"])){
		if(!isset(fetchCategories("SELECT * FROM category WHERE id=".$_GET['category'])[0])){
			errorMessage("Si è verificato un errore...", "La categoria che cerchi non esiste!");
			die();
		}
		$posts = fetchPosts("SELECT * FROM post WHERE status!='draft' AND category='".$conn->secure($_GET["category"])."' ORDER BY dateLastModified DESC LIMIT 20");

		$emptyText = "Non è ancora stato pubblicato alcun post per questa categoria!";
	}
	else if(isset($_GET["search"])){
		//Il match avviene in 3 casi
		//1) Il titolo contiene almeno una parola chiave
		//2) Il testo contiene almeno una parola chiave
		//3) I tag contengono almeno una parola chiave
		$keywords = explode(" ", $conn->secure($_GET["search"]));
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
			$query.="GROUP BY P.id ORDER BY dateLastModified DESC LIMIT 20;";
		$posts = fetchPosts($query);

		$emptyText = "Nessun articolo corrisponde alla tua ricerca, prova a utilizzare altre parole!";
	}
	else{
		$posts = fetchPosts("SELECT * FROM post WHERE status!='draft' ORDER BY dateLastModified DESC LIMIT 20");

		$emptyText = "Non è ancora stato pubblicato alcun post, riprova più tardi!";
	}
	
	if(count($posts) == 0){
		echo "<div class='emptyTarget'>".$emptyText."</div>";
	}

	for($i = 0; $i < count($posts); $i++){
		$posts[$i]->author = fetchUsers("SELECT * FROM user WHERE id=".$posts[$i]->author)[0];
		$posts[$i]->visited = count(fetchVisits("SELECT * FROM views WHERE idpost=".$posts[$i]->id));
		$posts[$i]->tags = fetchTags("SELECT * FROM tag WHERE idpost=".$posts[$i]->id);
		$posts[$i]->numberLikes = count(fetchLikes("SELECT * FROM `like` WHERE type='L' AND idpost=".$posts[$i]->id));
		$posts[$i]->numberDislikes = count(fetchLikes("SELECT * FROM `like` WHERE type='D' AND idpost=".$posts[$i]->id));
		$posts[$i]->comments = count(fetchComments("SELECT * FROM comment WHERE post=".$posts[$i]->id));
	}//poi vediamo le limit

	for($i = 0; $i < count($posts); $i++)
		$posts[$i]->showCard();

	if(count($posts) >= 3){
		echo "<button class='button roundButton' onclick=\"location.href='#'\"><img src='assets/icons/arrowUpWhite.png' alt='TOP'></button>";
	}

	if(count($posts) >= 20){
		echo "<button type='button' class='button largeButton' onclick=\"showMore()\">Mostra altro...</button>";
	}
?>
