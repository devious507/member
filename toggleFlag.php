<?php

require_once("project.php");
$db=myDB();

if(!isset($_GET['flag']) || !isset($_GET['memberID'])) {
	header("Location: index.php");
	exit();
}
checkIsWriter();

$flag = $_GET['flag'];
require_once("project.php");

$sql="SELECT {$flag} FROM members WHERE memberID={$_GET['memberID']}";
$res = simpleQuery($sql,true,$db);
$data=$res->fetchAll(PDO::FETCH_ASSOC);
if(count($data) == 1) {
	$row=$data[0];
	if($row[$flag] == 1) {
		$sql="UPDATE members SET {$flag}=0 WHERE memberID={$_GET['memberID']}";
	} else {
		$sql="UPDATE members SET {$flag}=1 WHERE memberID={$_GET['memberID']}";
	}
	simpleQuery($sql,true,$db);
	header("Location: memberRecord.php?memberID={$_GET['memberID']}");
	exit();
} else {
	header("Location: index.php");
}
?>
