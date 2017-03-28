/*Funzioni di utilità per setup.php*/

/*Associata alla onload() del body*/
function loadSetup(){
	startSlideshow();

	var error = location.search.split("=")[1];
	reportErrors(error);
}