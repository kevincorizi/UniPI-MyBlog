/*Classe User*/

/*Costruttore: riceve un oggetto PHP e lo converte in oggetto User*/
function User(user){
	this.id = user.id;
	this.username = user.username;
	this.email = user.email;
	this.name = user.name;
	this.surname = user.surname;
	this.password = user.password;
	this.role = user.role
	this.picture = user.picture;
}