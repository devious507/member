<?php

require_once("project.php");

$db=myDB();
if(!isset($_POST['update'])) {
	header("Location: index.php");
	exit();
}
$memberID = $_POST['memberID'];
unset($_POST['memberID']);
unset($_POST['update']);
$phone_temp = preg_replace("/[^0-9]/", '', $_POST['phone']);
if(strlen($phone_temp) == 10) {
	$acode = substr($phone_temp,0,3);
	$prefix= substr($phone_temp,3,3);
	$nxx   = substr($phone_temp,6,4);
	$_POST['phone'] = $acode."-".$prefix."-".$nxx;
}

foreach($_POST as $k=>$v) {
	$sets[]=$k."='".preg_replace("/'/","''",$v)."'";
}

$sql="SELECT min(expirationyear) AS minexp, max(expirationyear) as maxexp FROM membershipcards WHERE memberid={$memberID}";
$res = simpleQuery($sql,true,$db);
$row = $res->fetch(PDO::FETCH_ASSOC);
$first_year=$row['minexp'];
if($first_year != '') {
	$sets[]='membersince='.$first_year;
} else { 
	$sets[]='membersince=NULL';
}
if($row['maxexp'] != '') {
	$sets[]='expiresyear='.$row['maxexp'];
} else {
	$sets[]='expiresyear=NULL';
}
$sets[]='lat=NULL';
$sets[]='lon=NULL';

$sql="UPDATE members SET ".implode(", ",$sets)." WHERE memberid={$memberID}";
$res = simpleQuery($sql,true,$db);
header("Location: memberRecord.php?memberID={$memberID}");
exit();
?>
