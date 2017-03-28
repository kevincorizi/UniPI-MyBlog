<?php
	require_once 'config.php';
	require_once DIR_PHP.'sessionManager.php';
	require_once DIR_PHP.'databaseManager.php';
	require_once DIR_PHP.'lib.php';
	global $conn;
	startSession();

	//Se il setup è già stato effettuato rimando alla index
	if(blogName()){
		redirect('index.php');
	}

	if(isset($_POST['submitSetup'])){
		$blogName = $conn->secure($_POST['blogName']);
		$blogDescription = $conn->secure($_POST['blogDescription']);

		$adminName = $conn->secure($_POST['adminName']);
		$adminSurname = $conn->secure($_POST['adminSurname']);
		$adminEmail = $conn->secure($_POST['adminEmail']);
		$adminUsername = $conn->secure($_POST['adminUsername']);
		$adminPassword = md5($_POST['password']);

		$registerQuery = "INSERT INTO user (name, surname, email, username, password, role)
						  VALUES ('".$adminName."','".$adminSurname."','".$adminEmail."','".$adminUsername."','".$adminPassword."','admin')";
		$result = $conn->query($registerQuery);		
		if($result == FALSE){
			redirect('setup.php?error=admin');			
		}

		$blogQuery = "INSERT INTO blog (name, description)
						  VALUES ('".$blogName."','".$blogDescription."')";
		$result = $conn->query($blogQuery);		
		if($result == FALSE){
			redirect('setup.php?error=blog');			
		}
		
		$loginQuery = "SELECT * FROM user WHERE username='".$adminUsername."'";
		$resultLogin = fetchUsers($loginQuery);
		sessionFields($resultLogin);
		redirect('panel.php');					
	}
?>
<!DOCTYPE html>
<html lang='it'>
	<head>
		<title>Configurazione</title>

		<meta charset='UTF-8'>

		<script type='text/javascript' src='javascript/setup/setup.js'></script>
		<script type='text/javascript' src='javascript/lib.js'></script>
		<script type='text/javascript' src='javascript/objects/messageClass.js'></script>

		<link href='css/shared/shared.css' rel='stylesheet' type='text/css'>
		<link href='css/shared/messages.css' rel='stylesheet' type='text/css'>
		<link href='css/setup/setup.css' rel='stylesheet' type='text/css'>
	</head>
	<body onload='loadSetup()'>
		<header>
			<h1>Benvenuto!</h1>
			<h2>Per iniziare a usare il tuo blog, devi inserire alcune informazioni...</h2>
		</header>
		<div id='wrapper'>
			<form id='setup' action='#' method='POST' onsubmit='return validateForm(this)'>
				<div id='blogPanel'>
					<div class='messageContainer'>
						<p class='messageHeader'>Dati del blog</p>
						<p class='messageText'>Inserisci il nome del tuo blog, e (se vuoi) una breve descrizione. Potrai modificarli in qualsiasi momento.</p>
					</div>
					<input class='largeField' type='text' name='blogName' maxlength=45 required placeholder='Nome del blog' autocomplete='off'>
					<input class='largeField' type='text' name='blogDescription' maxlength=200 required placeholder='Descrizione del blog' autocomplete='off'>		
				</div>
				<div id='adminPanel'>
					<div class='messageContainer'>
						<p class='messageHeader'>I tuoi dati</p>
						<p class='messageText'>Inserisci i dati con i quali interagirai con il blog e con gli altri utenti. Dopo la registrazione, sarai l'amministratore del blog.</p>
					</div>
					<input class='largeField' type='text' name='adminName' maxlength=30 required placeholder='Nome' autocomplete='off' pattern='^[a-zA-Z\sàèéìùò]+$'>
					<input class='largeField' type='text' name='adminSurname' maxlength=30 required placeholder='Cognome' autocomplete='off' pattern='^[a-zA-Z\sàèéìùò]+$'>
					<input class='largeField' type='email' name='adminEmail' maxlength=45 required placeholder='Email' autocomplete='off'>
					<input class='largeField' type='text' name='adminUsername' maxlength=45 required placeholder='Nome utente' autocomplete='off'>
					<input class='largeField' type='password' name='password' maxlength=45 required placeholder='Password' autocomplete='off'>
					<input class='largeField' type='password' name='repeatPassword' maxlength=45 required placeholder='Ripeti password' autocomplete='off'>				
				</div>
				<input type='submit' name='submitSetup' class='button largeButton' content='Salva le impostazioni'>
			</form>	
		</div>
		<footer>
			<address>Copyright &copy; 2015 - <a href='mailto:kevincorizi@outlook.com' target='blank'>Kevin Corizi</a></address>
			<p>Icons by <a href='https://google.github.io/material-design-icons/' target='blank'>Google Material Icons</a> - <a href='help.php'>Guida al servizio</a></p>
		</footer>
	</body>
</html>
