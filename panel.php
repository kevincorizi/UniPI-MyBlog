<?php
	require_once 'config.php';
	require_once DIR_PHP.'sessionManager.php';
	require_once DIR_PHP.'databaseManager.php';
	require_once DIR_PHP.'lib.php';
	startSession();

	if(!isset($_SESSION['id']) || !isset($_SESSION['role']) || $_SESSION['role'] == 'user')
		redirect('index.php?error=accessdenied');

	if(!blogName()){
		//Siamo al primo accesso, quindi rimando alla pagina di configurazione
		redirect('setup.php');
	}
?>
<!DOCTYPE html>
<html lang='it'>
	<head>
		<title>Pannello di controllo</title>
		<meta charset='UTF-8'>
		
		<script type='text/javascript' src='javascript/objects/commentClass.js'></script>
		<script type='text/javascript' src='javascript/objects/messageClass.js'></script>
		<script type='text/javascript' src='javascript/objects/postClass.js'></script>
		<script type='text/javascript' src='javascript/objects/userClass.js'></script>

		<script type='text/javascript' src='editor/editor.js'></script>
		<script type='text/javascript' src='javascript/lib.js'></script>
		<script type='text/javascript' src='javascript/interaction.js'></script>
		<script type='text/javascript' src='javascript/async/AsyncManager.js'></script>

		<link href='css/shared/shared.css' rel='stylesheet' type='text/css'>
		<link href='css/shared/messages.css' rel='stylesheet' type='text/css'>
		<link href='css/panel/tables.css' rel='stylesheet' type='text/css'>
		<link href='css/panel/users.css' rel='stylesheet' type='text/css'>
		<link href='css/panel/panel.css' rel='stylesheet' type='text/css'>
		<link href='css/panel/editor.css' rel='stylesheet' type='text/css'>
		<link href='css/panel/settings.css' rel='stylesheet' type='text/css'>
	</head>
		<body onload=''>
		<header>
			<aside id='toolbar'>
				<div id='logo'>
					<a href='index.php'><?php echo blogName(); ?></a>
				</div>
				<ul id='menu'>
					<li class='item'>Articoli
						<ul class='submenu'>
							<li id='posts' class='subitem'><a href='?posts=all'>Tutti gli articoli</a></li>
							<li id='newpost' class='subitem'><a href='?editor=new'>Nuovo articolo</a></li>
							<li id='yourposts' class='subitem'><a href='?posts=yours'>I tuoi articoli</a></li>
						</ul>
					</li>
					<li class='item'>Commenti
						<ul class='submenu'>
							<li id='comments' class='subitem'><a href='?comments=all'>Tutti i commenti</a></li>
							<li id='yourpostscomments' class='subitem'><a href='?comments=yours'>Commenti ai tuoi articoli</a></li>
							<li id='moderation' class='subitem'><a href='?comments=moderation'>In moderazione</a></li>
						</ul>
					</li>
					<li class='item'>Utenti
						<ul class='submenu'>
							<?php
								echo "<li id='profile' class='subitem'><a href='?users=".$_SESSION['id']."'>Il tuo profilo</a></li>";
							?>
							<li id='allusers' class='subitem'><a href='?users=all'>Tutti gli utenti</a></li>
							<li id='staff' class='subitem'><a href='?users=staff'>Staff</a></li>
						</ul>
					</li>
					<li id='settings' class='item'>Impostazioni
						<ul class='submenu'>
							<li id='general' class='subitem'><a href='?settings=general'>Generali</a></li>
							<li id='categories' class='subitem'><a href='?settings=categories'>Gestione categorie</a></li>
							<li id='users' class='subitem'><a href='?settings=users'>Gestione utenti</a></li>
						</ul>
					</li>
				</ul>
			</aside>
		</header>
		<div id='wrapper'>
			<main id='content'>
				<?php
					if(isset($_GET['posts'])){
						if($_GET['posts'] == 'all')
							include_once 'php/display/allPostsTable.php';
						else if ($_GET['posts'] == 'yours')
							include_once 'php/display/yourPostsTable.php';
						else{
							errorMessage("Si è verificato un errore...", "La pagina che cerchi non esiste!");
							die();
						}
					}
					else if(isset($_GET['editor'])){
						if($_GET['editor'] == 'new' || ctype_digit($_GET['editor']))
							include_once 'php/display/editorPanel.php';
						else{
							errorMessage("Si è verificato un errore...", "La pagina che cerchi non esiste!");
							die();
						}
					}
					else if(isset($_GET['comments'])){
						if($_GET['comments'] == 'all' || $_GET['comments'] == 'yours' || $_GET['comments'] == 'moderation')
							include_once 'php/display/commentTable.php';
						else{
							errorMessage("Si è verificato un errore...", "La pagina che cerchi non esiste!");
							die();
						}
					}
					else if(isset($_GET['users'])){
						if($_GET['users'] == 'all' || $_GET['users'] == 'staff')
							include_once 'php/display/userCards.php';
						else if(ctype_digit($_GET['users']))
							include_once 'php/display/userProfile.php';
						else{
							errorMessage("Si è verificato un errore...", "La pagina che cerchi non esiste!");
							die();
						}
					}
					else if(isset($_GET['settings'])){
						if($_GET['settings'] == 'users')
							include_once 'php/display/adminUserSettings.php';
						else if($_GET['settings'] == 'general')
							include_once 'php/display/adminGeneralSettings.php';
						else if($_GET['settings'] == 'categories')
							include_once 'php/display/adminCategorySettings.php';
						else{
							errorMessage("Si è verificato un errore...", "La pagina che cerchi non esiste!");
							die();
						}
					}
					else{
						include_once 'php/display/userProfile.php';
					}
				?>
			</main>
		</div>
		<footer>
			<address>Copyright &copy; 2015 - <a href='mailto:kevincorizi@outlook.com' target='blank'>Kevin Corizi</a></address>
			<p>Icons by <a href='https://google.github.io/material-design-icons/' target='blank'>Google Material Icons</a> - <a href='help.php'>Guida al servizio</a></p>
		</footer>
	</body>
</html>