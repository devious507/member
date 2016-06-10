<?php

require_once("project.php");
if($_SESSION['write'] == 0 ) {
	header("Location: index.php");
	exit();
}


define("ANNUAL_MEMBERSHIP",100);
define("GUEST_PASS",25);
$db=myDB();
if(!isset($_GET['type']) || !isset($_GET['memberID'])) {
	header("Location: index.php");
	exit();
}

$types = myPaymentTypes();
switch($_GET['type']) {
case "annual":
	renderPage(annualMembership($_GET['memberID']));
	break;
case "guest":
	renderPage(guestPass($_GET['memberID']));
	break;
default:
	renderPage(miscSale($_GET['memberID']));
	break;
}

function guestPass($memberid) {
	global $db;
	$types = myPaymentTypes();
	$clubMemberName="<a class=\"whitePlain\" href=\"memberRecord.php?memberID={$memberid}\">".getClubMemberNameByMemberID($memberid,$db)."</a>";
	$body ="<form method=\"post\" action=\"receivePayment.php\">";
	$body.="<table cellpadding=\"5\" cellspacing=\"0\" border=\"1\">";
	$body.="<tr><td colspan=\"8\" class=\"invertBold\">{$clubMemberName} / Guest Membership Sale</td></tr>";
	$body.="<tr><td>Record #</td><td>Check # or CC</td><td>Amount</td><td>Sale Type</td><td>Guest Pass #</td><td>&nbsp;</td></tr>";
	$body.="<tr>";
		$body.="<td><input type=\"hidden\" name=\"memberid\" value=\"{$memberid}\">{$memberid}</td>";
		$body.="<td><input id=\"focusBox\" type=\"text\" size=\"6\" name=\"checknumber\" id=\"checknumber\" value=\"0\"></td>";
		$body.="<td><input type=\"text\" size=\"6\" name=\"amount\" value=\"".GUEST_PASS."\"></td>";
		$body.="<td><input type=\"hidden\" name=\"paymenttype\" value=\"{$types['guest']}\">{$types['guest']}</td>";
		$body.="<td><input type=\"text\" name=\"comment\"></td>";
		$body.="<td><input type=\"submit\" name=\"submit\" value=\"Post Payment\"></td>";
	$body.="</tr>";
	$body.="</table></form>";
	return $body;
}
function miscSale($memberid) {
	global $db;
	$types = myPaymentTypes();
	$clubMemberName="<a class=\"whitePlain\" href=\"memberRecord.php?memberID={$memberid}\">".getClubMemberNameByMemberID($memberid,$db)."</a>";
	$body ="<form method=\"post\" action=\"receivePayment.php\">";
	$body.="<table cellpadding=\"5\" cellspacing=\"0\" border=\"1\">";
	$body.="<tr><td colspan=\"8\" class=\"invertBold\">{$clubMemberName} / Misc. Sales</td></tr>";
	$body.="<tr><td>Record #</td><td>Check #</td><td>Amount</td><td>Sale Type</td><td>Comment</td><td>&nbsp;</td></tr>";
	$body.="<tr>";
		$body.="<td><input type=\"hidden\" name=\"memberid\" value=\"{$memberid}\">{$memberid}</td>";
		$body.="<td><input id=\"focusBox\" type=\"text\" size=\"6\" name=\"checknumber\" value=\"0\"></td>";
		$body.="<td><input type=\"text\" size=\"6\" name=\"amount\" value=\"\"></td>";
		$select="<select name=\"paymenttype\">";
		foreach($types as $k=>$v) {
			$select.="<option value=\"{$v}\">{$v}</option>";
		}
		$select.="</select>";
		$body.="<td>{$select}</td>";
		$body.="<td><input type=\"text\" name=\"comment\"></td>";
		$body.="<td><input type=\"submit\" name=\"submit\" value=\"Post Payment\"></td>";
	$body.="</tr>";
	$body.="</table></form>";
	return $body;
}
function annualMembership($memberid) {
	global $db;
	$nextYear=date('Y')+2;
	$lastYear=date('Y');
	$thisYear=date('Y')+1;
	$nextCard = nextCardNumber($db,$thisYear);
	$thisYearCard=$nextCard;
	$nextYearCard=nextCardNumber($db,$nextYear);
	$lastYearCard=nextCardNumber($db,$lastYear);
	$types = myPaymentTypes();
	$clubMemberName="<a class=\"whitePlain\" href=\"memberRecord.php?memberID={$memberid}\">".getClubMemberNameByMemberID($memberid,$db)."</a>";
	$expYear=date('Y'); $expYear++;
	$body ="<form method=\"post\" action=\"mixedPaymentCardIssue.php\">";
	$body.="<table cellpadding=\"5\" cellspacing=\"0\" border=\"1\">";
	$body.="<tr><td colspan=\"8\" class=\"invertBold\">{$clubMemberName}</td></tr>";
	$body.="<tr><td>Record #</td><td>Check #</td><td>Amount</td><td>Sale Type</td><td>Comment</td><td>Card #</td><td>Card Exp. Year</td><td>&nbsp;</td></tr>";
	$body.="<tr>";
		$body.="<td><input type=\"hidden\" name=\"memberid\" value=\"{$memberid}\">{$memberid}</td>";
		$body.="<td><input id=\"focusBox\" type=\"text\" size=\"6\" name=\"checknumber\" value=\"0\"></td>";
		$body.="<td><input type=\"text\" size=\"6\" name=\"amount\" value=\"".ANNUAL_MEMBERSHIP."\"></td>";
		$body.="<td><input type=\"hidden\" name=\"paymenttype\" value=\"{$types['annual']}\">{$types['annual']}</td>";
		$body.="<td><input type=\"text\" name=\"comment\"></td>";
		$body.="<td><input type=\"text\" name=\"card_number\" size=\"5\" value=\"{$nextCard}\"></td>";
		$body.="<td><input type=\"text\" name=\"card_expire\" size=\"5\" value=\"{$expYear}\"></td>";
		$body.="<td><input type=\"submit\" name=\"submit\" value=\"Post Payment\"></td>";
	$body.="</tr>";
	$body.="</table></form>\n";
	$body.="<p>Quick Payment Type Selector: ";
	$body.="<a href=\"#\" onclick=\"document.forms[0].checknumber.value='-1';document.forms[0].amount.value='102.85';\">Credit Card</a> | ";
	$body.="<a href=\"#\" onclick=\"document.forms[0].checknumber.value='-2';document.forms[0].amount.value='100';\">Donated Gift Cert</a> | ";
	$body.="<a href=\"#\" onclick=\"document.forms[0].checknumber.value='-3';document.forms[0].amount.value='100'\">Gift Cert</a> | ";
	$body.="<a href=\"#\" onclick=\"document.forms[0].card_number.value='$lastYearCard';document.forms[0].card_expire.value='{$lastYear}';\">{$lastYear}</a> | ";
	$body.="<a href=\"#\" onclick=\"document.forms[0].card_number.value='$thisYearCard';document.forms[0].card_expire.value='{$thisYear}';\">{$thisYear}</a> | ";
	$body.="<a href=\"#\" onclick=\"document.forms[0].card_number.value='$nextYearCard';document.forms[0].card_expire.value='{$nextYear}';\">{$nextYear}</a> | ";
	$body.="<a href=\"#\" onclick=\"document.forms[0].amount.value='0';\">\$0.00</a>";
	$body.="</p>\n";
	return $body;
}
?>
