<?php

require_once("project.php");
//var_dump($_SESSION); exit();

if($_SESSION['write'] == 0 ) {
	header("Location: index.php");
	exit();
}


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
$phone_temp = preg_replace("/[^0-9]/", '', $_POST['spouse_phone']);
if(strlen($phone_temp) == 10) {
	$acode = substr($phone_temp,0,3);
	$prefix= substr($phone_temp,3,3);
	$nxx   = substr($phone_temp,6,4);
	$_POST['spouse_phone'] = $acode."-".$prefix."-".$nxx;
}
if(($_POST['spouse_first'] != '') && ($_POST['spouse_last'] == '')) {
	$_POST['spouse_last']=$_POST['NameLast'];
}

if(preg_match("/ \& $/",$_POST['NameFirst'])) {
	$tmp=preg_replace("/ \& $/","",$_POST['NameFirst']);
	$_POST['NameFirst']=$tmp;
}
foreach($_POST as $k=>$v) {
	$v=trim($v);
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
