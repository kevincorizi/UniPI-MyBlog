/*Classe Comment*/

/*Costruttore: riceve un oggetto PHP e lo converte in oggetto Comment*/
function Comment(comment){
	this.id = comment.id;
	this.postId = comment.postId;
	this.author = new User(comment.author);
	this.text = comment.text;
	this.date = new JSDate(comment.date);
	this.numberReported = comment.numberReported;
	this.post = new Post(comment.post);

	this.numberLikes = comment.numberLikes;
	this.numberDislikes = comment.numberDislikes;
}

/*Visualizza il commento come riga di tabella nel pannello di controllo.*/
Comment.prototype.appendRowComment = function(){
	var tr = document.createElement('tr');
	tr.className = 'tableRow commentRow';
	tr.id = 'comment_' + this.id;

	var td1 = document.createElement('td');
	var ptd1 = document.createElement('a');
	ptd1.className = 'commentText';
	ptd1.textContent = this.text;
	ptd1.href = 'index.php?post=' + this.post.id + '#comment' + this.id;
	td1.appendChild(ptd1);

	var td2 = document.createElement('td');
	var ptd2 = document.createElement('a');
	ptd2.className = 'commentPost';
	ptd2.textContent = this.post.title;
	ptd2.href = 'index.php?post=' + this.post.id;
	td2.appendChild(ptd2);

	var td3 = document.createElement('td');
	var ptd3 = document.createElement('a');
	ptd3.className = 'commentAuthor';
	ptd3.textContent = this.author.username;
	ptd3.href = '?users=' + this.author.id;
	td3.appendChild(ptd3);


	var td4 = document.createElement('td');
	var ptd4 = document.createElement('p');
	ptd4.className = 'commentDate';
	ptd4.textContent = formatDate(this.date, 'short');
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
	ptd7.className = 'commentReported';
	ptd7.textContent = this.numberReported;
	td7.appendChild(ptd7);

	tr.appendChild(td1);
	tr.appendChild(td2);
	tr.appendChild(td3);
	tr.appendChild(td4);
	tr.appendChild(td5);
	tr.appendChild(td6);
	tr.appendChild(td7);

	document.getElementById('commentTable').getElementsByTagName('tbody')[0].appendChild(tr);
}

/*Visualizza il commento nella pagina dell'articolo*/
Comment.prototype.appendCardComment = function(){
	var container = document.createElement('div');
	container.className = 'comment';

	var anchor = document.createElement('a');
	anchor.id = 'comment_' + this.id;
	container.appendChild(anchor);

	var userPicture = document.createElement('div');
	userPicture.className = 'userPictureContainer';
	var picture = document.createElement('img');
	picture.className = 'userPicture';
	picture.src = this.author.picture;
	picture.alt = this.author.username;
	userPicture.appendChild(picture);
	container.appendChild(userPicture);

	var data = document.createElement('div');
	data.className = 'commentData';

	var p1 = document.createElement('p');
	p1.className = 'commentAuthor';
	p1.textContent = this.author.username;

	var p2 = document.createElement('p');
	p2.className = 'commentDate';
	p2.textContent = formatDate(this.date, 'long');

	var p3 = document.createElement('p');
	p3.className = 'commentText';
	p3.textContent = this.text;

	data.appendChild(p1);
	data.appendChild(p2);
	data.appendChild(p3);
	container.appendChild(data);

	var interaction = document.createElement('div');
	interaction.className = 'commentCommunity';

	var likes = document.createElement('div');
	likes.className = 'commentLikes';

	var likeDiv = document.createElement('div');
	var dislikeDiv = document.createElement('div');
	var reportDiv = document.createElement('div');

	var likeNumber = document.createElement('label');
	likeNumber.className = 'count';
	likeNumber.textContent = this.numberLikes;

	var likeButton = document.createElement('button');
	likeButton.type = 'button';
	likeButton.className = 'button';
	likeButton.disabled = 'disabled';

	var likeImg = document.createElement('img');
	likeImg.src = 'assets/icons/likeBlack.png';
	likeImg.alt = 'Mi piace';
	likeButton.appendChild(likeImg);

	var dislikeNumber = document.createElement('label');
	dislikeNumber.className = 'count';
	dislikeNumber.textContent = this.numberDislikes;

	var dislikeButton = document.createElement('button');
	dislikeButton.type = 'button';
	dislikeButton.className = 'button';
	dislikeButton.disabled = 'disabled';

	var dislikeImg = document.createElement('img');
	dislikeImg.src = 'assets/icons/dislikeBlack.png';
	dislikeImg.alt = 'Non mi piace';
	dislikeButton.appendChild(dislikeImg);

	var reportedNumber = document.createElement('label');
	reportedNumber.className = 'count';
	reportedNumber.textContent = this.numberReported;

	var reportButton = document.createElement('button');
	reportButton.type = 'button';
	reportButton.className = 'button';
	reportButton.disabled = 'disabled';

	var reportImg = document.createElement('img');
	reportImg.src = 'assets/icons/reportBlack.png';
	reportImg.alt = 'Segnalato';
	reportButton.appendChild(reportImg);

	likeDiv.appendChild(likeNumber);
	likeDiv.appendChild(likeButton);

	dislikeDiv.appendChild(dislikeNumber);
	dislikeDiv.appendChild(dislikeButton);

	reportDiv.appendChild(reportedNumber);
	reportDiv.appendChild(reportButton);

	var deleteThisComment = document.createElement('label');
	deleteThisComment.className = 'deleteComment';
	deleteThisComment.onclick = function(){
		deleteComment(this.id);
	}.bind(this);
	deleteThisComment.textContent = 'Elimina';

	interaction.appendChild(likeDiv);
	interaction.appendChild(dislikeDiv);
	interaction.appendChild(reportDiv);
	interaction.appendChild(deleteThisComment);
	container.appendChild(interaction);

	if(document.getElementsByClassName('emptyTarget')[0]){
		document.getElementsByClassName('emptyTarget')[0].style.display = 'none';
	}

	document.getElementsByClassName('postComments')[0].insertBefore(container, document.getElementsByClassName('postComments')[0].firstChild);
}