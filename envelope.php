<?php

require_once("project.php");
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
