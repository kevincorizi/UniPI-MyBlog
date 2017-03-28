<?php
	/*Classe Category*/
	class Category{
		public $id;
		public $name;
		public $color;

		/*Costruttore*/
		function __construct($id, $name, $color){
			$this->id = $id;
			$this->name = $name;
			$this->color = $color;
		}

		/*Funzione di visualizzazione per il pannello di controllo*/
		function showCard(){
			global $conn;
			echo "<div class='categoryCard' id='category_".$this->id."' style='background: ".$this->color."'>";
			echo "<p>".$this->name."</p>";
			echo "<img src='assets/icons/editWhite.png' alt='Modifica categoria' onclick=\"showCategoryFlyout(".str_replace("\"", "'", json_encode($this)).")\">";
			echo "<p>".count(fetchPosts("SELECT * FROM post WHERE category=".$this->id))." post in questa categoria</p>";
			echo "</div>";
		}

		/*Funzione di visualizzazione per la pagina iniziale*/
		function showSmall(){
			echo "<li class='categoryElement' id='category_".$this->id."' style='background-color: ".$this->color.";'>";
			echo "<label class='categoryName'><a href='?category=".$this->id."'>".$this->name."</a></label>";
			echo "</li>";
		}
	}
?>