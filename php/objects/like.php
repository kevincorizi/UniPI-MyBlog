<?php
	/*Classe Like*/
	class Like{
		public $id;
		public $type;
		public $onPost;
		public $onComment;
		public $userId;
		public $date;

		/*Costruttore*/
		function __construct($id, $type, $onPost, $onComment, $userId, $date){
			$this->id = $id;
			$this->type = $type;
			$this->onPost = $onPost;
			$this->onComment = $onComment;
			$this->userId = $userId;
			$this->date = $date;
		}
	}
?>