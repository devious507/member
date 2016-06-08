<?php

require_once("project.php");

$db = myDB();
if(!isset($_GET['memberID'])) {
	header("Location: index.php");
	exit();
}
$tm[] = "<a href=\"enterSale.php?type=annual&memberID={$_GET['memberID']}\">Annual Membership</a>";
$tm[] = "<a href=\"enterSale.php?type=guest&memberID={$_GET['memberID']}\">Guest Pass</a>";
$tm[] = "<a href=\"enterSale.php?type=select&memberID={$_GET['memberID']}\">Other Sales</a>";
if(isset($_GET['printable'])) {
	$tm[]="<a href=\"memberRecord.php?memberID={$_GET['memberID']}\">Normal View</a>";
} else {
	$tm[]="<a href=\"memberRecord.php?memberID={$_GET['memberID']}&printable=true\">Printable View</a>";
	$tm[]="<a href=\"map/index.php?mode=singlemember&value={$_GET['memberID']}\">Map</a>";
}
$tmp=mkEnvelopeButton($_GET['memberID'],$db);
if(isset($tmp)) {
	$tm[]=$tmp;
}
$topMenu="[ ".implode(" | ",$tm)." ]";


$sql_member = "SELECT * FROM members WHERE memberID={$_GET['memberID']}";
$sql_cards  = "SELECT expirationyear,cardnumber,note,void FROM membershipCards WHERE memberID={$_GET['memberID']} ORDER BY expirationYear DESC, cardNumber DESC LIMIT 8";
$sql_pymnts = "SELECT bankDepositNumber,datePaid,checknumber,amount,paymenttype,comment FROM duesPaid WHERE memberID={$_GET['memberID']} ORDER BY bankDepositNumber DESC, datePaid DESC, checknumber, amount";


$res=simpleQuery($sql_pymnts,true,$db);
$data=$res->fetchAll(PDO::FETCH_ASSOC);
if(count($data) > 0) {
	$payments ="<table cellpadding=\"3\" cellspacing=\"0\" border=\"0\" width=\"800\">";
	$payments.="<tr><th class=\"left\">Batch #</th><th class=\"left\">Pmt. Date</th><th class=\"left\">Check #</th><th class=\"right\">Amount</th><th class=\"left\">Pmt. Type</th><th class=\"left\">Notes</th></tr>";
	foreach($data as $row) {
		$payments.='<tr>';
		foreach($row as $k=>$v) {
			$this_deposit=$row['bankDepositNumber'];
			$current_deposit = getCurrentDepositNumber($db);
			switch($k) {
			case "bankdepositnumber":
				$url="<a href=\"report_depositReport.php?depositnum={$v}\">{$v}</a>";
				$payments.="<td class=\"left\">{$url}</td>";
				break;
			case "amount":
				$v=sprintf("%.2f",$v);
				$payments.="<td class=\"right\">{$v}</td>";
				break;
			case "datepaid":
				$tmp=preg_split("/ /",$v);
				$v=$tmp[0];
				$payments.="<td class=\"left\">{$v}</td>";
				break;
			case "comment":
				if($this_deposit == $current_deposit) {
					$delete=mkDeleteQuery($row,'duesPaid');
					$payments.="<td class=\"left\">{$v}</td><td>{$delete}</td>";
				} else {
					$payments.="<td class=\"left\">{$v}</td><td>&nbsp;</td>";
				}
				break;
			case "checknumber":
				if($v == '-1') {
					$payments.="<td class=\"left\">CC</td>";
				} elseif($v == 0) {
					$payments.="<td class=\"left\">CASH</td>";
				} else {
					$payments.="<td class=\"left\">{$v}</td>";
				}
				break;
			default:
				$payments.="<td class=\"left\">{$v}</td>";
				break;
			}
		}
		$payments.="</tr>";
	}
}

$res=simpleQuery($sql_cards,true,$db);
$cards="<table cellpadding=\"5\" cellspacing=\"0\" border=\"0\">\n";
$cards.="<tr><td>Year</td><td>Card #</td><td>Note</td></tr>\n";
$cards.="<tr><td colspan=\"4\"><hr></td></tr>\n";

while(($row=$res->fetch(PDO::FETCH_ASSOC))==true) {
	$cards.="<tr>\n";
	$void=$row['void'];
	unset($row['void']);
	foreach($row as $k=>$v) {
		if($void == 0) {
			switch($k) {
			case "cardNumber":
				$v="<a href=\"toggleVoid.php?expirationyear={$row['expirationYear']}&cardnumber={$v}\">{$v}</a>";
				$cards.="\t<td>{$v}</td>";
				break;
			case "note":
				$deleteLine=mkDeleteQuery($row,'membershipcards');
				$editLine=mkEditQuery($row,'membershipcards');
				$cards.="\t<td>{$v}</td><td>{$deleteLine} {$editLine}</td>";
				break;
			default:
				$cards.="\t<td>{$v}</td>\n";
				break;
			}
		} else {
			switch($k) {
			case "cardNumber":
				$v="<a href=\"toggleVoid.php?expirationyear={$row['expirationYear']}&cardnumber={$v}\">{$v}</a>";
				$cards.="\t<td><del>{$v}</del></td>\n";
				break;
			case "note":
				$deleteLine=mkDeleteQuery($row,'membershipcards');
				$cards.="\t<td>{$v}</td><td>{$deleteLine}</td>";
				break;
			default:
				$cards.="\t<td><del>{$v}</del></td>\n";
				break;
			}
		}
	}
	$cards.="</tr>\n";
}
$cards.="</table>\n";
$cards=preg_replace("/\t/","",$cards);
$cards=preg_replace("/\n/","",$cards);

$res=simpleQuery($sql_member,true,$db);
$row = $res->fetch(PDO::FETCH_ASSOC);
$infoCol="<table>";
$infoCol.="<tr><td>Member Since:</td><td>{$row['membersince']}</td></tr>\n";
$infoCol.="<tr><td>Expires:</td><td>{$row['expiresyear']}</td></tr>\n";
$infoCol.="<tr><td colspan=\"2\"><hr></td></tr>\n";
$flags=getMemberFlags();
foreach($flags as $flag=>$v) {
	$row[$flag] = flagLinkBuilder($flag,$row[$flag],$_GET['memberID']);
	$infoCol.= "<tr><td>{$v}</td><td>{$row[$flag]}</td></tr>";
	if($v == 'Board Member') {
		$infoCol.="<tr><td colspan=\"2\"><hr></td></tr>\n";
	}
}
$infoCol.="</table>\n";

//myDumper($row);
$member = "<form method=\"post\" action=\"updateMember.php\">";
$member.= "<input type=\"hidden\" name=\"memberID\" value=\"{$row['memberID']}\">";
$member.= "<table cellspacing=\"0\" cellpadding=\"5\" border=\"1\">";
$member.= "<tr><td class=\"invertBold\">Record # {$row['memberID']}</td><td colspan=\"5\">{$topMenu}</td></tr>";
$member.= "<tr><td>First Name</td><td><input id=\"focusBox\" type=\"text\" size=\"15\" name=\"NameFirst\" value=\"{$row['NameFirst']}\"></td><td>Last Name</td><td><input type=\"text\" size=\"15\" name=\"NameLast\" value=\"{$row['NameLast']}\"></td><td class=\"topLeft\" rowspan=\"11\">{$infoCol}</td><td rowspan=\"11\" class=\"topLeft\">{$cards}</td></tr>";
$member.= "<tr><td>Address</td><td colspan=\"3\"><input type=\"text\" size=\"30\" name=\"address\" value=\"{$row['address']}\"></td></tr>";
$member.= "<tr><td>City, State Zip</td><td><input type=\"text\" size=\"10\" name=\"City\" value=\"{$row['City']}\"></td><td>, <input type=\"text\" size=\"4\" name=\"State\" value=\"{$row['State']}\"></td><td><input type=\"text\" name=\"Zip\" size=\"6\" value=\"{$row['Zip']}\"></td></tr>";
$member.= "<tr><td>Phone</td><td colspan=\"3\"><input type=\"text\" name=\"phone\" value=\"{$row['phone']}\" size=\"25\"></td></tr>";
$member.= "<tr><td>Email</td><td colspan=\"3\"><input type=\"text\" name=\"email\" value=\"{$row['email']}\" size=\"25\"></td></tr>";
// Spouse INfo
$member.="<tr><td>Spouse First</td><td><input type=\"text\" size=\"15\" name=\"spouse_first\" value=\"{$row['spouse_first']}\"></td><td>Spouse Last</td><td><input type=\"text\" size=\"15\" name=\"spouse_last\" value=\"{$row['spouse_last']}\"></td></tr>";
$member.= "<tr><td>Spouse Phone</td><td colspan=\"3\"><input type=\"text\" name=\"spouse_phone\" value=\"{$row['spouse_phone']}\" size=\"25\"></td></tr>";
$member.= "<tr><td>Spouse Email</td><td colspan=\"3\"><input type=\"text\" name=\"spouse_email\" value=\"{$row['spouse_email']}\" size=\"25\"></td></tr>";
$member.= "<tr><td colspan=\"4\">Comments</td></tr>";
$member.= "<tr><td colspan=\"4\"><textarea rows=\"6\" cols=\"50\" name=\"comment\">{$row['comment']}</textarea></td></tr>";
$member.= "<tr><td colspan=\"4\"><input type=\"submit\" name=\"update\" value=\"Update Record\"></td></tr>";
$member.= "<tr><td colspan=\"6\">Payment History</td></tr>\n";
if(isset($payments)) {
	$member.= "<tr><td colspan=\"6\">{$payments}</td></tr>\n";
}
$member.= "</table>";
$member.= "</form>";

if(isset($_GET['printable'])) {
	renderPage($member,false);
} else {
	renderPage($member,true,'Membership Management',$db);
}

function flagLinkBuilder($flag,$value,$member) {
	if($value == 1) {
		return "<a href=\"toggleFlag.php?flag={$flag}&memberID={$_GET['memberID']}\">Yes</a>";
	} else {
		return "<a href=\"toggleFlag.php?flag={$flag}&memberID={$_GET['memberID']}\">No</a>";
	}
}
?>
