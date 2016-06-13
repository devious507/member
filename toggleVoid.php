<?php

require_once("project.php");
checkIsWriter();

$db=myDB();


if(!isset($_GET['expirationyear']) || !isset($_GET['cardnumber'])) {
	header("Location: index.php");
	exit();
}
$sql="SELECT memberID,void FROM membershipCards WHERE expirationyear={$_GET['expirationyear']} AND cardnumber={$_GET['cardnumber']}";

$res = simpleQuery($sql,true,$db);
$data=$res->fetchAll(PDO::FETCH_ASSOC);
if(count($data) != 1) {
	header("Location: index.php");
} else {
	$row=$data[0];
	$memberID = $row['memberID'];
	$void = $row['void'];
	if($void == 1) {
		$sql="UPDATE membershipCards SET void=0 WHERE expirationyear={$_GET['expirationyear']} AND cardnumber={$_GET['cardnumber']}";
	} else {
		$sql="UPDATE membershipCards SET void=1 WHERE expirationyear={$_GET['expirationyear']} AND cardnumber={$_GET['cardnumber']}";
	}
	$res = simpleQuery($sql,true,$db);
	header("Location: memberRecord.php?memberID={$memberID}");
	exit();
}
?>
