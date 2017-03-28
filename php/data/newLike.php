<?php
	/*Script per l'inserimento di 'non mi piace' ai post o ai commenti*/
	require_once "../../config.php";
	require_once "../sessionManager.php";
	require_once "../lib.php";
	startSession();

	global $conn;

	$authorId = $conn->secure($_GET["filter0"]);
	$destId = $conn->secure($_GET["filter1"]);
	$action = $conn->secure($_GET["filter2"]);
	$destType = $conn->secure($_GET["filter3"]);

	$query = "";
	if ($destType == "post") {
		$presentL = fetchLikes("SELECT * FROM `like` WHERE iduser=".$authorId." AND idpost=".$destId." AND type='L'");
		if ($action == "add"){
			if(count($presentL) == 0)
				$query = "INSERT INTO `like` (iduser, idpost, type) VALUES (".$authorId.", ".$destId.", 'L')";
		}	
		else
			$query = "DELETE FROM `like` WHERE iduser=".$authorId." AND idpost=".$destId." AND type='L'";
	}
	else{
		$presentL = fetchLikes("SELECT * FROM `like` WHERE iduser=".$authorId." AND idcomment=".$destId." AND type='L'");
		if ($action == "add"){
			if(count($presentL) == 0)
				$query = "INSERT INTO `like` (iduser, idcomment, type) VALUES (".$authorId.", ".$destId.", 'L')";
		}
		else
			$query = "DELETE FROM `like` WHERE iduser=".$authorId." AND idcomment=".$destId." AND type='L'";
	}
	if($query != "")
		$result = $conn->query($query);
	else
		$result = false;

	header("Content-Type: application/json");
	echo json_encode($result);
?>