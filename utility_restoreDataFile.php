<?php

require_once("project.php");
checkIsWriter();

$db = myDB();

if(!isset($_FILES['file'])) {
	$body= "<p>Restore Database File from Local Backup <b>WARNING</b> this will erase the database on the server, proceed with caution!</p>";
	$body.='<form action="utility_restoreDataFile.php" method="post" enctype="multipart/form-data">';
	$body.='<label for="file">Filename:</label>';
	$body.='<input type="file" name="file" id="file"><br>';
	$body.='<input type="submit" name="submit" value="Submit">';
	$body.="</form>";
	renderPage($body);
	exit();
} else {
	$input = $_FILES['file']['tmp_name'];
	unlink(DATAFILE);
	copy($input,DATAFILE);
	chmod(DATAFILE,0777);
	renderPage("<p>File Uploaded and restored</p>");
	exit();
}

?>
