/*Classe Message*/

/*Costruttore*/
function Message(type, target, position, header, text){
	this.type = type;
	this.target = target;
	this.position = position;
	this.header = header;
	this.text = text;
}

/*Visualizza il messaggio secondo i parametri specificati nel costruttore*/
Message.prototype.displayMessage = function(){
	var div = document.createElement('div');
	div.className = this.type == 'error' ? 'messageContainer errorMessageContainer' : 'messageContainer simpleMessageContainer';

	if(this.target.getElementsByClassName(div.className)[0])
		return;

	var header = document.createElement('p');
	header.className = 'messageHeader';
	header.textContent = this.header;

	var text = document.createElement('p');
	text.className = 'messageText';
	text.textContent = this.text;

	var closeButton = document.createElement('button');
	closeButton.type = 'button';
	closeButton.className = 'button messageClose';
	closeButton.onclick = function(){
		this.closeMessage();
	}.bind(this);

	var img = document.createElement('img');
	img.src = '../../assets/icons/closeBlack.png';
	img.alt = 'Chiudi';
	closeButton.appendChild(img);

	div.appendChild(header);
	div.appendChild(text);
	div.appendChild(closeButton);

	this.target.insertBefore(div, this.target.childNodes[this.position + 1]);
}

/*Chiude il messaggio*/
Message.prototype.closeMessage = function(){
	var classType = this.type == 'error' ? 'messageContainer errorMessageContainer' : 'messageContainer simpleMessageContainer';
	this.target.getElementsByClassName(classType)[0].remove();
}


/*Classe Confirmation*/

/*Costruttore*/
function Confirmation(type, target, position, header, text, acceptText, acceptAction, denyText, denyAction){
	this.type = type;
	this.target = target;
	this.position = position;
	this.header = header;
	this.text = text;
	this.acceptText = acceptText;
	this.acceptAction = acceptAction;
	this.denyText = denyText;
	this.denyAction = denyAction;
}

/*Visualizza la richiesta di conferma secondo i parametri specificati nel costruttore*/
Confirmation.prototype.displayConfirmation = function(){
	var div = document.createElement('div');
	div.className = 'messageContainer confirmMessageContainer';

	var header = document.createElement('p');
	header.className = 'messageHeader';
	header.textContent = this.header;

	var text = document.createElement('p');
	text.className = 'messageText';
	text.textContent = this.text;

	var accept = document.createElement('button');
	accept.type = 'button';
	accept.textContent = 'SI';
	accept.className = 'confirmAccept';
	accept.onclick = function(){
		this.acceptAction();
	}.bind(this);

	var deny = document.createElement('button');
	deny.type = 'button';
	deny.textContent = 'NO';
	deny.className = 'confirmDeny';
	if(this.denyAction){
		deny.onclick = function(){
			this.denyAction();
		}.bind(this);
	}
	else{
		deny.onclick = function(){
			this.closeConfirmation();
		}.bind(this);
	}
	div.appendChild(header);
	div.appendChild(text);
	div.appendChild(accept);
	div.appendChild(deny);

	this.target.insertBefore(div, this.target.childNodes[this.position + 1]);
}

/*Chiude la richiesta di conferma, e non compie alcuna azione*/
Confirmation.prototype.closeConfirmation = function(){
	this.target.getElementsByClassName('messageContainer confirmMessageContainer')[0].remove();
}