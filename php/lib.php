<?php
	/*Funzioni di utilità utilizzate da più file PHP*/
	require_once 'databaseManager.php';
	require_once DIR_OBJECTS.'post.php';
	require_once DIR_OBJECTS.'like.php';
	require_once DIR_OBJECTS.'comment.php';
	require_once DIR_OBJECTS.'user.php';
	require_once DIR_OBJECTS.'visit.php';
	require_once DIR_OBJECTS.'category.php';

	/*Funzione di redirezione con svuotamento cache*/
	function redirect($url){
		header('Location:'.$url, true, 303);
	}

	/*Funzione che inizializza i campi di $_SESSION con i dati di un utente*/
	function sessionFields($resultSet){
		$_SESSION['id'] = $resultSet[0]->id;
		$_SESSION['username'] = $resultSet[0]->username;
		$_SESSION['name'] = $resultSet[0]->name;
		$_SESSION['surname'] = $resultSet[0]->surname;
		$_SESSION['email'] = $resultSet[0]->email;
		$_SESSION['role'] = $resultSet[0]->role;
		$_SESSION['picture'] = $resultSet[0]->picture;
	}

	/*Funzione che ritorna il nome del blog*/
	function blogName(){
		global $conn;
		$result = $conn->query('SELECT name FROM blog LIMIT 1');
		if($result instanceof mysqli_result)
			if($result->num_rows > 0)
				return $result->fetch_object()->name;				
		else
			return false;
	}

	/*Funzione che ritorna la descrizione del blog*/
	function blogDescription(){
		global $conn;
		$result = $conn->query('SELECT description FROM blog LIMIT 1');
		if($result instanceof mysqli_result)
			if($result->num_rows > 0)
				return $result->fetch_object()->description;				
		else
			return false;
	}

	/*Funzione che recupera le tuple della tabella USER e le converte in un array di oggetti User*/
	function fetchUsers($query){
		global $conn;
		$result = $conn->query($query);
		if($result instanceof mysqli_result){
			$resultSet = array();
			while ($row = $result->fetch_assoc()) {
				array_push($resultSet, new User($row['id'], $row['username'], $row['email'], $row['name'], $row['surname'], $row['password'], $row['role'], $row['profilePicture']));
			}
			return $resultSet;				
		}
		else{
			return $result;
		}
	}

	/*Funzione che recupera le tuple della tabella COMMENT e le converte in un array di oggetti Comment*/
	function fetchComments($query){
		global $conn;
		$result = $conn->query($query);
		if($result instanceof mysqli_result){
			$resultSet = array();
			while ($row = $result->fetch_assoc()) {
				array_push($resultSet, new Comment($row['id'], $row['post'], $row['author'], $row['text'], $row['dateComment'], $row['reported']));
			}
			return $resultSet;				
		}
		else{
			return $result;
		}
	}

	/*Funzione che recupera le tuple della tabella POST e le converte in un array di oggetti Post*/
	function fetchPosts($query){
		global $conn;
		$result = $conn->query($query);
		if($result instanceof mysqli_result){
			$resultSet = array();
			while ($row = $result->fetch_assoc()) {
				array_push($resultSet, new Post($row['id'], $row['title'], $row['text'], $row['author'], $row['dateCreated'], $row['dateLastModified'], $row['category'], $row['status']));
			}
			return $resultSet;				
		}
		else{
			return $result;
		}
	}

	/*Funzione che recupera le tuple della tabella LIKE e le converte in un array di oggetti Like*/
	function fetchLikes($query){
		global $conn;
		$result = $conn->query($query);
		if($result instanceof mysqli_result){
			$resultSet = array();
			while ($row = $result->fetch_assoc()) {
				array_push($resultSet, new Like($row['id'], $row['type'], $row['idpost'], $row['idcomment'], $row['iduser'], $row['date']));
			}
			return $resultSet;				
		}
		else{
			return $result;
		}			
	}

	/*Funzione che recupera le tuple della tabella TAG e le converte in un array di oggetti Tag*/
	function fetchTags($query){
		global $conn;
		$result = $conn->query($query);
		if($result instanceof mysqli_result){
			$resultSet = array();
			while ($row = $result->fetch_assoc()) {
				array_push($resultSet, $row['tag']);
			}
			return $resultSet;				
		}
		else{
			return $result;
		}				
	}

	/*Funzione che recupera le tuple della tabella CATEGORY e le converte in un array di oggetti Category*/
	function fetchCategories($query){
		global $conn;
		$result = $conn->query($query);
		if($result instanceof mysqli_result){
			$resultSet = array();
			while ($row = $result->fetch_assoc()) {
				array_push($resultSet, new Category($row['id'], $row['category'], $row['color']));
			}
			return $resultSet;				
		}
		else{
			return $result;
		}	
	}

	/*Funzione che recupera le tuple della tabella VIEWS e le converte in un array di oggetti Visit*/
	function fetchVisits($query){
		global $conn;
		$result = $conn->query($query);
		if($result instanceof mysqli_result){
			$resultSet = array();
			while ($row = $result->fetch_assoc()) {
				array_push($resultSet, new Visit($row['iduser'], $row['dateViewed']));
			}
			return $resultSet;				
		}
		else{
			return $result;
		}	
	}

	/*Funzione che registra la visualizzazione del post quando questo viene aperto*/
	function addVisit($post){
		global $conn;
		if(ctype_digit($post)){
			$user = isset($_SESSION['id']) ? $_SESSION['id'] : null;

			if ($user == null) 
				$query = "INSERT INTO views (idpost) VALUES ('".$post."')";
			else
				$query = "INSERT INTO views (idpost, iduser) VALUES ('".$post."', '".$user."')";

			if(!$conn->query($query)){
				errorMessage("Si è verificato un errore...", "C'è stato un problema con l'apertura dell'articolo. Riprova più tardi!");
				die();
			}
		}
		else{
			errorMessage("Si è verificato un errore...", "L'articolo che cerchi non esiste!");
			die();
		}
	}

	/*Funzione che mostra una data MYSQL nel formato specificato.
	SHORT: dd/mm/aaaa hh:mm
	LONG: dd month aaaa hh:mm*/
	function toDate($string, $format){
		$months = ['Gennaio', 'Febbraio', 'Marzo', 'Aprile', 'Maggio', 'Giugno', 'Luglio', 'Agosto', 'Settembre', 'Ottobre', 'Novembre', 'Dicembre'];

		$date = explode(' ', $string)[0];
		$time = substr(explode(' ', $string)[1], 0, 5);

		$day = explode('-', $date)[2];
		$monthS = explode('-', $date)[1];
		$monthL = $months[intval($monthS) - 1];
		$year = explode('-', $date)[0];

		if($format == 'short')
			return $day.'/'.$monthS.'/'.$year.' '.$time;
		else
			return $day.' '.$monthL.' '.$year.' '.$time;
	}

	/*Funzione per l'ordinamento degli articoli nella visualizzazione tabulare.*/
	function sortPosts($filter, $order, $tab, $start = null){
		$start = $start != null ? $start."," : "";

		switch($filter){
			case 'title':
				if($tab == 'all')
					$query = "SELECT * FROM post ORDER BY title ".$order." LIMIT ".$start."20";
				else
					$query = "SELECT * FROM post WHERE author=".$_SESSION['id']." ORDER BY title ".$order." LIMIT ".$start."20";
				break;
			case 'date':
				if($tab == 'all')
					$query = "SELECT * FROM post ORDER BY dateLastModified ".$order." LIMIT ".$start."20";
				else
					$query = "SELECT * FROM post WHERE author=".$_SESSION['id']." ORDER BY dateLastModified ".$order." LIMIT ".$start."20";
				break;
			case 'comment':
				if($tab == 'all')
					$query = "SELECT P.*, COUNT(C.id) FROM post P LEFT OUTER JOIN comment C ON P.id=C.post GROUP BY P.id ORDER BY COUNT(C.id) ".$order." LIMIT ".$start."20";
				else
					$query = "SELECT P.*, COUNT(C.id) FROM post P LEFT OUTER JOIN comment C ON P.id=C.post WHERE P.author=".$_SESSION['id']." GROUP BY P.id ORDER BY COUNT(C.id) ".$order." LIMIT ".$start."20";
				break;
			case 'like':
				if($tab == 'all')
					$query = "SELECT P.*, COUNT(L.id) 
							FROM post P LEFT OUTER JOIN (SELECT * FROM `like` WHERE type='L' ) AS L ON P.id=L.idpost
							GROUP BY P.id 
							ORDER BY COUNT(L.id) ".$order." LIMIT ".$start."20";
				else
					$query = "SELECT P.*, COUNT(L.id) 
							FROM post P LEFT OUTER JOIN (SELECT * FROM `like` WHERE type='L' ) AS L ON P.id=L.idpost
							WHERE P.author=".$_SESSION['id']."
							GROUP BY P.id 
							ORDER BY COUNT(L.id) ".$order." LIMIT ".$start."20";
				break;
			case 'dislike':
				if($tab == 'all')
					$query = "SELECT P.*, COUNT(L.id) 
							FROM post P LEFT OUTER JOIN (SELECT * FROM `like` WHERE type='D' ) AS L ON P.id=L.idpost
							GROUP BY P.id 
							ORDER BY COUNT(L.id) ".$order." LIMIT ".$start."20";
				else
					$query = "SELECT P.*, COUNT(L.id) 
							FROM post P LEFT OUTER JOIN (SELECT * FROM `like` WHERE type='D' ) AS L ON P.id=L.idpost
							WHERE P.author=".$_SESSION['id']."
							GROUP BY P.id 
							ORDER BY COUNT(L.id) ".$order." LIMIT ".$start."20";
				break;
		}
		return fetchPosts($query);
	}

	/*Funzione per l'ordinamento dei commenti nella visualizzazione tabulare.*/
	function sortComments($filter, $order, $tab, $start = null){
		$start = $start != null ? $start."," : "";
		switch($filter){
			case 'date':
				if($tab == 'all')
					$query = "SELECT * FROM comment ORDER BY dateComment ".$order." LIMIT ".$start."20";
				else if($tab == 'yours')
					$query = "SELECT * FROM comment 
							WHERE post IN (SELECT id
										FROM post
										WHERE author=".$_SESSION['id'].") 
										ORDER BY dateComment ".$order." LIMIT ".$start."20";
				else
					$query = "SELECT * FROM comment WHERE reported>=3 ORDER BY dateComment ".$order." LIMIT ".$start."20";
				break;
			case 'report':
				if($tab == 'all')
					$query = "SELECT * FROM comment ORDER BY reported ".$order." LIMIT ".$start."20";
				else if($tab == 'yours')
					$query = "SELECT * FROM comment 
							WHERE post IN (SELECT id
										FROM post
										WHERE author=".$_SESSION['id'].") 
										ORDER BY reported ".$order." LIMIT ".$start."20";
				else
					$query = "SELECT * FROM comment WHERE reported>=3 ORDER BY reported ".$order." LIMIT ".$start."20";
				break;
			case 'like':
				if($tab == 'all')
					$query = "SELECT C.*, COUNT(L.id) 
							FROM comment C LEFT OUTER JOIN (SELECT * FROM `like` WHERE type='L' ) AS L ON C.id=L.idcomment
							GROUP BY C.id
							ORDER BY COUNT(L.id) ".$order." LIMIT ".$start."20";
				else if($tab == 'yours')
					$query = "SELECT * FROM 
								(SELECT * FROM comment 
								WHERE post IN (SELECT id
								FROM post
								WHERE author=".$_SESSION['id'].") AS C 
							LEFT OUTER JOIN 
								(SELECT * FROM `like` WHERE type='L' ) AS L 
							ON C.id=L.idcomment
							GROUP BY C.id
							ORDER BY COUNT(L.id) ".$order." LIMIT ".$start."20";
				else
					$query = "SELECT C.*, COUNT(L.id) 
							FROM comment C LEFT OUTER JOIN (SELECT * FROM `like` WHERE type='L' ) AS L ON C.id=L.idcomment
							WHERE C.reported>=3
							GROUP BY C.id
							ORDER BY COUNT(L.id) ".$order." LIMIT ".$start."20";
				break;
			case 'dislike':
				if($tab == 'all')
					$query = "SELECT C.*, COUNT(L.id) 
							FROM comment C LEFT OUTER JOIN (SELECT * FROM `like` WHERE type='D' ) AS L ON C.id=L.idcomment
							GROUP BY C.id
							ORDER BY COUNT(L.id) ".$order." LIMIT ".$start."20";
				else if($tab == 'yours')
					$query = "SELECT * FROM 
								(SELECT * FROM comment 
								WHERE post IN (SELECT id
								FROM post
								WHERE author=".$_SESSION['id'].") AS C 
							LEFT OUTER JOIN 
								(SELECT * FROM `like` WHERE type='D' ) AS L 
							ON C.id=L.idcomment
							GROUP BY C.id
							ORDER BY COUNT(L.id) ".$order." LIMIT ".$start."20";
				else
					$query = "SELECT C.*, COUNT(L.id) 
							FROM comment C LEFT OUTER JOIN (SELECT * FROM `like` WHERE type='D' ) AS L ON C.id=L.idcomment
							WHERE C.reported>=3
							GROUP BY C.id
							ORDER BY COUNT(L.id) ".$order." LIMIT ".$start."20"; 
				break;
		}
		return fetchComments($query);
	}

	/*Funzione che mostra il pannello per l'ordinamento nella visualizzazione tabulare degli articoli*/
	function showPostFilterPanel(){
		echo "<div class='filterPanel'>";
		echo "<form id='orderForm' class='orderBy' action='#' method='POST'>";
		echo "<div>";
		echo "<label class='filterLabel'>Ordina per: </label>";
		echo "<select name='filter'>";
		echo "<option value='title'>Titolo</option>";
		echo "<option value='date'>Data</option>";
		echo "<option value='comment'>Commenti</option>";
		echo "<option value='like'>Likes</option>";
		echo "<option value='dislike'>Dislikes</option>";
		echo "</select>";
		echo "<label class='filterLabel'>Crescente <input type='radio' name='order' value='asc' checked></label>";
		echo "<label class='filterLabel'>Decrescente <input type='radio' name='order' value='desc'></label>";
		echo "</div>";
		echo "<button type='submit' class='button smallButton' name='sort'>Ordina</button>";
		echo "</form>";
		echo "</div>";
	}

	/*Funzione che mostra il pannello per l'ordinamento nella visualizzazione tabulare dei commenti*/
	function showCommentFilterPanel(){
		echo "<div class='filterPanel'>";
		echo "<form class='orderBy' action='#' method='POST'>";
		echo "<div>";
		echo "<label class='filterLabel'>Ordina per: </label>";
		echo "<select name='filter'>";
		echo "<option value='date'>Data</option>";
		echo "<option value='like'>Likes</option>";
		echo "<option value='dislike'>Dislikes</option>";
		echo "<option value='report'>Segnalazioni</option>";
		echo "</select>";
		echo "<label class='filterLabel'>Crescente <input type='radio' name='order' value='asc' checked></label>";
		echo "<label class='filterLabel'>Decrescente <input type='radio' name='order' value='desc'></label>";
		echo "</div>";
		echo "<button type='submit' class='button smallButton' name='sort'>Ordina</button>";
		echo "</form>";
		echo "</div>";
	}

	/*Funzione che calcola la popolarità di utente*/
	function userPopularity($user){
		//La popolarità di un utente è calcolata in base ai mi piace/non mi piace ricevuti dai suoi post e dai suoi commenti
		//LP = mi piace ai post -> valgono 5
		//LC = mi piace ai commenti -> valgono 3
		//DP = non mi piace ai post -> valgono -3
		//DC = non mi piace ai commenti -> valgono -1
		//SC = segnalazioni ai commenti -> valgono -0.5
		$values = userSocialData($user);

		$popularity = $values[0]*5 + $values[1]*(-3) + $values[2]*3 + $values[3]*(-1) + $values[4]*(-0.5);
		return $popularity;
	}

	/*Funzione che ottiene i dati necessari al calcolo della popolarità di un utente*/
	function userSocialData($user){
		$comments = fetchComments("SELECT * FROM comment WHERE author=".$user->id);
		$posts = fetchPosts("SELECT * FROM post WHERE author=".$user->id);

		$postLikes = 0;
		$postDislikes = 0;
		$commentLikes = 0;
		$commentDislikes = 0;
		$commentReport = 0;

		for($i = 0; $i < count($posts); $i++){
			$postLikes += count(fetchLikes("SELECT * FROM `like` WHERE type='L' AND idpost=".$posts[$i]->id));
			$postDislikes += count(fetchLikes("SELECT * FROM `like` WHERE type='D' AND idpost=".$posts[$i]->id));
		}
		for($i = 0; $i < count($comments); $i++){
			$commentLikes += count(fetchLikes("SELECT * FROM `like` WHERE type='L' AND idcomment=".$comments[$i]->id));
			$commentDislikes += count(fetchLikes("SELECT * FROM `like` WHERE type='D' AND idcomment=".$comments[$i]->id));
			$commentReport += $comments[$i]->numberReported;
		}

		return array($postLikes, $postDislikes, $commentLikes, $commentDislikes, $commentReport);
	}

	/*Funzione che mostra un messaggio di errore compatibile con quello mostrato in JS.*/
	function errorMessage($header, $message){
		echo "<div class='errorMessageContainer'>";
		echo "<p class='messageHeader'>".$header."</p>";
		echo "<p class='messageText'>".$message."</p>";
		echo "</div>";
	}
?>