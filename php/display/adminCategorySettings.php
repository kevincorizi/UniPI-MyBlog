<?php
	/*Script per la visualizzazione delle impostazioni per le categorie*/
	if(!isset($_GET['settings']) || (isset($_GET['settings']) && $_GET['settings'] != 'categories')){
		errorMessage("Si è verificato un errore...", "La pagina che cerchi non esiste!");
		die();
	}

	global $conn;

	if(isset($_POST['addCategory'])){
		$name = $conn->secure($_POST['newCategoryName']);
		$color = $conn->secure($_POST['newCategoryColor']);
		$query = "INSERT INTO category (category, color) VALUES ('".$name."', '".$color."')";
		$result = $conn->query($query);
	}
	else if(isset($_POST['editCategory'])){
		$category = $conn->secure($_POST['editCategory']);
		$name = $conn->secure($_POST['newCategoryName']);
		$color = $conn->secure($_POST['newCategoryColor']);

		$query = "UPDATE category SET category='".$name."', color='".$color."' WHERE id=".$category;
		$result = $conn->query($query);
	}
	else if(isset($_POST['deleteCategory'])){
		$query = "DELETE FROM category WHERE id=".$_POST['deleteCategory'];
		$result = $conn->query($query);
	}

	$categories = fetchCategories("SELECT * FROM category");

	echo "<label class='messageText'>Qui puoi modificare le categorie degli articoli o crearne nuove.</label>";
	echo "<div class='settings'>";
	echo "<div class='categoryCard' id='category_0' style='background: black; color: white;'>";
	echo "<p>Tutti gli articoli</p>";
	echo "<p>".count(fetchPosts("SELECT * FROM post"))." post in questa categoria</p>";
	echo "</div>";
	for($i = 0; $i < count($categories); $i++){
		$categories[$i]->showCard();
	}
	echo "</div>";

	echo "<button class='button largeButton' type='button' onclick=\"showCategoryFlyout()\">Aggiungi categoria</button>";
?>