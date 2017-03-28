<?php
	/*Funzioni di utilità per la gestione della connessione al database*/
	require_once "config.php";

	$conn = new DatabaseInterface();
	class DatabaseInterface{
		private $dbConnector = null;

		function __construct(){
			$this->dbConnector = new mysqli(DATABASE_HOST, DATABASE_USERNAME, DATABASE_PASSWORD, DATABASE_NAME);
			$this->dbConnector->set_charset("utf8");
		}

		function query($query){
			$result = $this->dbConnector->query($query);
			return $result;
		}

		function secure($input){
			return $this->dbConnector->real_escape_string($input);
		}

		function getError(){
			return $this->dbConnector->error;
		}
	}
?>