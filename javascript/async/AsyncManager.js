/*Funzione per la gestione delle chiamate asincrone*/

function AsyncManager(url, filters, action){
	var xhr = new XMLHttpRequest();
	xhr.onreadystatechange = function(){
		if(xhr.readyState == 4 && xhr.status == 200){
			/*DEBUG*/
			//console.log(xhr.responseText);
			action(JSON.parse(xhr.responseText));

		}
	};
	destination = url + '?';
	for(var i = 0; i < filters.length; i++){
		destination = destination + 'filter' + i + '=' + filters[i];
		if(i != filters.length - 1){
			destination = destination + '&';
		}
	}
	xhr.open('GET', destination, true);
	xhr.send();
}