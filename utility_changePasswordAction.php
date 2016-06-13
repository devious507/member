<?php

require_once("project.php");

if(!isset($_POST['username']) || !isset($_POST['newpass1']) || !isset($_POST['newpass2'])) {
	renderPage("<p>Password Error 0x001</p>");
	exit();
}

if( ($_SESSION['write'] == 0 ) && ($_POST['username'] != $_SESSION['username']) ) {
	renderPage("<p>Password Error 0x002</p>");
	header("Location: index.php");
	exit();
}

if($_POST['newpass1'] != $_POST['newpass2']) {
	renderPage("<p>Password Error 0x003</p>");
	exit();
}

if(strlen($_POST['newpass1']) < 6) {
	renderPage("<p>Password Error 0x004</p>");
	exit();
}

$pass=crypt($_POST['newpass1']);
$sql=sprintf("UPDATE users SET password='%s' WHERE username='%s'",$pass,$_POST['username']);
$db=myDB();
$db->exec($sql);
header("Location: utility_userManagement.php");


?>
