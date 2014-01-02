<?php


require_once("project.php");
if(!isset($_POST['tablename'])) {
	header("Location: index.php");
	exit();
}

$table = $_POST['tablename'];
$memberID = $_POST['h_memberID'];
unset($_POST['tablename']);
unset($_POST['submit']);
foreach($_POST as $k=>$v) {
	if(preg_match("/^h_/",$k)) {
		$kk=preg_replace("/^h_/",'',$k);
		$wheres[]=$kk."='".$v."'";
	} else {
		$sets[]=$k."='".$v."'";
	}
}
$sql=sprintf("UPDATE %s SET %s WHERE %s",$table,implode(',',$sets),implode(' AND ',$wheres));
$db=myDB();
$res=simpleQuery($sql,true,$db);
header("Location: memberRecord.php?memberID={$memberID}");
?>
