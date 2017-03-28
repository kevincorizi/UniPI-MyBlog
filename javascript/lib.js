/*Funzioni di utilità per più pagine del sito*/

/*Funzione per la gestione dei colori del background in join.php e setup.php*/
iterations = -1;

function startSlideshow(){
	var colors = ['#2196F3', '#00BCD4', '#4CAF50', '#FFEB3B', '#F44336', '#E91E63', '#673AB7'];
	setInterval(changePicture, 2000, colors);
}

function changePicture(colors){
	iterations++;
	index = iterations % colors.length;
	document.body.style.background = colors[index];
}

/*Funzione di validazione form: controlla se le password inserite in fase di registrazione coincidono*/
function validateForm(form){
	if(form['password'].value !== form['repeatPassword'].value){
		reportErrors('pwdmatch');
		return false;
	}
	else{
		return true;
	}
}

/*Funzione di visualizzazione errori*/
function reportErrors(error){
	switch(error){
		case 'pwdmatch':
			var message = new Message('error', document.getElementById('wrapper'), 0, 'Errore!', 'Le password non coincidono');
			message.displayMessage();
			break;
		case 'username':
			var message = new Message('error', document.getElementById('wrapper'), 0, 'Errore!', 'Questo utente non esiste');
			message.displayMessage();
			break;
		case 'duplicate':
			var message = new Message('error', document.getElementById('wrapper'), 0, 'Errore!', 'Un altro utente ha già usato lo stesso username o la stessa email. Riprova!');
			message.displayMessage();
			break;
		case 'password':
			var message = new Message('error', document.getElementById('wrapper'), 0, 'Errore!', 'La password inserita non è corretta');
			message.displayMessage();
			break;
		default:
			break;
	}
}

/*Funzione di conversione di una data PHP in data JS*/
function JSDate(phpDate){
	if(phpDate instanceof Date){
		return phpDate;
	}
	else if(phpDate != undefined){
		var date = phpDate.split(' ')[0];
		var time = phpDate.split(' ')[1];

		var year = parseInt(date.split('-')[0]);
		var month = parseInt(date.split('-')[1]) - 1;
		var day = parseInt(date.split('-')[2]);

		var hour = parseInt(time.split(':')[0]);
		var minute = parseInt(time.split(':')[1]);
		var second = parseInt(time.split(':')[2]);

		var jsDate = new Date(year, month, day, hour, minute, second);

		return jsDate;
	}
}

/*Funzione di visualizzazione della data in due formati
SHORT: dd/mm/aaaa hh:mm
LONG: dd month aaaa hh:mm
*/
function formatDate(jsDate, format){
	var months = ['Gennaio', 'Febbraio', 'Marzo', 'Aprile', 'Maggio', 'Giugno', 'Luglio', 'Agosto', 'Settembre', 'Ottobre', 'Novembre', 'Dicembre'];
	var day = jsDate.getDate() < 10 ? '0' + jsDate.getDate() : jsDate.getDate();
	var monthL = months[jsDate.getMonth()];
	var monthS = jsDate.getMonth() + 1 < 10 ? '0' + (jsDate.getMonth() + 1) : jsDate.getMonth() + 1;
	var year = jsDate.getFullYear();

	var hour = jsDate.getHours() < 10 ? '0' + jsDate.getHours() : jsDate.getHours();
	var minute = jsDate.getMinutes() < 10 ? '0' + jsDate.getMinutes() : jsDate.getMinutes();

	if(format == 'short'){
		return day + '/' + monthS + '/' + year + ' ' + hour + ':' + minute;
	}
	else if(format == 'long'){
		return day + ' ' + monthL + ' ' + year + ' ' + hour + ':' + minute;
	}
}

/*Funzione di visualizzazione del flyout per la modifica dell'immagine del profilo*/
function showPictureURLFlyout(){
	var overlay = document.createElement('div');
	overlay.className = 'overlayDiv';

	var div = document.createElement('div');
	div.className = 'flyout pictureURLFlyout';

	var p = document.createElement('p');
	p.className = 'messageText';
	p.textContent = "Inserisci l'URL della tua nuova immagine del profilo";

	var close = document.createElement('button');
	close.className = 'button messageClose';
	close.onclick = function(){
		document.getElementsByClassName('overlayDiv')[0].remove();
	}

	var img = document.createElement('img');
	img.src = '../assets/icons/closeBlack.png';
	img.alt = 'Chiudi';
	close.appendChild(img);

	var form = document.createElement('form');
	form.id = 'profilePictureForm';
	form.action = '../php/data/changeProfilePicture.php';
	form.method = 'POST';

	var input = document.createElement('input');
	input.type = 'url';
	input.className = 'largeField';
	input.placeholder = 'URL della nuova immagine...';
	input.name = 'newPicture';
	input.required = 'required';

	var submit = document.createElement('button');
	submit.type = 'submit';
	submit.className = 'button largeButton';
	submit.name = 'changePicture';
	submit.textContent = 'Modifica';

	div.appendChild(p);
	div.appendChild(close);
	div.appendChild(form);
	form.appendChild(input);
	form.appendChild(submit);
	overlay.appendChild(div);

	document.body.appendChild(overlay);
}

/*Funzione di visualizzazione del flyout per la modifica delle categorie*/
function showCategoryFlyout(category){
	var overlay = document.createElement('div');
	overlay.className = 'overlayDiv';

	var div = document.createElement('div');
	div.className = 'flyout categoryFlyout';

	if(category == undefined){
		var p = document.createElement('p');
		p.className = 'messageText';
		p.textContent = 'Inserisci il nome e il colore della nuova categoria';
	}
	else{
		var p = document.createElement('p');
		p.className = 'messageText';
		p.textContent = 'Modifica il nome o il colore della categoria';
	}
	var close = document.createElement('button');
	close.className = 'button messageClose';
	close.onclick = function(){
		document.getElementsByClassName('overlayDiv')[0].remove();
	}

	var img = document.createElement('img');
	img.src = '../assets/icons/closeBlack.png';
	img.alt = 'Chiudi';
	close.appendChild(img);

	var form = document.createElement('form');
	form.id = 'categoryForm';
	form.action = '#';
	form.method = 'POST';

	var hiddenId = document.createElement('input');
	hiddenId.type = 'hidden';
	hiddenId.name = 'categoryId';
	if(category != undefined)
		hiddenId.value = category.id;
	else
		hiddenId.value = '';
	form.appendChild(hiddenId);

	var input = document.createElement('input');
	input.type = 'text';
	input.name = 'newCategoryName';
	input.className = 'largeField';
	if(category == undefined)
		input.placeholder = 'Nome della categoria...';
	else
		input.value = category.name;
	input.required = 'required';

	var color = document.createElement('input');
	color.type = 'color';
	color.name = 'newCategoryColor';
	if(category == undefined)
		color.value = '#000000';
	else
		color.value = category.color;

	var submit = document.createElement('button');
	submit.type = 'button';
	submit.className = 'button largeButton';
	
	if(category == undefined){
		submit.id = 'addCategory';
		submit.textContent = 'Aggiungi';		
	}
	else{
		submit.id = 'editCategory';
		submit.textContent = 'Modifica';
	}
	submit.onclick = submitForm.bind(submit);

	div.appendChild(p);
	div.appendChild(close);
	div.appendChild(form);
	form.appendChild(input);
	form.appendChild(color);
	form.appendChild(submit);

	if(category != undefined){
		var remove = document.createElement('button');
		remove.type = 'button';
		remove.className = 'button largeButton';
		remove.id = 'deleteCategory';
		remove.textContent = 'Elimina categoria';
		remove.onclick = submitForm.bind(remove);
		form.appendChild(remove);
	}
	overlay.appendChild(div);

	document.body.appendChild(overlay);
}

/*Funzione di invio delle modifiche a una categoria tramite flyout.
Mostra una richiesta di conferma prima di sottomettere la form e apportare le modifiche.
*/
function submitForm(event){
	event.preventDefault();
	var confirmMessage = (this.id == 'addCategory') ? 'Vuoi aggiungere questa categoria?' : (this.id == 'editCategory') ? 'Vuoi davvero apportare le modifiche?' : 'Vuoi davvero eliminare la categoria?';
	if(!document.getElementsByClassName('confirmMessageContainer')[0]){
		var confirm = new Confirmation('confirm', 
					document.getElementById('categoryForm').parentNode, 
					3, 
					'Sei sicuro?', 
					confirmMessage, 
					'Si', 
					function(){
						var input = document.createElement('input');
						input.type = 'hidden';
						input.name = this.id;
						input.value = document.forms['categoryForm']['categoryId'].value;
						document.forms['categoryForm'].appendChild(input);
						document.forms[0].submit();
					}.bind(this), 
					'No');
				confirm.displayConfirmation();
	}
	else 
		return false;
}

/*Funzione di visualizzazione dei post oltre i 20.
Vengono caricati 20 elementi alla volta tramite chiamata asincrona, 
mantenendo eventuali ordinamenti. La visualizzazione avviene
con le funzioni di visualizzazione degli oggetti JS*/
function showMore(filter, order){
	var target = location.search.split('=')[0].split('?')[1];
	var tab = location.search.split('=')[1];

	switch(target){
		case 'posts':
			var start = document.getElementById('postTable').getElementsByTagName('tbody')[0].childNodes.length;
			AsyncManager('./php/data/getPosts.php', [tab, start, filter, order], function(posts){
				if(posts != ''){
					for(var i = 0; i < posts.length; i++){
						var post = new Post(posts[i]);
						post.appendRowPost();
					}
					disableShowMore(posts.length);
				}
			});
			break;
		case 'comments':
			var start = document.getElementById('commentTable').getElementsByTagName('tbody')[0].childNodes.length;
			AsyncManager('./php/data/getComments.php', [tab, start, filter, order], function(comments){
				if(comments != ''){
					for(var i = 0; i < comments.length; i++){
						var comment = new Comment(comments[i]);
						comment.appendRowComment();
					}
					disableShowMore(comments.length);
				}
			});
			break;
		case 'category':
			var start = document.getElementsByClassName('post').length;
			AsyncManager('./php/data/getPosts.php', ['category', start, tab, order], function(posts){
				if(posts != ''){
					for(var i = 0; i < posts.length; i++){
						var post = new Post(posts[i]);
						post.appendCardPost();
					}
					disableShowMore(posts.length);
				}
			});
			break;
		case 'search':
			var start = document.getElementsByClassName('post').length;
			AsyncManager('./php/data/getPosts.php', ['search', start, tab, order], function(posts){
				if(posts != ''){
					for(var i = 0; i < posts.length; i++){
						var post = new Post(posts[i]);
						post.appendCardPost();
					}
					disableShowMore(posts.length);
				}
			});
			break;
		case undefined:
			var start = document.getElementsByClassName('post').length;
			AsyncManager('./php/data/getPosts.php', ['', start, filter, order], function(posts){
				if(posts != ''){
					for(var i = 0; i < posts.length; i++){
						var post = new Post(posts[i]);
						post.appendCardPost();
					}
					disableShowMore(posts.length);
				}
			});
			break;
	}
}

/*Funzione che disattiva il pulsante di richiesta di ulteriori elementi
nel caso in cui con l'ultima richiesta si siano terminati gli elementi*/
function disableShowMore(count){
	if(count < 20){
		document.getElementsByClassName('button largeButton')[0].style.display = 'none';
	}
}