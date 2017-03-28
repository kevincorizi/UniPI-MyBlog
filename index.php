<?php
	require_once 'config.php';
	require_once DIR_PHP.'sessionManager.php';
	require_once DIR_PHP.'databaseManager.php';
	require_once DIR_PHP.'lib.php';
	startSession();
	if (isset($_POST['logout'])) {
		logout();
	}
	if(!blogName()){
		//Siamo al primo accesso, quindi rimando alla pagina di configurazione
		redirect("setup.php");
	}
?>
<!DOCTYPE html>
<html lang='it'>
	<head>
		<title><?php echo blogName();?></title>
		<meta charset='UTF-8'>	
		<script type='text/javascript' src='javascript/index/index.js'></script>
		<script type='text/javascript' src='javascript/lib.js'></script>
		<script type='text/javascript' src='javascript/interaction.js'></script>
		<script type='text/javascript' src='javascript/async/AsyncManager.js'></script>

		<script type='text/javascript' src='javascript/objects/commentClass.js'></script>
		<script type='text/javascript' src='javascript/objects/messageClass.js'></script>
		<script type='text/javascript' src='javascript/objects/postClass.js'></script>
		<script type='text/javascript' src='javascript/objects/userClass.js'></script>

		<link href='css/shared/shared.css' rel='stylesheet' type='text/css'>
		<link href='css/shared/messages.css' rel='stylesheet' type='text/css'>
		<link href='css/index/index.css' rel='stylesheet' type='text/css'>
		<link href='css/index/post.css' rel='stylesheet' type='text/css'>
		<link href='css/index/community.css' rel='stylesheet' type='text/css'>
	</head>

	<body onload='loadIndex()'>
		<header>
			<div id='headerWrapper'>
				<h1 id='siteName'><a href='index.php'><?php echo blogName(); ?></a></h1>
				<div id='headerRight'>
					<div id='search'>
						<?php
							if(!isset($_GET['post']))
								echo "<input class='largeField' id='searchInput' type='search' placeholder='cerca...' autocomplete='off' onkeyup=\"(function(){monitorSearchBar(event)})(event)\">";
						?>
					</div>	
					<div id='welcomeUser'>
						<?php
							if(isLogged())
								echo "<img src='".$_SESSION['picture']."' alt='".$_SESSION['username']."' onclick='toggleUserNavigation()'>";
							else
								echo "<img src='assets/users/userIconDefault.png' alt='Utente' onclick='toggleUserNavigation()'>";
						?>
					</div>
				</div>
				<h2 id='siteDescription'><?php echo blogDescription(); ?></h2>
			</div>
		</header>
		<div id='wrapper'>
			<aside id='categories'>
				<ul id='categoryList'>
					<?php
						$allCategories = 'SELECT * FROM category';
						$categories = fetchCategories($allCategories);
						echo "<li class='categoryElement' id='allPosts' style='background-color: black;'><label class='categoryName'><a href='index.php'>Tutti gli articoli</a></label></li>";
						for($i = 0; $i < count($categories); $i++)
							$categories[$i]->showSmall();
					?>
					</ul>
			</aside>
			<main id='postflow'>
				<?php 
					if(isset($_GET['post'])){
						if(ctype_digit($_GET['post']))
							include 'php/display/fullPost.php';
						else{
							errorMessage("Si è verificato un errore...", "L'articolo che cerchi non esiste!");
							die();
						}
					}
					else if(isset($_GET['search'])){
						include 'php/display/postflow.php';
					}
					else if(isset($_GET['category'])){
						if(ctype_digit($_GET['category']))
							include 'php/display/postflow.php';
						else{
							errorMessage("Si è verificato un errore...", "La categoria che cerchi non esiste!");
						}
					}
					else{
						include 'php/display/postflow.php';
					}
					
				?>
			</main>
		</div>
		<?php
			echo "<div id='userNavigation'>";
			if(isLogged()){
				echo "<div class='profilePictureMedium'>";
				echo "<img class='profilePicture' src='".$_SESSION['picture']."' alt='".$_SESSION['username']."'>";
				echo "<img class='changePicture' src='assets/icons/editRound.png' alt='Cambia foto' onclick='showPictureURLFlyout()'>";
				echo "</div>";
				echo "<p class='userIntroduction'>Ciao, ".$_SESSION['username']."!</p>";
				echo "<p class='userIntroduction'>Che cosa vuoi fare?</p>";
				if($_SESSION['role'] != 'user')
					echo "<button class='button goToPanel' onclick=\"location.href='panel.php'\">Pannello di controllo</button>";
				echo "<button class='button goToJoin' onclick=\"location.href='logout.php'\">Esci</button>";
			}
			else{
				echo "<p class='userIntroduction'>Esegui il login per iniziare a commentare e a interagire con gli altri utenti! Oppure registrati se non lo hai ancora fatto!</p>";
				echo "<button class='button goToJoin' onclick=\"location.href='join.php'\">Accedi</button>";
			}	
			echo "<p class='messageHeader'>Serve aiuto?</p>";
			echo "<p class='messageText'><a href='help.php'>Vai alla guida!</a></p>";
			echo "</div>";
		?>
		<footer>
			<address>Copyright &copy; 2015 - <a href='mailto:kevincorizi@outlook.com' target='blank'>Kevin Corizi</a></address>
			<p>Icons by <a href='https://google.github.io/material-design-icons/' target='blank'>Google Material Icons</a> - <a href='help.php'>Guida al servizio</a></p>
		</footer>
	</body>
</html>