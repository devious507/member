<?php

require_once("project.php");

if($_SESSION['write'] == 0 ) {
	header("Location: index.php");
	exit();
}
if(!isset($_POST['username'])) {
	header("Location: utility_userManagement.php");
	exit();
}

$db=myDB();
$sql="SELECT username FROM users WHERE username='{$_POST['username']}'";
$res = simpleQuery($sql,true,$db);
$data = $res->fetchAll(PDO::FETCH_ASSOC);
if(count($data) == 0) {
	$pass=crypt(md5(rand(100000,1000000)));
	$sql=sprintf("INSERT INTO users VALUES ('%s','%s',1)",
		$_POST['username'],
		$pass);
	$db->exec($sql);
	header("Location: utility_userManagement.php");
	exit();
} else {
	header("Location: utility_userManagement.php");
	exit();
}




?>
