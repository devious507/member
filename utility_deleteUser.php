<?php

require_once("project.php");

if($_SESSION['write'] == 0 ) {
	header("Location: index.php");
	exit();
}

if(!isset($_GET['username'])) {
	header("Location: utility_userManagement.php");
	exit();
}
if($_GET['username'] == $_SESSION['username']) {
	header("Location: utility_userManagement.php");
	exit();
}

$sql="DELETE FROM users WHERE username='{$_GET['username']}'";
$db->exec($sql);
header("Location: utility_userManagement.php");
exit();
?>
