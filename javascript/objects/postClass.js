/*Classe Post*/

/*Costruttore: riceve un oggetto PHP e lo converte in oggetto Post*/
function Post(post){
	this.id = post.id;
	this.title = post.title;
	this.text = post.text;
	this.author = post.author;
	this.dateCreated = new JSDate(post.dateCreated);
	this.dateLastModified = new JSDate(post.dateLastModified);
	this.category = post.category;
	this.status = post.status;

	if(post.tags.length == 0)
		this.tags = 'Nessun tag';
	else
		this.tags = post.tags.join(', ');
	this.numberLikes = post.numberLikes;
	this.numberDislikes = post.numberDislikes;
	this.comments = post.comments;
	this.visited = post.visited;
}

/*Visualizza l'articolo come riga di tabella nel pannello di controllo.*/
Post.prototype.appendRowPost = function(){
	var tr = document.createElement('tr');
	tr.id = 'post_' + this.id;
	tr.className = (location.search == '?posts=all') ? 'table postRow allPostRow' : 'tableRow postRow yourPostRow'; 

	var td1 = document.createElement('td');
	var ptd1 = document.createElement('a');
	ptd1.className = 'postTitle';
	ptd1.textContent = this.title;
	ptd1.href = 'index.php?post=' + this.id;
	td1.appendChild(ptd1);

	var td3 = document.createElement('td');
	var ptd3 = document.createElement('p');
	ptd3.className = 'postDate';
	ptd3.textContent = formatDate(this.dateLastModified, 'short');
	td3.appendChild(ptd3);

	var td4 = document.createElement('td');
	var ptd4 = document.createElement('p');
	ptd4.className = 'postComments';
	ptd4.textContent = this.comments;
	td4.appendChild(ptd4);

	var td5 = document.createElement('td');
	var ptd5 = document.createElement('p');
	ptd5.className = 'postLikes';
	ptd5.textContent = this.numberLikes;
	td5.appendChild(ptd5);

	var td6 = document.createElement('td');
	var ptd6 = document.createElement('p');
	ptd6.className = 'postDislikes';
	ptd6.textContent = this.numberDislikes;
	td6.appendChild(ptd6);

	var td7 = document.createElement('td');
	var ptd7 = document.createElement('p');
	ptd7.className = 'postTags';
	ptd7.textContent = this.tags;
	td7.appendChild(ptd7);

	tr.appendChild(td1);
	tr.appendChild(td3);

	if(location.search == '?posts=all'){
		var td2 = document.createElement('td');
		var ptd2 = document.createElement('a');
		ptd2.className = 'postAuthor';
		ptd2.textContent = this.author;
		ptd2.href = '?users=' + this.author;
		td2.appendChild(ptd2);
		tr.appendChild(td2);
	}
	tr.appendChild(td4);
	tr.appendChild(td5);
	tr.appendChild(td6);
	tr.appendChild(td7);
	if(location.search != '?posts=all'){
		var td8 = document.createElement('td');
		var img = document.createElement('img');
		img.src = 'assets/icons/editBlack.png';
		img.alt = 'Modifica';
		img.onclick = function(){
			location.href='?editor=' + this.id;
		}
		td8.appendChild(img);
		tr.appendChild(td8);
	}

	var present = document.getElementById('postTable').getElementsByTagName('tbody')[0].appendChild(tr);
}

/*Visualizza l'articolo nel formato ridotto nella homepage*/
Post.prototype.appendCardPost = function(){
	var dom = new DOMParser();

	var article = document.createElement('article');
	article.className = 'post';
	article.id = 'post_' + this.id;

	var content = document.createElement('div');
	content.className = 'postContent';

	var h2 = document.createElement('h2');
	h2.className = 'postTitle';

	var a = document.createElement('a');
	a.textContent = this.title;
	a.href = 'index.php?post=' + this.id;
	h2.appendChild(a);

	var p1 = document.createElement('p');
	p1.className = 'postAuthor';
	p1.textContent = this.author;

	var p2 = document.createElement('p');
	p2.className = 'postDate';
	p2.textContent = formatDate(this.dateLastModified, 'long');

	var text = this.text.split('<!-- more -->')[0];
	var p4 = null;
	if(this.text.split('<!-- more -->')[1] != null){
		text += "</pre>";
		text += "<a href='index.php?post=" + this.id + "'>Continua a leggere...</a>";
		var p3 = dom.parseFromString(text, 'text/html').firstChild.childNodes[1].firstChild;
		var p4 = dom.parseFromString(text, 'text/html').firstChild.childNodes[1].childNodes[1];
	}
	else{
		var p3 = dom.parseFromString(text, 'text/html').firstChild.childNodes[1].firstChild;
	}

	article.appendChild(content);
	content.appendChild(h2);
	content.appendChild(p1);
	content.appendChild(p2);
	content.appendChild(p3);
	if(p4)
		content.appendChild(p4);

	var info = document.createElement('div');
	info.className = 'postInfo';

	var p4 = document.createElement('p');
	p4.className = 'postTags';
	p4.textContent = this.tags == '' ? 'Non ci sono tag!' : 'Tag: ' + this.tags;
	info.appendChild(p4);

	var interaction = document.createElement('div');
	interaction.className = 'postCounts';
	info.appendChild(interaction);

	var count1 = document.createElement('div');
	var count2 = document.createElement('div');
	var count3 = document.createElement('div');
	count1.className = 'counts';
	count2.className = 'counts';
	count3.className = 'counts';

	var icon1 = document.createElement('img');
	var icon2 = document.createElement('img');
	var icon3 = document.createElement('img');
	icon1.className = 'countIcon';
	icon1.src = '../../assets/icons/commentWhite.png';
	icon1.alt = 'Commenti';
	icon2.className = 'countIcon';
	icon2.src = '../../assets/icons/likeWhite.png';
	icon2.alt = 'Mi piace';
	icon3.className = 'countIcon';
	icon3.src = '../../assets/icons/dislikeWhite.png';
	icon3.alt = 'Non mi piace';

	var comments = document.createElement('div');
	comments.className = 'commentCount';
	comments.textContent = this.comments;

	var likes = document.createElement('div');
	likes.className = 'likeCount';
	likes.textContent = this.numberLikes;

	var dislikes = document.createElement('div');
	dislikes.className = 'dislikecount';
	dislikes.textContent = this.numberDislikes;

	count1.appendChild(icon1);
	count1.appendChild(comments);

	count2.appendChild(icon2);
	count2.appendChild(likes);

	count3.appendChild(icon3);
	count3.appendChild(dislikes);

	article.appendChild(info);
	interaction.appendChild(count1);
	interaction.appendChild(count2);
	interaction.appendChild(count3);

	document.getElementById('postflow').appendChild(article);
}