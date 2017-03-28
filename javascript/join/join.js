/*Funzioni di utilità per join.php*/

/*Associata alla onload() del body*/
function loadJoin(){
	startSlideshow();

	var error = location.search.split("=")[1];
	reportErrors(error);
}