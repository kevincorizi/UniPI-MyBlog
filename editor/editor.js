/*Funzioni di utilità per l'editor di testo*/


/*Per ogni tasto premuto, adegua la visualizzazione dell'iframe*/
function monitorText(event){
	var frame = frames['editorText'];
	var text = document.getElementById('htmlText');

	frame.srcdoc = "<!DOCTYPE html><html><head><link href='editor/embedded.css' rel='stylesheet' type='text/css'></head><body><pre class='postTextPre'>" + text.value + "</pre></body></html>";
}

/*Gestisce l'inserimento di elementi all'interno del testo*/
function buttonHandler(button){
	var text = document.getElementById("htmlText");

	var tags = {
		bold : ["<span class='bold'>", "</span>"],
		italic : ["<span class='italic'>", "</span>"],
		underline : ["<span class='underline'>", "</span>"],
		img : ["<img class='postImg' src='' alt=''>", "</img>"],
		link : ["<a class='postLink' href=''>", "</a>"],
		quote : ["<q class='postQuote'>", "</q>"],
		more : ["<!-- more -->", ""]
	};
	
	var posI = text.selectionStart;
	var posF = text.selectionEnd;
	var before = text.value.substring(0, posI);
	var after = text.value.substring(posF, text.value.length);
	var selected = text.value.substring(posI, posF);
	
	text.value = before + tags[button][0] + selected + tags[button][1] + after;

	text.focus();

	//riposiziono adeguatamente il cursore
	text.selectionStart = posI + tags[button][0].length;
	text.selectionEnd = text.selectionStart;
}