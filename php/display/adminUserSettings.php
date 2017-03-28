<?php
	/*Script per la visualizzazione delle impostazioni per gli utenti*/
	if(!isset($_GET['settings']) || (isset($_GET['settings']) && $_GET['settings'] != 'users')){
		errorMessage("Si è verificato un errore...", "La pagina che cerchi non esiste!");
		die();
	}

	global $conn;
	$users = fetchUsers("SELECT * FROM user");

	if(isset($_POST['save'])){
		for($i = 0; $i < count($users); $i++){
			if(isset($_POST["role_".$users[$i]->id]) && $users[$i]->role != $conn->secure($_POST["role_".$users[$i]->id])){
				//Almeno un amministratore deve esistere
				if($users[$i]->role == 'admin' && count(fetchUsers("SELECT * FROM user WHERE role='admin'")) == 1){
					errorMessage("Attenzione!", "Deve esistere almeno un amministratore!");
				}
				else{
					$query = "UPDATE user SET role='".$conn->secure($_POST["role_".$users[$i]->id])."' WHERE id=".$users[$i]->id;
					$conn->query($query);
				}
			}
		}
		$users = fetchUsers("SELECT * FROM user");
	}

	echo "<label class='messageText'>Qui puoi modificare il ruolo degli utenti iscritti al tuo blog. Puoi nominare moderatori e amministratori, oppure degradare a grado di utente.</label>";
	echo "<form class='settings' action='#' method='POST' name='userEdit'>";
	echo "<table id='userTable'>";
	echo "<thead>";
	echo "<tr class='tableHeader userTableHeader'>";
	echo "<th>Username</th>
		  <th>Nome</th>
          <th>Cognome</th>
		  <th>Email</th>
		  <th>Ruolo</th>
		  <th><img src='assets/icons/likeWhite.png' alt='Mi piace'></th>
		  <th><img src='assets/icons/dislikeWhite.png' alt='Non mi piace'></th>
		  <th><img src='assets/icons/editWhite.png' alt='Tags'></th>";
	echo "</tr>";
	echo "</thead>";
	echo "<tfoot>";
	echo "</tfoot>";

	echo "<tbody>";
	for($i = 0; $i < count($users); $i++)
		$users[$i]->showRow();
	echo "</tbody>";
	echo "</table>";

	echo "<button type='submit' class='button largeButton' name='save'>Salva le modifiche</button>";

	echo "</form>";
?>