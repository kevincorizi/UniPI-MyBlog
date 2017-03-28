<?php
	require_once 'config.php';
	require_once DIR_PHP.'sessionManager.php';
	require_once DIR_PHP.'databaseManager.php';
	require_once DIR_PHP.'lib.php';
	global $conn;
	startSession();
	//Se l'utente è già loggato lo rimando alla pagina principale
	if(isLogged()){
		redirect('index.php');
	}
	//Se l'utente ha premuto il bottone di login
	if (isset($_POST['submitLogin'])) {
		$username = $conn->secure($_POST['usernameLogin']);
		$password = md5($_POST['passwordLogin']);

		$resultSet = fetchUsers("SELECT * FROM user WHERE username='".$username."'");

		if(count($resultSet) != 1){
			redirect('join.php?error=username');
		}
		else{
			if($resultSet[0]->password == $password){
				sessionFields($resultSet);
				redirect('index.php');				
			}
			else{
				redirect('join.php?error=password');				
			}
		}
	}
	//Se invece ha premuto il bottone di registrazione
	else if(isset($_POST['submitRegister'])){
		$name = $conn->secure($_POST['nameRegister']);
		$surname = $conn->secure($_POST['surnameRegister']);
		$email = $conn->secure($_POST['emailRegister']);
		$username = $conn->secure($_POST['usernameRegister']);
		$password = md5($_POST['password']);	

		$previousUser = fetchUsers("SELECT * FROM user WHERE username='".$username."' OR email='".$email."'");
		if(count($previousUser) == 0){
			$registerQuery = "INSERT INTO user (name, surname, email, username, password, role) VALUES ('".$name."','".$surname."','".$email."','".$username."','".$password."','user')";
			$result = $conn->query($registerQuery);		

			if($result == FALSE){
				redirect('join.php?error=register');			
			}
			else{
				$loginQuery = "SELECT * FROM user WHERE username='".$username."'";
				$resultLogin = fetchUsers($loginQuery);
				sessionFields($resultLogin);
				redirect('index.php');				
			}
		}
		else{
			redirect('join.php?error=duplicate');	
		}
	}
?>
<!DOCTYPE html>
<html lang='it'>
	<head>
		<title>Unisciti a noi!</title>

		<meta charset='UTF-8'>
		
		<script type='text/javascript' src='javascript/join/join.js'></script>
		<script type='text/javascript' src='javascript/lib.js'></script>
		<script type='text/javascript' src='javascript/objects/messageClass.js'></script>

		<link href='css/shared/shared.css' rel='stylesheet' type='text/css'>
		<link href='css/shared/messages.css' rel='stylesheet' type='text/css'>
		<link href='css/join/join.css' rel='stylesheet' type='text/css'>
	</head>
	<body onload='loadJoin()'>
		<header>
			<h1 id='blogName'><a href='index.php'><?php echo blogName(); ?></a></h1>
		</header>
		<div id='wrapper'>
			<div id='loginPanel'>
				<p class='messageHeader'>Accedi</p>
				<form id='login' action='#' method='POST'>
					<input class='largeField' type='text' name='usernameLogin' class='loginInput' required placeholder='Nome utente'>
					<input class='largeField' type='password' name='passwordLogin' class='loginInput' required placeholder='Password'>	
					<button type='submit' name='submitLogin' class ='button largeButton'>ACCEDI</button>			
				</form>
			</div>
			<div id='registerPanel'>
				<p class='messageHeader'>Non sei ancora registrato? Registrati adesso!</p>
				<form id='register' action='#' method='POST' onsubmit='return validateForm(this)'>
					<input class='largeField' type='text' name='nameRegister' maxlength=30 required placeholder='Nome' pattern='^[a-zA-Z\sàèéìùò]+$'>
					<input class='largeField' type='text' name='surnameRegister' maxlength=30 required placeholder='Cognome' pattern='^[a-zA-Z\sàèéìùò]+$'>
					<input class='largeField' type='email' name='emailRegister' maxlength=45 required placeholder='Email'>
					<input class='largeField' type='text' name='usernameRegister' maxlength=45 required placeholder='Nome utente'>
					<input class='largeField' type='password' name='password' id='password' maxlength=45 required placeholder='Password'>
					<input class='largeField' type='password' name='repeatPassword' id='passwordRepeat' maxlength=45 required placeholder='Ripeti password'>		
					<button type='submit' name='submitRegister' class ='button largeButton'>REGISTRATI</button>			
				</form>				
			</div>
		</div>
		<footer>
			<address>Copyright &copy; 2015 - <a href='mailto:kevincorizi@outlook.com' target='blank'>Kevin Corizi</a></address>
			<p>Icons by <a href='https://google.github.io/material-design-icons/' target='blank'>Google Material Icons</a> - <a href='help.php'>Guida al servizio</a></p>
		</footer>	
	</body>
</html>

