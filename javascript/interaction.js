/*Funzioni di utilità l'interazione con i commenti, con gli articoli e con gli utenti*/

/*Aggiunge un 'mi piace' a un articolo, e adegua i pulsanti di conseguenza*/
function newPostLike(author, action, post){
	AsyncManager('./php/data/newLike.php', [author, post, action, 'post'], function(result){
		if (result == true){
			var SL = document.getElementsByClassName('setLike')[0];
			var SD = document.getElementsByClassName('setDislike')[0];
			var CL = document.getElementById('numberLikes');

			if(action == 'add'){
				//Il pulsante 'mi piace' rimuove il 'mi piace'
				CL.textContent = parseInt(CL.textContent) + 1;
				SL.disabled = false;
				SL.onclick = null;
				SL.onclick = function(){
					newPostLike(author, 'sub', post);
				}
				//Non è possibile aggiungere 'non mi piace'
				SD.onclick = null;
				SD.disabled = true;
			}
			else{
				CL.textContent = parseInt(CL.textContent) - 1;
				//Il pulsante 'mi piace' aggiunge il 'mi piace'
				SL.disabled = false;
				SL.onclick = null;
				SL.onclick = function(){
					newPostLike(author, 'add', post);
				}
				//E' possibile aggiungere 'non mi piace'
				SD.disabled = false;
				SD.onclick = null;
				SD.onclick = function(){
					newPostDislike(author, 'add', post);
				}
			}
		}
		else{
			var message = new Message('error', document.getElementsByClassName('postCommunity')[0], 2, 'Errore', 'Non è stato possibile aggiungere il Mi Piace, riprova più tardi!');
			message.displayMessage();
		}
	});
}

/*Aggiunge un 'non mi piace' a un articolo, e adegua i pulsanti di conseguenza*/
function newPostDislike(author, action, post){
	AsyncManager('./php/data/newDislike.php', [author, post, action, 'post'], function(result){
		if (result == true){
			var SL = document.getElementsByClassName('setLike')[0];
			var SD = document.getElementsByClassName('setDislike')[0];
			var CD = document.getElementById('numberDislikes');

			if(action == 'add'){
				CD.textContent = parseInt(CD.textContent) + 1;
				//Non è possibile aggiungere 'mi piace'
				SL.onclick = null;
				SL.disabled = true;
				//Il pulsante 'non mi piace' rimuove il 'non mi piace'
				SD.disabled = false;
				SD.onclick = null;
				SD.onclick = function(){
					newPostDislike(author, 'sub', post);
				};
			}
			else{
				CD.textContent = parseInt(CD.textContent) - 1;
				//Il pulsante 'mi piace' aggiunge il 'mi piace'
				SL.disabled = false;
				SL.onclick = null;
				SL.onclick = function(){
					newPostLike(author, 'add', post);
				};
				//Il pulsante 'non mi piace' aggiunge il 'non mi piace'
				SD.disabled = false;
				SD.onclick = null;
				SD.onclick = function(){
					newPostDislike(author, 'add', post);
				};
			}
		}
		else{
			var message = new Message('error', document.getElementsByClassName('postCommunity')[0], 2, 'Errore', 'Non è stato possibile aggiungere il Non Mi Piace, riprova più tardi!');
			message.displayMessage();
		}
	});
}

/*Aggiunge un commento*/
function newComment(author, text, post){
	if(text != ''){
		AsyncManager('./php/data/newComment.php', [post, author, text], function(result){
			if (!(result instanceof Object)){
				if(!document.getElementsByClassName('errorMessageContainer')[0]){
					var message = new Message('error', document.getElementsByClassName('postCommunity')[0], 2, 'Errore', 'Non è stato possibile aggiungere il commento, riprova più tardi!');
					message.displayMessage();
				}
			}
			else{
				var comment = new Comment(result);
				comment.appendCardComment();
			}
		});
		document.getElementsByClassName('newComment')[0].childNodes[0].value = '';
	}
	else{
		if(!document.getElementsByClassName('errorMessageContainer')[0]){
			var message = new Message('error', document.getElementsByClassName('postCommunity')[0], 1, 'Errore', 'Che commenti a fare se non dici niente? :)');
			message.displayMessage();
		}
	}
}

/*Mostra la richesta di conferma, e eventualmente elimina il commento*/
function deleteComment(selected){
	if(!document.getElementsByClassName('confirmMessageContainer')[0]){
		var confirm = new Confirmation('confirm', 
						document.getElementById('comment_' + selected).parentNode, 
						5, 
						'Sei sicuro?', 
						'Vuoi davvero eliminare il commento?', 
						'Si', 
						function(){
							AsyncManager('./php/data/deleteComment.php', [selected], function(result){
								if (result == true)
									document.getElementById('comment_' + selected).parentNode.remove();
								else{
									var message = new Message('error', document.getElementById('comment_' + selected).parentNode, 1, 'Errore', "C'è stato un problema con l'eliminazione del commento. Riprova più tardi!");
									message.displayMessage();
								}
							});
						}, 
						'No');
		confirm.displayConfirmation();
	}
}

/*Mostra la richesta di conferma, e eventualmente elimina l'utente*/
function deleteUser(selected){
	if(!document.getElementsByClassName('confirmMessageContainer')[0]){
		var confirm = new Confirmation('confirm', 
						document.getElementById('user_' + selected), 
						9, 
						'Sei sicuro?', 
						"Vuoi davvero eliminare l'utente?", 
						'Si', 
						function(){
							AsyncManager('./php/data/deleteUser.php', [selected], function(result){
								if (result == true)
									document.getElementById('user_' + selected).remove();
								else{
									var message = new Message('error', document.getElementById('user_' + selected), 1, 'Errore', "C'è stato un problema con l'eliminazione dell'utente. Riprova più tardi!");
									message.displayMessage();
								}
							});
						}, 
						'No');
		confirm.displayConfirmation();
	}
}

/*Aggiunge un 'mi piace' a un commento, e adegua i pulsanti di conseguenza*/
function newCommentLike(author, action, selected){
	AsyncManager('./php/data/newLike.php', [author, selected, action, 'comment'], function(result){
		if (result == true){
			var comment = document.getElementById('comment_' + selected).parentNode;

			var SL = comment.getElementsByClassName('button')[0];
			var SD = comment.getElementsByClassName('button')[1];
			var CL = comment.getElementsByClassName('count')[0];

			if(action == 'add'){
				CL.textContent = parseInt(CL.textContent) + 1;
				//Il pulsante 'mi piace' rimuove il 'mi piace'
				SL.disabled = false;
				SL.firstChild.onclick = null;
				SL.firstChild.onclick = function(){
					newCommentLike(author, 'sub', selected);
				};
				//Non è possibile aggiungere 'non mi piace'	
				SD.firstChild.onclick = null;
				SD.disabled = true;
			}
			else{
				CL.textContent = parseInt(CL.textContent) - 1;
				//Il pulsante 'mi piace' aggiunge il 'mi piace'
				SL.disabled = false;
				SL.firstChild.onclick = null;
				SL.firstChild.onclick = function(){
					newCommentLike(author, 'add', selected);
				};
				//E' possibile aggiungere 'non mi piace'
				SD.disabled = false;
				SD.firstChild.onclick = null;
				SD.firstChild.onclick = function(){
					newCommentDislike(author, 'add', selected);
				};
			}
		}
	});
}

/*Aggiunge un 'non mi piace' a un commento, e adegua i pulsanti di conseguenza*/
function newCommentDislike(author, action, selected){
	AsyncManager('./php/data/newDislike.php', [author, selected, action, 'comment'], function(result){
		//ottengo il contenuto dei div di conto e add o sub a seconda del parametro
		if (result == true){
			var comment = document.getElementById('comment_' + selected).parentNode;
			var SL = comment.getElementsByClassName('button')[0];
			var SD = comment.getElementsByClassName('button')[1];
			var CD = comment.getElementsByClassName('count')[1];


			if(action == 'add'){
				CD.textContent = parseInt(CD.textContent) + 1;
				//Non è possibile aggiungere 'mi piace'
				SL.firstChild.onclick = null;
				SL.disabled = true;
				//Il pulsante 'non mi piace' rimuove il 'non mi piace'
				SD.disabled = false;
				SD.firstChild.onclick = null;
				SD.firstChild.onclick = function(){
					newCommentDislike(author, 'sub', selected);
				};
			}
			else{
				CD.textContent = parseInt(CD.textContent) - 1;
				//Il pulsante 'mi piace' aggiunge il 'mi piace'
				SL.disabled = false;
				SL.firstChild.onclick = null;
				SL.firstChild.onclick = function(){
					newCommentLike(author, 'add', selected);
				};
				//Il pulsante 'non mi piace' aggiunge il 'non mi piace'
				SD.disabled = false;
				SD.firstChild.onclick = null;
				SD.firstChild.onclick = function(){
					newCommentDislike(author, 'add', selected);
				};
			}
		}
	});
}

/*Aggiunge una segnalazione a un commento, e adegua i pulsanti di conseguenza*/
function newReport(selected){
	AsyncManager('./php/data/newReport.php', [selected], function(result){
		if (result == true){
			var comment = document.getElementById('comment_' + selected).parentNode;

			var AR = comment.getElementsByClassName('button')[2];
			var CR = comment.getElementsByClassName('count')[2];
			AR.disabled = true;
			CR.textContent = parseInt(CR.textContent) + 1;
		}
	});
}