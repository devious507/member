<?php

require_once("project.php");
checkIsWriter();

if(!isset($_GET['username'])) {
	header("Location: utility_userManagement.php");
	exit();
}
if($_GET['username'] == $_SESSION['username']) {
	header("Location: utility_userManagement.php");
	exit();
}

$sql="SELECT write FROM users WHERE username='{$_GET['username']}'";
$res = simpleQuery($sql,true,$db);
$data = $res->fetchAll(PDO::FETCH_ASSOC);
if(count($data) != 1) {
	header("Location: utility_userManagement.php");
}
$ro=$data[0]['write'];
if($ro == 1) {
	$ro=0;
} else {
	$ro=1;
}
$sql="UPDATE users SET write={$ro} WHERE username='{$_GET['username']}'";
$db->exec($sql);
header("Location: utility_userManagement.php");

?>
