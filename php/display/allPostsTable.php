<?php
	/*Script per la visualizzazione tabulare degli articoli*/
	require_once DIR_OBJECTS."post.php";
	if(!isset($_GET['posts']) || $_GET['posts'] != 'all'){
		redirect("../../panel.php");
	}
	if(isset($_POST['sort']))
		$posts = sortPosts($conn->secure($_POST['filter']), $conn->secure($_POST['order']), $conn->secure($_GET['posts']));
	else	
		$posts = fetchPosts("SELECT * FROM post ORDER BY dateLastModified DESC LIMIT 20");

	if(count($posts) == 0)
		echo "<div class='emptyTarget'>Non è stato ancora pubblicato nessun post!</div>";
	else{
		showPostFilterPanel();

		echo "<table id='allPostTable'>";
		echo "<thead>";
		echo "<tr class='tableHeader postTableHeader allPostTableHeader'>";
		echo "<th>Titolo</th>
			  <th>Data</th>
			  <th>Autore</th>
			  <th><img src='assets/icons/commentWhite.png' alt='Commenti'></th>
			  <th><img src='assets/icons/likeWhite.png' alt='Mi piace'></th>
			  <th><img src='assets/icons/dislikeWhite.png' alt='Non mi piace'></th>
			  <th><img src='assets/icons/tagsWhite.png' alt='Tags'></th>";
		echo "</tr>";
		echo "</thead>";
		echo "<tfoot>";
		echo "</tfoot>";

		echo "<tbody>";
		for($i = 0; $i < count($posts); $i++){
			$posts[$i]->author = fetchUsers("SELECT * FROM user WHERE id=".$posts[$i]->author)[0];
			$posts[$i]->visited = count(fetchVisits("SELECT * FROM views WHERE idpost=".$posts[$i]->id));
			$posts[$i]->tags = fetchTags("SELECT * FROM tag WHERE idpost=".$posts[$i]->id);
			$posts[$i]->numberLikes = count(fetchLikes("SELECT * FROM `like` WHERE type='L' AND idpost=".$posts[$i]->id));
			$posts[$i]->numberDislikes = count(fetchLikes("SELECT * FROM `like` WHERE type='D' AND idpost=".$posts[$i]->id));
			$posts[$i]->comments = count(fetchComments("SELECT * FROM comment WHERE post=".$posts[$i]->id));

			$posts[$i]->showRow("all");
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