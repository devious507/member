<?php

require_once("project.php");

$db=myDB();
$_POST['bankdepositnumber']=getCurrentDepositNumber($db);
if(!isset($_POST['datepaid'])) {
	$_POST['datepaid']=date('m/d/Y');
}
if(!isset($_POST['submit'])) {
	header("Location: index.php");
	exit();
}
if(strtolower($_POST['checknumber']) == 'cc') {
	$_POST['checknumber']=-1;
}
unset($_POST['submit']);
foreach($_POST as $k=>$v) {
	$keys[]=$k;
	$v=preg_replace("/'/","''",$v);
	$vals[]="'{$v}'";
}

$key=implode(",",$keys);
$val=implode(",",$vals);
$sql="INSERT INTO duesPaid ({$key}) VALUES ({$val})";

$memberID = $_POST['memberid'];
simpleQuery($sql,true,$db);
header("Location: memberRecord.php?memberID={$memberID}");
?>
