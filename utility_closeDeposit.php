<?php

require_once("project.php");
$db=myDB();

	$currentDeposit=getCurrentDepositNumber($db);
	$nextDeposit=$currentDeposit+1;
if(isset($_GET['confirm'])) {
	$date=date('m/d/Y');
	$sql="UPDATE bankDeposit SET depositDate='{$date}' WHERE depositNumber={$currentDeposit}";
	simpleQuery($sql,true,$db);
	$sql="INSERT INTO bankDeposit (depositNumber,depositDate) VALUES ({$nextDeposit},'{$date}')";
	simpleQuery($sql,true,$db);
	renderPage("Deposit {$currentDeposit} closed, and {$nextDeposit} opened");
	exit();
} else {
	$href = "<a href=\"utility_closeDeposit.php?confirm=true\">Really Close Deposit {$currentDeposit}</a>";
	renderPage($href);
	exit();
}
?>
