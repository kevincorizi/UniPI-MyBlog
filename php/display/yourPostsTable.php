<?php
	/*Script per la visualizzazione tabulare degli articoli di un utente*/
	require_once DIR_OBJECTS."post.php";
	if(!isset($_GET['posts']) || $_GET['posts'] != 'yours'){
		redirect("../../panel.php");
	}
	if(isset($_POST['sort']))
		$posts = sortPosts($conn->secure($_POST['filter']), $conn->secure($_POST['order']), $conn->secure($_GET['posts']));
	else
		$posts = fetchPosts("SELECT * FROM post WHERE author=".$_SESSION["id"]." ORDER BY dateLastModified DESC LIMIT 20");

	if(count($posts) == 0)
		echo "<div class='emptyTarget'>Non hai ancora pubblicato nessun post!</div>";
	else{
		showPostFilterPanel();

		echo "<table id='yourPostTable'>";
		echo "<thead>";
		echo "<tr class='tableHeader postTableHeader yourPostTableHeader'>";
		echo "<th>Titolo</th>
	          <th>Data</th>
			  <th><img src='assets/icons/commentWhite.png' alt='Commenti'></th>
			  <th><img src='assets/icons/likeWhite.png' alt='Mi piace'></th>
			  <th><img src='assets/icons/dislikeWhite.png' alt='Non mi piace'></th>
			  <th><img src='assets/icons/tagsWhite.png' alt='Tags'></th>
			  <th><img src='assets/icons/editWhite.png' alt='Tags'></th>";
		echo "</tr>";
		echo "</thead>";
		echo "<tfoot>";
		echo "</tfoot>";

		echo "<tbody>";
		for($i = 0; $i < count($posts); $i++){
			$posts[$i]->visited = count(fetchVisits("SELECT * FROM views WHERE idpost=".$posts[$i]->id));
			$posts[$i]->tags = fetchTags("SELECT * FROM tag WHERE idpost=".$posts[$i]->id);
			$posts[$i]->numberLikes = count(fetchLikes("SELECT * FROM `like` WHERE type='L' AND idpost=".$posts[$i]->id));
			$posts[$i]->numberDislikes = count(fetchLikes("SELECT * FROM `like` WHERE type='D' AND idpost=".$posts[$i]->id));
			$posts[$i]->comments = count(fetchComments("SELECT * FROM comment WHERE post=".$posts[$i]->id));

			$posts[$i]->showRow("user");
		}
		echo "</tbody>";
		echo "</table>";

		if(count($posts) == 20){
			if(isset($_POST['sort']))
				echo "<button type='button' class='button largeButton' onclick=\"showMore('".$conn->secure($_POST['filter'])."','".$conn->secure($_POST['order'])."')\">Mostra altro...</button>";
			else
				echo "<button type='button' class='button largeButton' onclick='showMore()'>Mostra altro...</button>";
		}
	}
?>