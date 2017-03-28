<?php
	/*Classe Comment*/
	class Comment{
		/*Informazioni ottenibili direttamente dalla tupla*/
		public $id;
		public $postId;
		public $author;
		public $text;
		public $date;
		public $numberReported;

		/*Informazioni ottenibili tramite query aggiuntive*/
		public $likes;
		public $post;
		public $numberLikes;
		public $numberDislikes;
		
		/*Costruttore*/
		function __construct($id, $postId, $author, $text, $date, $numberReported){
			$this->id = $id;
			$this->postId = $postId;
			$this->author = $author;
			$this->text = $text;
			$this->date = $date;
			$this->numberReported = $numberReported;

			$this->likes = array();
			$this->post = array();
			$this->numberLikes = 0;
			$this->numberDislikes = 0;
		}

		/*Funzione di visualizzazione per il post*/
		function showCard(){
			echo "<div class='comment'>";
			echo "<a id='comment_".$this->id."'></a>";
			echo "<div class='userPictureContainer'>";
			echo "<img class='userPicture' src='".$this->author->picture."' alt='".$this->author->id."'>";
			echo "</div>";
			echo "<div class='commentData'>";
			echo "<p class='commentAuthor'>".$this->author->username."</p>";
			echo "<p class='commentDate'>".toDate($this->date, 'long')."</p>";
			echo "<p class='commentText'>".$this->text."</p>";
			echo "</div>";
			echo "<div class='commentCommunity'>";
			/*Se non si è loggati o si è l'autore del commento, non è possibile interagire con esso*/
			if(!isset($_SESSION["id"]) || $this->author->id == $_SESSION["id"]){
				echo "<div>";
				echo "<label class='count'>".$this->numberLikes."</label>";
				echo "<button class='button' type='button' disabled><img src='assets/icons/likeBlack.png' alt='Mi piace'></button>";
				echo "</div>";
				echo "<div>";
				echo "<label class='count'>".$this->numberDislikes."</label>";
				echo "<button class='button' type='button' disabled><img src='assets/icons/dislikeBlack.png' alt='Non mi piace'></button>";
				echo "</div>";
				echo "<div>";
				echo "<label class='count'>".$this->numberReported."</label>";
				echo "<button class='button' type='button' disabled><img src='assets/icons/reportBlack.png' alt='Segnalato'></button>";
				echo "</div>";		
			}
			/*Se non si è l'autore del commento, si può interagire con esso*/
			else if($this->author->id != $_SESSION["id"]){
				if(count($this->likes) == 0){
					echo "<div>";
					echo "<label class='count'>".$this->numberLikes."</label>";
					echo "<button class='button' type='button'><img src='assets/icons/likeBlack.png' alt='Mi piace' onclick=\"(function(){newCommentLike(".$_SESSION["id"].", 'add', ".$this->id.");})()\"></button>";
					echo "</div>";
					echo "<div>";
					echo "<label class='count'>".$this->numberDislikes."</label>";
					echo "<button class='button' type='button'><img src='assets/icons/dislikeBlack.png' alt='Non mi piace' onclick=\"(function(){newCommentDislike(".$_SESSION["id"].", 'add', ".$this->id.");})()\"></button>";
					echo "</div>";
				}
				else 
					for($i = 0; $i < count($this->likes); $i++){
						if($this->likes[$i]->userId == $_SESSION["id"]){
							/*Se si è già messo 'mi piace' non è più possibile farlo*/
							if($this->likes[$i]->type == "L"){
								echo "<div>";
								echo "<label class='count'>".$this->numberLikes."</label>";
								echo "<button class='button' type='button'><img src='assets/icons/likeBlack.png' alt='Mi piace' onclick=\"(function(){newCommentLike(".$_SESSION["id"].", 'sub', ".$this->id.");})()\"></button>";
								echo "</div>";
								echo "<div>";
								echo "<label class='count'>".$this->numberDislikes."</label>";
								echo "<button class='button' type='button' disabled><img src='assets/icons/dislikeBlack.png' alt='Non mi piace'></button>";
								echo "</div>";
							}
							/*Se si è già messo 'non mi piace' non è più possibile farlo*/
							else{
								echo "<div>";
								echo "<label class='count'>".$this->numberLikes."</label>";
								echo "<button class='button' type='button' disabled><img src='assets/icons/likeBlack.png' alt='Mi piace'></button>";
								echo "</div>";
								echo "<div>";
								echo "<label class='count'>".$this->numberDislikes."</label>";
								echo "<button class='button' type='button'><img src='assets/icons/dislikeBlack.png' alt='Non mi piace' onclick=\"(function(){newCommentDislike(".$_SESSION["id"].", 'sub', ".$this->id.");})()\"></button>";
								echo "</div>";
							}
							break;
						}
						else if($i == count($this->likes) - 1){
							echo "<div>";
							echo "<label class='count'>".$this->numberLikes."</label>";
							echo "<button class='button' type='button'><img src='assets/icons/likeBlack.png' alt='Mi piace' onclick=\"(function(){newCommentLike(".$_SESSION["id"].", 'add', ".$this->id.");})()\"></button>";
							echo "</div>";
							echo "<div>";
							echo "<label class='count'>".$this->numberDislikes."</label>";
							echo "<button class='button' type='button'><img src='assets/icons/dislikeBlack.png' alt='Non mi piace' onclick=\"(function(){newCommentDislike(".$_SESSION["id"].", 'add', ".$this->id.");})()\"></button>";
							echo "</div>";
						}
					}
				echo "<div>";
				echo "<label class='count'>".$this->numberReported."</label>";
				echo "<button class='button' type='button' onclick=\"(function(){newReport(".$this->id.");})()\"><img src='assets/icons/reportBlack.png' alt='Segnalato'></button>";
				echo "</div>";
			}
			//Se l'utente è l'autore del commento, o se l'utente è un moderatore/amministratore e il commento è in moderazione, lo può eliminare
			if(isset($_SESSION['id']) && ($_SESSION['id'] == $this->author->id || ($_SESSION['role'] != 'user' && $this->numberReported >= 3)))
				echo "<label class='deleteComment' onclick=\"(function(){deleteComment($this->id);})()\">Elimina</label>";

			echo "</div>";
			echo "</div>";
		}

		/*Funzione di visualizzazione per la tabella nel pannello di controllo*/
		function showRow(){
			echo "<tr class='tableRow commentRow' id='comment_".$this->id."'>";
			echo "<td><a class='commentText' href='index.php?post=".$this->postId."#comment_".$this->id."'>".$this->text."</a></td>";
			echo "<td><a class='commentPost' href='index.php?post=".$this->postId."'>".$this->post->title."</a></td>";
			echo "<td><a class='commentAuthor' href='?users=".$this->author->id."'>".$this->author->username."</a></td>";
			echo "<td><p class='commentDate'>".toDate($this->date, 'short')."</p></td>";
			echo "<td><p class='postLikes'>".$this->numberLikes."</p></td>";
			echo "<td><p class='postDislikes'>".$this->numberDislikes."</p></td>";
			echo "<td><p class='commentReported'>".$this->numberReported."</p></td>";
			echo "</tr>";
		}
	}
?>