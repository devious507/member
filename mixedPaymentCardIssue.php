<?php

require_once("project.php");
$db = myDB();
if(!isset($_POST['memberid'])) {
	header("Location: index.php");
	exit();
}
if(!isset($_POST['checknumber'])) {
	$_POST['checknumber']=0;
}
if(isset($_POST['amount']) && $_POST['amount'] != '') {
	//  memberID integer, year integer, datepaid varchar, checknumber integer, amount numeric, bankDepositNumber integer
	//  , comment varchar, paymenttype varchar
	$depositNumber=getCurrentDepositNumber($db);
	$membershipYear=date('Y'); $membershipYear++;
	$datePaid=date('m/d/Y');
	if(isset($_POST['card_number']) && $_POST['card_number'] != '') {
		$duesComment= $_POST['comment']." Card # {$_POST['card_number']}";
	} else {
		$duesComment=$_POST['comment'];
	}
	if($_POST['amount'] == '' || $_POST['amount'] == 0 || !isset($_POST['amount'])) {
		$sql="INSERT INTO duesPaid VALUES ({$_POST['memberid']},{$membershipYear},'{$datePaid}',{$_POST['checknumber']},{$_POST['amount']},{$depositNumber},'{$duesComment}','{$_POST['paymenttype']}')";
	} else {
		$sql="INSERT INTO duesPaid VALUES ({$_POST['memberid']},{$membershipYear},'{$datePaid}',{$_POST['checknumber']},{$_POST['amount']},{$depositNumber},'{$duesComment}','{$_POST['paymenttype']}')";
		simpleQuery($sql,true,$db);
	}
}

if(isset($_POST['card_number']) && $_POST['card_number'] != '') {
	$sql="SELECT memberid FROM membershipcards WHERE expirationyear={$_POST['card_expire']} AND cardnumber={$_POST['card_number']}";
	$res=simpleQuery($sql,true,$db);
	$data=$res->fetchAll(PDO::FETCH_ASSOC);
	if(count($data) > 0) {
		$row=$res->fetch();
		$idNum = $row[0];
		$userURL="<a href=\"memberRecord.php?memberID={$idNum}\">{$idNum}</a>";
		$body="Unable to insert card number {$_POST['card_number']} for year {$_POST['card_expire']} already exists for memberID {$userURL}";
		renderPage($body);
		exit();
	}
	$sql="INSERT INTO membershipcards VALUES ({$_POST['memberid']},{$_POST['card_expire']},{$_POST['card_number']},'{$_POST['comment']}',0)";
	simpleQuery($sql,true,$db);
	$sql="UPDATE members SET expiresyear={$_POST['card_expire']} WHERE memberID={$_POST['memberid']}";
	simpleQuery($sql,true,$db);
}
header("Location: memberRecord.php?memberID={$_POST['memberid']}");
?>
