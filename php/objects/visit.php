<?php
	/*Classe Visit*/
	class Visit{
		public $userId;
		public $dateViewed;
		
		/*Costruttore*/
		function __construct($user, $date){
			$this->userId = $user;
			$this->dateViewed = strtotime($date);
		}
	}
?>