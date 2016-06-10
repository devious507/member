<?php

require_once("project.php");

if($_SESSION['write'] == 0 ) {
	header("Location: index.php");
	exit();
}

if(!isset($_POST['username']) || !isset($_POST['newpass1']) || !isset($_POST['newpass2'])) {
	header("Location: utility_userManagment.php");
	exit();
}
if($_POST['newpass1'] != $_POST['newpass2']) {
	header("Location: utility_userManagement.php");
	exit();
}
if(strlen($_POST['newpass1']) < 6) {
	header("Location: utility_userManagement.php");
	exit();
}

$pass=crypt($_POST['newpass1']);
$sql=sprintf("UPDATE users SET password='%s' WHERE username='%s'",$pass,$_POST['username']);
$db=myDB();
$db->exec($sql);
header("Location: utility_userManagement.php");


?>
