/*Funzioni di utilità per index.php*/

/*Associata alla onload() del body*/
function loadIndex(){
	initUserNavigation();

	/*Controllo se ci sono stati errori nella navigazione*/
	if(location.search.split('=')[0] == '?error'){
		var error = location.search.split('=')[1];
		switch(error){
			case 'invalidpicture':
				var message = new Message('error', document.getElementById('postflow'), 0, 'Errore!', "Non è stato possibile aggiornare l'immagine del profilo. Prova con un altro URL!");
				message.displayMessage();
				break;
			case 'accessdenied':
				var message = new Message('error', document.getElementById('postflow'), 0, 'Errore!', "Non sei autorizzato a accedere al pannello di controllo.");
				message.displayMessage();
				break;
			default:
				break;
		}
	}
}

/*Imposta la larghezza della barra laterale*/
function initUserNavigation(){
	document.getElementById('userNavigation').style.width = '0px';
}

/*Gestisce l'apertura e la chiusura della barra laterale*/
function toggleUserNavigation(){
	document.getElementById('userNavigation').style.width = '250px';
	document.body.addEventListener('click', initUserNavigation, true);
}

/*Gestisce il box di ricerca*/
function monitorSearchBar(event){
	var bar = document.getElementById('searchInput');
	var key = event.keyCode || event.charCode;

    if(key == 13 && bar.value != ''){
        location.href = '?search=' + bar.value;
    }
}

