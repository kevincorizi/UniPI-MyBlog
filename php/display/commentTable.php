<?php
	/*Script per la visualizzazione tabulare dei commenti*/
	require_once DIR_OBJECTS."comment.php";

	if(!isset($_GET['comments']))
		redirect("../../panel.php");

	if($_GET['comments'] != 'all' && $_GET['comments'] != 'yours' && $_GET['comments'] != 'moderation'){
		errorMessage("Si è verificato un errore...", "La pagina che cerchi non esiste!");
		die();
	}

	if(isset($_POST['sort']))
		$comments = sortComments($conn->secure($_POST['filter']), $conn->secure($_POST['order']), $conn->secure($_GET['comments']));
	else{
		if($_GET["comments"] == "yours")
			$comments = fetchComments("SELECT * FROM comment 
										WHERE post IN (SELECT id
														FROM post
														WHERE author=".$_SESSION["id"].") ORDER BY dateComment DESC LIMIT 20");

		else if($_GET["comments"] == "moderation")
			$comments = fetchComments("SELECT * FROM comment WHERE reported>=3 ORDER BY dateComment DESC LIMIT 20");
		else
			$comments = fetchComments("SELECT * FROM comment ORDER BY dateComment DESC LIMIT 20");
	}

	if(count($comments) == 0){
		if($conn->secure($_GET["comments"]) == "yours")
			echo "<div class='emptyTarget'>Nessuno ha ancora commentato i tuoi post!</div>";
		else if($conn->secure($_GET["comments"]) == "moderation")
			echo "<div class='emptyTarget'>Nessun commento necessita di moderazione!</div>";
		else
			echo "<div class='emptyTarget'>Nessun utente ha ancora commentato!</div>";
	}
	else{
		showCommentFilterPanel();

		echo "<table id='commentTable'>";
		echo "<thead>";
		echo "<tr class='tableHeader commentTableHeader'>";
		echo "<th>Testo</th>
	          <th>Post</th>
			  <th>Autore</th>
			  <th>Data</th>
			  <th><img src='assets/icons/likeWhite.png' alt='Mi piace'></th>
			  <th><img src='assets/icons/dislikeWhite.png' alt='Non mi piace'></th>
			  <th><img src='assets/icons/reportWhite.png' alt='Segnalazioni'></th>";
		echo "</tr>";
		echo "</thead>";
		echo "<tfoot>";
		echo "</tfoot>";

		echo "<tbody>";
		for($i = 0; $i < count($comments); $i++){
			$comments[$i]->author = fetchUsers("SELECT * FROM user WHERE id=".$comments[$i]->author)[0];
			$comments[$i]->post = fetchPosts("SELECT * FROM post WHERE id=".$comments[$i]->postId)[0];
			$comments[$i]->numberLikes = count(fetchLikes("SELECT * FROM `like` WHERE type='L' AND idcomment=".$comments[$i]->id));
			$comments[$i]->numberDislikes = count(fetchLikes("SELECT * FROM `like` WHERE type='D' AND idcomment=".$comments[$i]->id));

			$comments[$i]->showRow();
		}
		echo "</tbody>";
		echo "</table>";

		if(count($comments) == 20){
			if(isset($_POST['sort']))
				echo "<button type='button' class='button largeButton' onclick=\"showMore('".$conn->secure($_POST['filter'])."','".$conn->secure($_POST['order'])."')\">Mostra altro...</button>";
			else
				echo "<button type='button' class='button largeButton' onclick='showMore()'>Mostra altro...</button>";
		}
	}
?>