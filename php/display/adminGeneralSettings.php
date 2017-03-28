<?php
	/*Script per la visualizzazione delle impostazioni generali*/
	if(!isset($_GET['settings']) || (isset($_GET['settings']) && $_GET['settings'] != 'general')){
		errorMessage("Si è verificato un errore...", "La pagina che cerchi non esiste!");
		die();
	}

	global $conn;

	if(isset($_POST['saveBlog'])){
		$newBlogName = $conn->secure($_POST['newBlogName']);
		$newBlogDescription = $conn->secure($_POST['newBlogDescription']);

		$query = "UPDATE blog SET name='".$newBlogName."', description='".$newBlogDescription."' WHERE name='".blogName()."'";
		if(!$conn->query($query)){
			errorMessage("Errore", "Non è stato possibile completare l'operazione. Riprova più tardi");
		}
		else
			redirect("panel.php?settings=general");
	}

	if($_SESSION['role'] == 'admin'){
		echo "<div class='settings'>
			<form id='blogSettings' method='POST' action='#'>
			<label class='messageHeader'>Nome del sito</label>
			<input class='largeField' type='text' name='newBlogName' required value='".blogName()."'>
			<label class='messageHeader'>Descrizione del sito</label>
			<input class='largeField' type='text' name='newBlogDescription' value='".blogDescription()."'>
			<button class='button largeButton' type='submit' name='saveBlog'>Salva le modifiche</button>
			</form>
			</div>";
	}
	else if($_SESSION['role'] == 'mod'){
		echo "<div class='settings'>
			<form id='blogSettings' method='POST' action='#'>
			<label class='messageHeader'>Nome del sito</label>
			<input class='largeField' type='text' name='newBlogName' required value='".blogName()."' disabled>
			<label class='messageHeader'>Descrizione del sito</label>
			<input class='largeField' type='text' name='newBlogDescription' value='".blogDescription()."' disabled>
			<button class='button largeButton' type='submit' name='saveBlog' disabled>Salva le modifiche</button>
			</form>
			</div>";
	}
?>