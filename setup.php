<?php

require_once("project.php");

// Create the Membership Table

$sql_test = "SELECT name FROM sqlite_master WHERE type='table' AND name='members'";
$sql_create ="CREATE TABLE members ( memberID int, NameLast varchar, NameFirst varchar, address varchar, City varchar, State varchar, Zip varchar, phone varchar, email varchar, comment varchar, membersince integer, expiresyear integer, wantsemail boolean, deceased boolean, lifemember boolean, pendingifemember boolean, remaininglifeamount numeric)";
testCreateTable($sql_test,$sql_create);

$sql_test = "SELECT name FROM sqlite_master WHERE type='table' AND name='duesPaid'";
$sql_create = "CREATE TABLE duesPaid ( memberID integer, year integer, datepaid varchar, checknumber integer, amount numeric, bankDepositNumber integer, comment varchar, paymenttype varchar)";
testCreateTable($sql_test,$sql_create);

$sql_test = "SELECT name FROM sqlite_master WHERE type='table' AND name='membershipCards'";
$sql_create = "CREATE TABLE membershipCards ( memberID integer, expirationYear integer, cardNumber integer, note varchar, void boolean)";
testCreateTable($sql_test,$sql_create);

$sql_test = "SELECT name FROM sqlite_master WHERE type='table' AND name='bankDeposit'";
$sql_create ="CREATE TABLE bankDeposit (depositNumber interger, depositDate varchar)";
testCreateTable($sql_test,$sql_create);

header("Location: index.php");

function testCreateTable($sql_test,$sql_create) {
	$db = myDB();

	$res = $db->query($sql_test);
	if($res->numRows() == 0) {
		$res=$db->query($sql_create);
		if(PEAR::isError($res)) {
			print $res->getMessage();
		}
}
}
?>
