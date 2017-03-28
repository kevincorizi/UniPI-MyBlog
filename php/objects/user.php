<?php
	/*Classe User*/
	class User{
		/*Informazioni ricavabili dalla tupla*/
		public $id;
		public $username;
		public $email;
		public $name;
		public $surname;
		public $password;
		public $role;
		public $picture;

		/*Informazioni ricavabili tramite query aggiuntive*/
		public $comments;
		public $posts;
		public $likes;
		public $likesSet;
		public $dislikesSet;

		/*Costruttore*/
		function __construct($id, $username, $email, $name, $surname, $password, $role, $profilePicture){
			$this->id = $id;
			$this->username = $username;
			$this->email = $email;
			$this->name = $name;
			$this->surname = $surname;
			$this->password = $password;
			$this->role = $role;
			$this->picture = $profilePicture;

			$this->comments = array();
			$this->posts = array();
			$this->likes = array();
			$this->likesSet = 0;
			$this->dislikesSet = 0;
			$this->popularity = 0;
		}

		/*Funzione di visualizzazione del profilo nel pannello di controllo*/
		function showProfile(){
			echo "<div class='profile'>";
			if($this->role == 'admin')
				echo "<div class='profileData dataAdmin'>";
			else if($this->role=='mod')
				echo "<div class='profileData dataMod'>";
			else
				echo "<div class='profileData dataUser'>";
			
			echo "<div class='profilePictureBig'>";
			echo "<img class='profilePicture' src='".$this->picture."' alt='".$this->username."'>";
			if($this->id == $_SESSION['id'])
				echo "<img class='changePicture' src='assets/icons/editRound.png' alt='Cambia foto' onclick='showPictureURLFlyout()'>";
			echo "</div>";
			echo "<p>".$this->name." ".$this->surname."</p>";
			echo "<p>".$this->username."</p>";
			echo "<p>".$this->email."</p>";

			if($this->role == 'admin')
				echo "<p>Amministratore</p>";
			else if($this->role=='mod')
				echo "<p>Moderatore</p>";
			else
				echo "<p>Utente</p>";
			
			echo "</div>";

			$publishedPosts = count(fetchPosts("SELECT * FROM post WHERE author=".$this->id));
			$publishedComments = count(fetchComments("SELECT * FROM comment WHERE author=".$this->id));

			echo "<div class='profileActivity'>";
			if($this->id == $_SESSION['id']){
				echo "<p class='profileHeader'>La tua attività</p>";
				echo "<ul class='activityList'>";
				echo "<li class='activityItem'>Hai pubblicato ".$publishedPosts;
				if($publishedPosts == 1)
					echo " articolo e ";
				else
					echo " articoli e ";
				echo $publishedComments;
				if($publishedComments == 1)
					echo " commento.</li>";
				else
					echo " commenti.</li>";

				$values = userSocialData($this);
				echo "<li class='activityItem'>I tuoi articoli hanno ricevuto ".$values[0]." 'mi piace' e ".$values[1]." 'non mi piace'.</li>";
				echo "<li class='activityItem'>I tuoi commenti hanno ricevuto ".$values[2]." 'mi piace' e ".$values[3]." 'non mi piace.</li>";
				echo "<li class='activityItem'>I tuoi commenti sono stati segnalati ".$values[4]." volte.</p>";
				echo "</ul>";
				echo "<p class='activityItem'>Hai realizzato ".userPopularity($this)." punti popolarità. Continua così!</p>";
			}
			else{
				echo "<p class='profileHeader'>Attività di ".$this->username."</p>";
				echo "<ul class='activityList'>";
				echo "<li class='activityItem'>Ha pubblicato ".$publishedPosts;
				if($publishedPosts == 1)
					echo " articolo e ";
				else
					echo " articoli e ";
				echo $publishedComments;
				if($publishedComments == 1)
					echo " commento.</li>";
				else
					echo " commenti.</li>";

				$values = userSocialData($this);
				echo "<li class='activityItem'>I suoi articoli hanno ricevuto ".$values[0]." 'mi piace' e ".$values[1]." 'non mi piace'.</li>";
				echo "<li class='activityItem'>I suoi commenti hanno ricevuto ".$values[2]." 'mi piace' e ".$values[3]." 'non mi piace.</li>";
				echo "<li class='activityItem'>I suoi commenti sono stati segnalati ".$values[4]." volte.</li>";
				echo "</ul>";
				echo "<p class='activityItem'>Ha realizzato ".userPopularity($this)." punti popolarità.</p>";
			}
			echo "</div>";
			echo "</div>";
			
		}

		/*Funzione di visualizzazione degli utenti nel pannello di controllo*/
		function showCard(){
			echo "<div class='userInfo'>";
			echo "<div class='profilePictureSmall'><img class='profilePicture' src='".$this->picture."' alt='".$this->username."'></div>";
			echo "<div class='userData'>";
			echo "<a class='userUsername' id='user_".$this->id."' href='?users=".$this->id."'>".$this->username."</a>";
			echo "<p class='userNameAndSurname'>".$this->name." ".$this->surname."</p>";
			echo "<a class='userEmail' href='mailto:".$this->email."'>".$this->email."</a>";
			if($this->role == 'mod')
				echo "<p class='userRole'>Moderatore</p>";
			else if($this->role == 'admin')
				echo "<p class='userRole'>Amministratore</p>";
			else
				echo "<p class='userRole'>Utente</p>";
			echo "<p class='userNumberComments'>".$this->comments." commenti inviati</p>";
			echo "<p class='userPopularity'>Popolarità: ".userPopularity($this)."</p>";
			echo "</div>";
			echo "</div>";
		}

		/*Funzione di visualizzazione degli utenti come righe della tabella nel pannello di controllo*/
		function showRow(){
			echo "<tr class='tableRow userRow' id='user_".$this->id."'>";
			echo "<td><a class='userUsername' href='?users=".$this->id."'>".$this->username."</a></td>";
			echo "<td><p class='userNameRow'>".$this->name."</p></td>";
			echo "<td><p class='userSurname'>".$this->surname."</p></td>";
			echo "<td><p class='userEmail'><a href='mailto:".$this->email."'>".$this->email."</a></p></td>";

			/*Un moderatore non può modificare il grado di altri moderatori o degli amministratori*/
			if(($_SESSION['role'] == 'mod' && $this->role != 'user') || $this->id == $_SESSION['id'])
				echo "<td><select class='userRole' name='role_".$this->id."' disabled>";
			else
				echo "<td><select class='userRole' name='role_".$this->id."'>";
			if($this->role == 'user'){
				echo "<option value='user' selected>Utente</option>";
				echo "<option value='mod'>Moderatore</option>";
				echo "<option value='admin'>Amministratore</option>";
			}
			else if($this->role == 'mod'){
				echo "<option value='user'>Utente</option>";
				echo "<option value='mod' selected>Moderatore</option>";
				echo "<option value='admin'>Amministratore</option>";
			}
			else if($this->role == 'admin'){
				echo "<option value='user'>Utente</option>";
				echo "<option value='mod'>Moderatore</option>";
				echo "<option value='admin' selected>Amministratore</option>";
			}
			echo "</select></td>";

			$values = userSocialData($this);
			echo "<td><p class='userLikes'>".($values[0] + $values[2])."</p></td>";
			echo "<td><p class='userDislikes'>".($values[1] + $values[3])."</p></td>";
			if(($_SESSION['role'] == 'mod' && $this->role != 'user') || $this->id == $_SESSION['id'])
				echo "<td><button class='button' type='button' disabled><img src='assets/icons/deleteBlack.png' alt='Elimina'></button></td>";
			else
				echo "<td><button class='button' type='button'><img src='assets/icons/deleteBlack.png' alt='Elimina' onclick=\"deleteUser(".$this->id.")\"></button></td>";
			echo "</tr>";
		}
	}
?>