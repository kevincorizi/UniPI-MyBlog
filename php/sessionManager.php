<?php
	/*Funzioni di utilità per la gestione delle sessioni*/
	function startSession(){
		if (!isset($_SESSION)) {
			session_start();
		}
	}

	function isLogged(){
		return (isset($_SESSION['id']));
	}

	function logout(){
		if (isset($_SESSION)) {
			session_destroy();
			header('Location: ../index.php');
		}
	}
?>