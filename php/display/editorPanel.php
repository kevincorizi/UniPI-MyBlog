<?php
	/*Script per la visualizzazione dell'editor di testo*/
	require_once DIR_OBJECTS."post.php";
	global $conn;

	if($conn->secure($_GET['editor']) != 'new'){
		if(ctype_digit($_GET['editor'])){
			if(!isset(fetchPosts("SELECT * FROM post WHERE id=".$conn->secure($_GET['editor']))[0])){
				errorMessage("Si è verificato un errore...", "Questo articolo non esiste!");
				die();
			}
			else{
				$post = fetchPosts("SELECT * FROM post WHERE id=".$conn->secure($_GET['editor']))[0];
				if($post->author != $_SESSION['id']){
					errorMessage("Si è verificato un errore...", "Non sei autorizzato a modificare l'articolo di un altro utente!");
					die();
				}
				$post->text = explode("</pre>", explode("<pre class='postTextPre'>", $post->text)[1])[0];
				$post->tags = fetchTags("SELECT * FROM tag WHERE idpost=".$post->id);
			}
		}
		else
			errorMessage("Si è verificato un errore...", "Questa pagina non esiste!");
	}

	//Gestisce la richiesta vera e propria
	if(isset($_POST['send'])){
		$title = $conn->secure($_POST["postTitle"]);
		$text = "<pre class=\'postTextPre\'>".$conn->secure($_POST["postText"])."</pre>";
		$category = $conn->secure($_POST["categories"]);

		if($category == "all")
			$category = null;

		$tags = explode(", ", strtolower($conn->secure($_POST["tags"])));
		$status = $conn->secure($_POST["status"]);

		if($conn->secure($_POST['send']) == 'insert'){
			if($category == null)
				$query = "INSERT INTO post (title, text, dateCreated, dateLastModified, author, status)
				VALUES ('".$title."','".$text."','".date("Y-m-d H:i:s")."','".date("Y-m-d H:i:s")."',".$_SESSION["id"].", '".$status."')";
			else
				$query = "INSERT INTO post (title, text, dateCreated, dateLastModified, category, author, status)
				VALUES ('".$title."','".$text."','".date("Y-m-d H:i:s")."','".date("Y-m-d H:i:s")."','".$category."',".$_SESSION["id"].", '".$status."')";

			$result = $conn->query($query);
			
			$id = $conn->query("SELECT id FROM post WHERE author=".$_SESSION["id"]." ORDER BY dateCreated DESC LIMIT 1")->fetch_object()->id;

			for($i = 0; $i < count($tags); $i++)
				if($tags[$i] != "")
					$result = $conn->query("INSERT INTO tag VALUES (".$id.",'".$tags[$i]."')");

			if(!$result){
				errorMessage("Si è verificato un errore...", "La pubblicazione dell'articolo non è andata a buon fine. Riprova più tardi!");
				die();
			}

			if($status == "draft")
				redirect("../../panel.php?posts=all");
			else
				redirect("../../index.php?post=".$id);
		}
		else if($conn->secure($_POST['send']) == 'update'){
			if($category != null)
				$query = "UPDATE post
					SET title='".$title."', text='".$text."', dateLastModified='".date("Y-m-d H:i:s")."', category='".$category."', status='".$status."'
					WHERE id=".$post->id;
			else
				$query = "UPDATE post
					SET title='".$title."', text='".$text."', dateLastModified='".date("Y-m-d H:i:s")."', status='".$status."'
					WHERE id=".$post->id;

			$result = $conn->query($query);

			if(!$result){
				errorMessage("Si è verificato un errore...", "L'aggiornamento dell'articolo non è andato a buon fine. Riprova più tardi!");
				die();
			}

			$conn->query("DELETE FROM tag WHERE idpost=".$post->id);
			for($i = 0; $i < count($tags); $i++)
				if($tags[$i] != "")
					$result = $conn->query("INSERT INTO tag VALUES (".$post->id.",'".$tags[$i]."')");

			if($status == "draft")
				redirect("../../panel.php?posts=all");
			else
				redirect("../../index.php?post=".$post->id);
		}
		else if($conn->secure($_POST['send']) == 'delete'){
			$result = $conn->query("DELETE FROM post WHERE id=".$post->id);

			if(!$result){
				errorMessage("Si è verificato un errore...", "L'eliminazione dell'articolo non è andata a buon fine. Riprova più tardi!");
				die();
			}

			redirect("../../panel.php?posts=all");
		}

		echo $conn->getError();
	}

	if($conn->secure($_GET['editor']) == 'new'){
		echo "<form action='#' method='POST' id='editor'>";
		echo "<div id='mainEditor'>";
		echo "<input type='text' class='largeField' placeholder='Titolo...' name='postTitle' autocomplete='off' tabindex=1 required>";
		echo "<div id='writeBox'>";
		echo "<div id='editorToolbox'>";
		echo "<button class='textOption' name='bold' type='button' onclick=\"buttonHandler('bold')\"><img src='assets/icons/boldWhite.png' alt='BOLD'></button>";
		echo "<button class='textOption' name='italic' type='button' onclick=\"buttonHandler('italic')\"><img src='assets/icons/italicWhite.png' alt='ITALIC'></button>";
		echo "<button class='textOption' name='underline' type='button' onclick=\"buttonHandler('underline')\"><img src='assets/icons/underlineWhite.png' alt='UNDERLINE'></button>";
		echo "<button class='textOption' name='img' type='button' onclick=\"buttonHandler('img')\"><img src='assets/icons/imgWhite.png' alt='IMAGE'></button>";
		echo "<button class='textOption' name='link' type='button' onclick=\"buttonHandler('link')\"><img src='assets/icons/linkWhite.png' alt='LINK'></button>";
		echo "<button class='textOption' name='quote' type='button' onclick=\"buttonHandler('quote')\"><img src='assets/icons/quoteWhite.png' alt='QUOTE'></button>";
		echo "<button class='textOption' name='more' type='button' onclick=\"buttonHandler('more')\"><img src='assets/icons/moreWhite.png' alt='MORE'></button>";
		echo "</div>";
		echo "<textarea id='htmlText' name='postText' required placeholder='Inizia a scrivere...' onkeyup=\"(function(){monitorText(event)})(event)\" tabindex=2></textarea>";
		echo "</div>";
		echo "<iframe id='editorText' src='editor/embedded.html'></iframe>";
		echo "</div>";
		echo "<div id='secondaryEditor'>";
		echo "<label class='messageHeader'>Completa il post</label>";
		echo "<label class='messageHeader'>Categoria</label>";

		$categories = fetchCategories("SELECT * FROM category");
		echo "<select class='mediumField' name='categories' tabindex=3>";
		echo "<option value='all' selected>Tutti gli articoli</option>";
		for($i = 0; $i < count($categories); $i++){
			echo "<option value='".$categories[$i]->id."'>".$categories[$i]->name."</option>";
		}
		echo "</select>";

		echo "<label class='messageHeader'>Tags</label>";
		echo "<input class='mediumField' type='text' id='tags' name='tags' placeholder='Inserisci i tag, separati da virgole...' pattern='^[a-zA-Z0-9,]+$' tabindex=4>";
		echo "<select class='mediumField' name='status' tabindex=5>";
		echo "<option value='draft'>Bozza</option>";
		echo "<option value='published'>Pubblicato</option>";
		echo "</select>";
		echo "<button class='button largeButton' id='publishPost' type='submit' name='send' value='insert' tabindex=6>Aggiungi</button>";
		echo "</div>";
		echo "</form>";
	}
	else{
		echo "<form action='#' method='POST' id='editor'>";
		echo "<div id='mainEditor'>";
		echo "<input type='text' class='largeField' name='postTitle' autocomplete='off' value='".$post->title."' tabindex=1 required>";
		echo "<div id='writeBox'>";
		echo "<div id='editorToolbox'>";
		echo "<button class='textOption' name='bold' type='button' onclick=\"buttonHandler('bold')\"><img src='assets/icons/boldWhite.png' alt='BOLD'></button>";
		echo "<button class='textOption' name='italic' type='button' onclick=\"buttonHandler('italic')\"><img src='assets/icons/italicWhite.png' alt='ITALIC'></button>";
		echo "<button class='textOption' name='underline' type='button' onclick=\"buttonHandler('underline')\"><img src='assets/icons/underlineWhite.png' alt='UNDERLINE'></button>";
		echo "<button class='textOption' name='img' type='button' onclick=\"buttonHandler('img')\"><img src='assets/icons/imgWhite.png' alt='IMAGE'></button>";
		echo "<button class='textOption' name='link' type='button' onclick=\"buttonHandler('link')\"><img src='assets/icons/linkWhite.png' alt='LINK'></button>";
		echo "<button class='textOption' name='quote' type='button' onclick=\"buttonHandler('quote')\"><img src='assets/icons/quoteWhite.png' alt='QUOTE'></button>";
		echo "<button class='textOption' name='more' type='button' onclick=\"buttonHandler('more')\"><img src='assets/icons/moreWhite.png' alt='MORE'></button>";
		echo "</div>";
		echo "<textarea id='htmlText' name='postText'required onkeyup=\"(function(){monitorText(event)})(event)\" tabindex=2>".$post->text."</textarea>";
		echo "</div>";
		echo "<iframe id='editorText' srcdoc=\"".htmlspecialchars($post->text)."\"></iframe>";
		echo "</div>";
		echo "<div id='secondaryEditor'>";
		echo "<label class='messageHeader'>Completa il post</label>";
		echo "<label class='messageHeader'>Categoria</label>";

		$categories = fetchCategories("SELECT * FROM category");
		echo "<select class='mediumField' name='categories' tabindex=3>";
		echo "<option value='all' selected>Tutti gli articoli</option>";
		for($i = 0; $i < count($categories); $i++){
			if($categories[$i]->id == $post->category)
				echo "<option value='".$categories[$i]->id."' selected>".$categories[$i]->name."</option>";
			else
				echo "<option value='".$categories[$i]->id."'>".$categories[$i]->name."</option>";
		}
		echo "</select>";

		echo "<label class='messageHeader'>Tags</label>";

		if(count($post->tags) == 0)
			echo "<input class='mediumField' type='text' id='tags' name='tags' placeholder='Inserisci i tag, separati da virgole...' pattern='^[a-zA-Z0-9,]+$' tabindex=4>";
		else
			echo "<input class='mediumField' type='text' id='tags' name='tags' value='".implode(', ', $post->tags)."' pattern='^[a-zA-Z0-9,]+$' tabindex=4>";
		
		echo "<select class='mediumField' name='status' tabindex=5>";
		if($post->status == 'draft'){
			echo "<option value='draft' selected>Bozza</option>";
			echo "<option value='published'>Pubblicato</option>";
		}
		else{
			echo "<option value='draft'>Bozza</option>";
			echo "<option value='published' selected>Pubblicato</option>";
		}	
		echo "</select>";
		echo "<button class='button largeButton' id='updatePost' type='submit' name='send' value='update' tabindex=6>Aggiorna</button>";
		echo "<button class='button largeButton' id='deletePost' type='submit' name='send' value='delete' tabindex=7>Elimina</button>";
		echo "</div>";
		echo "</form>";
	}
?>