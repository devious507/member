<?php

require_once("project.php");
if($_SESSION['write'] == 0 ) {
	header("Location: index.php");
	exit();
}

$db = myDB();
if(isset($_GET['memberid'])) {
	$sql="UPDATE members SET printenvelope=1 WHERE memberID={$_GET['memberid']}";
	$res=simpleQuery($sql,true,$db);
	header("Location: memberRecord.php?memberID={$_GET['memberid']}");
	exit();
} else {
	die();
}

?>
