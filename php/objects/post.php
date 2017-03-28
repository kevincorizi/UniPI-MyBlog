<?php
	/*Classe Post*/
	class Post{
		/*Informazioni ottenibili direttamente dalla tupla*/
		public $id;
		public $title;
		public $text;
		public $author;
		public $dateCreated;
		public $dateLastModified;
		public $category;
		public $status;

		/*Informazioni ottenibili tramite query aggiuntive*/
		public $tags;
		public $likes;
		public $comments;
		public $visited;
		public $numberLikes;
		public $numberDislikes;

		/*Costruttore*/
		function __construct($id, $title, $text, $author, $dateCreated, $dateLastModified, $category, $status){
			$this->id = $id;
			$this->title = $title;
			$this->text = $text;
			$this->author = $author;
			$this->dateCreated = $dateCreated;
			$this->dateLastModified = $dateLastModified;
			$this->category = $category;
			$this->status = $status;

			$this->tags = array();
			$this->likes = array();
			$this->visited = array();
			$this->comments = array();
			$this->numberLikes = 0;
			$this->numberDislikes = 0;
		}	

		/*Funzione di visualizzazione nella pagina iniziale*/
		function showCard(){
			echo "<article class='post' id='post_".$this->id."'>";
			echo "<div class='postContent'>";
			echo "<h2 class='postTitle'>";
			echo "<a href='index.php?post=".$this->id."'>".$this->title."</a>";
			echo "</h2>";
			echo "<p class='postAuthor'>".$this->author->username."</p>";
			echo "<p class='postDate'>".toDate($this->dateLastModified, 'long')."</p>";

			/*Controllo del tag fittizio MORE*/
			if(!explode("<!-- more -->", $this->text)[0])
				echo $this->text;
			else{
				echo explode("<!-- more -->", $this->text)[0];
				if(isset(explode("<!-- more -->", $this->text)[1])){
					echo "</pre>";
					echo "<a href='index.php?post=".$this->id."'>Continua a leggere...</a>";
				}
			}
			echo "</div>";

			echo "<div class='postInfo'>";
			if(count($this->tags) == 0){
				echo "<p class='postTags'>Non ci sono tag!</p>";
			}
			else{
				echo "<p class='postTags'>Tag: ".implode(", ", $this->tags)."</p>";
			}
			echo "<div class='postCounts'>";
			echo "<div class='counts'>";
			echo "<img class='countIcon' src='assets/icons/commentWhite.png' alt='Commenti'>";
			echo "<div class='commentCount'>".$this->comments."</div>";
			echo "</div>";
			echo "<div class='counts'>";
			echo "<img class='countIcon' src='assets/icons/likeWhite.png' alt='Mi Piace'>";
			echo "<div class='likeCount'>".$this->numberLikes."</div>";
			echo "</div>";
			echo "<div class='counts'>";
			echo "<img class='countIcon' src='assets/icons/dislikeWhite.png' alt='Non Mi Piace'>";
			echo "<div class='dislikeCount'>".$this->numberDislikes."</div>";
			echo "</div>";
			echo "</div>";
			echo "</div>";
			echo "</article>";
		}

		/*Funzione di visualizzazione del post completo*/
		function showFull(){
			echo "<article class='post' id='post_".$this->id."'>";
			echo "<div class='postContent'>";
			echo "<h2 class='postTitle'>".$this->title."</h2>";
			echo "<p class='postAuthor'>".$this->author->username."</p>";
			echo "<p class='postDate'>".toDate($this->dateLastModified, 'long')."</p>";
			echo $this->text;
			echo "</div>";
			echo "<div class='postInfo'>";
			if(count($this->tags) == 0){
				echo "<p class='postTags'>Non ci sono tag!</p>";
			}
			else{
				echo "<p class='postTags'>Tag: ".implode(", ", $this->tags)."</p>";
			}			
			echo "<div class='postLikesDislikes'>";
			echo "Ci sono ";
			echo "<p id='numberLikes'>".$this->numberLikes."</p>";
			echo " likes e ";
			echo "<p id='numberDislikes'>".$this->numberDislikes."</p>";
			echo " dislike";
			echo "</div>";
			echo "</div>";

			echo "<div class='postInteraction'>";
			/*Solo se si è loggati, si può interagire con il post*/
			if(isset($_SESSION["id"])){
				if(count($this->likes) == 0){
					echo "<button type='button' class='button setLike' onclick=\"(function(){newPostLike(".$_SESSION["id"].", 'add', ".$this->id.");})()\">Mi piace</button>";
					echo "<button type='button' class='button setDislike' onclick=\"(function(){newPostDislike(".$_SESSION["id"].", 'add', ".$this->id.");})()\">Non mi piace</button>";
				}
				else{
					for($i = 0; $i < count($this->likes); $i++){
						if($this->likes[$i]->userId == $_SESSION["id"]){
							/*Se si è già messo 'mi piace' non è più possibile farlo*/
							if($this->likes[$i]->type == "L"){
								echo "<button type='button' class='button setLike' onclick=\"(function(){newPostLike(".$_SESSION["id"].", 'sub', ".$this->id.");})()\">Mi piace</button>";
								echo "<button type='button' class='button setDislike' disabled>Non mi piace</button>";
							}
							/*Se si è già messo 'non mi piace' non è più possibile farlo*/
							else{
								echo "<button type='button' class='button setLike' disabled>Mi piace</button>";
								echo "<button type='button' class='button setDislike' onclick=\"(function(){newPostDislike(".$_SESSION["id"].", 'sub', ".$this->id.");})()\">Non mi piace</button>";
							}
							break;
						}
						else if($i == count($this->likes) - 1){
							echo "<button type='button' class='button setLike' onclick=\"(function(){newPostLike(".$_SESSION["id"].", 'add', ".$this->id.");})()\">Mi piace</button>";
							echo "<button type='button' class='button setDislike' onclick=\"(function(){newPostDislike(".$_SESSION["id"].", 'add', ".$this->id.");})()\">Non mi piace</button>";
						}
					}
				}
			}

			echo "<div class='postCommunity'>";
			echo "<p class='communitySection'>Commenti</p>";
			/*Solo se si è loggati si può commentare*/
			if(isset($_SESSION['id'])){
				echo "<div class='newComment'>";
				echo "<textarea placeholder='Aggiungi un commento...'></textarea>";
				echo "<button type='button' class='button sendComment' onclick=\"(function(){newComment(".$_SESSION["id"].", document.getElementsByTagName('textarea')[0].value, $this->id);})()\">Invia</button>";
				echo "</div>";
			}
			echo "</div>";

			echo "<div class='postComments'>";
			if(count($this->comments) == 0){
				echo "<div class='emptyTarget'>Non ci sono ancora commenti su questo articolo!</div>";
			}
			else{
				for($i = 0; $i < count($this->comments); $i++){
					$this->comments[$i]->showCard();
				}
			}
			echo "</div>";
			echo "</div>";
			echo "</article>";
		}

		/*Funzione di visualizzazione per la tabella nel pannello di controllo*/
		/*FORMAT: discrimina tra la tabella di tutti i post e la tabella dei propri post*/
		function showRow($format){
			if($format == "all")
				echo "<tr class='tableRow postRow allPostRow' id='post_".$this->id."'>";
			else
				echo "<tr class='tableRow postRow yourPostRow' id='post_".$this->id."'>";
			echo "<td><a class='postTitle' href='index.php?post=".$this->id."'>".$this->title."</a></td>";
			echo "<td><p class='postDate'>".toDate($this->dateLastModified, 'short')."</p></td>";
			if($format == "all"){
				echo "<td><a class='postAuthor' href='?users=".$this->author->id."'>".$this->author->username."</a></td>";
			}
			echo "<td><p class='postComments'>".$this->comments."</p></td>";
			echo "<td><p class='postLikes'>".$this->numberLikes."</p></td>";
			echo "<td><p class='postDislikes'>".$this->numberDislikes."</p></td>";
			if(count($this->tags) == 0)
				echo "<td><p class='postTags'>Nessun tag</p></td>";
			else
				echo "<td><p class='postTags'>".implode(", ", $this->tags)."</p></td>";
			if($format == "user")
				echo "<td><button class='button' onclick=\"location.href='?editor=".$this->id."'\"><img src='assets/icons/editBlack.png' alt='Modifica'></button></td>";
			echo "</tr>";
		}
	}
?>