<?php
	/*Script per l'aggiunta dei commenti*/
	require_once "../../config.php";
	require_once "../sessionManager.php";
	require_once "../lib.php";
	startSession();

	global $conn;

	$postId = $conn->secure($_GET["filter0"]);
	$authorId = $conn->secure($_GET["filter1"]);
	$text = $conn->secure($_GET["filter2"]);

	$query = "INSERT INTO comment (author, `text`, post)
				VALUES ('".$authorId."', '".$text."', '".$postId."')";

	$result = $conn->query($query);

	if($result == true){
		$findComment = "SELECT * 
				FROM comment 
				WHERE author=".$authorId." AND post=".$postId." AND text='".$text."' ORDER BY dateComment DESC LIMIT 1";
		$result = fetchComments($findComment)[0];
		$result->author = fetchUsers("SELECT * FROM user WHERE id=".$result->author)[0];
		$result->post = fetchPosts("SELECT * FROM post WHERE id=".$result->postId)[0];
	}

	header("Content-Type: application/json");
	echo json_encode($result);
?>