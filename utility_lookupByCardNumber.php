<?php

require_once("project.php");

if(!isset($_GET['search'])) {
	$year=date('Y');
	$year++;
	$body ="<form method=\"GET\" action=\"{$_SERVER['PHP_SELF']}\">";
	$body.="<table cellpadding=\"5\" cellspacing=\"0\" border=\"0\">";
	$body.="<tr><td>Membership Year</td><td><input type=\"text\" size=\"5\" name=\"expirationYear\" value=\"{$year}\"></td></tr>";
	$body.="<tr><td>Card Number</td><td><input type=\"text\" size=\"5\" name=\"cardNumber\"></td></tr>";
	$body.="<tr><td colspan=\"2\"><input type=\"submit\" name=\"search\" value=\"Search\"></td></tr>";
	$body.="</table></form>";
	renderPage($body);
} else {
	unset($_GET['search']);
	foreach($_GET as $k=>$v) {
		$w[]=$k.'='.escapeshellarg($v);
	}
	$db=myDB();
	$sql="SELECT memberID FROM membershipcards WHERE ".implode(" AND ",$w);
	$res=simpleQuery($sql,true,$db);
	$row=$res->fetch();
	header("Location: memberRecord.php?memberID={$row[0]}");
}
?>
