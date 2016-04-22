<?php

require_once("project.php");
if(!isset($_GET['depositnum'])) {
	header("Location: index.php");
	exit();
} 
$sql="select m.NameFirst||' '||m.NameLast AS Name,d.datepaid AS Date,d.checknumber AS 'Check&nbsp;#',d.comment AS Comment,d.paymenttype AS Type,d.amount AS Amount from duesPaid AS d LEFT OUTER JOIN members AS m ON d.memberID=m.memberID WHERE d.bankDepositNumber={$_GET['depositnum']} ORDER BY d.datepaid DESC,m.NameLast,m.NameFirst";
$db=myDB();
$res = simpleQuery($sql,true,$db);
$data = $res->fetchAll(PDO::FETCH_ASSOC);
$tRows=array();
$gTotal=0;
$ccTotal=0;
$otherTotal=0;
$donationTotal=0;
$giftCertTotal=0;
foreach($data as $row) {
	if(count($tRows) == 0) {
		$tRows[]=getHeaders($row);
	}
	$line="<tr>";
	foreach($row as $k=>$v) {
		switch($k) {
		case "Check&nbsp;#":
			if($v == -3) {
				$line.="<td>Gift Cert.</td>";
			} elseif($v == -2) {
				$line.="<td>Donated GC</td>";
			} elseif($v == -1) {
				$line.="<td>CC</td>";
			} elseif($v == 0) {
				$line.="<td>CASH</td>";
			} else {
				$line.="<td>{$v}</td>";
			}
			$checkNum=$v;
			break;
		case "Amount":
			$line.=sprintf("<td align=\"right\">%0.2f</td>",$v);
			if($checkNum >= 0) {
				$gTotal+=$v;
			} elseif($checkNum == -1) {
				$ccTotal+=$v;
			} elseif($checkNum == -2) {
				$donationTotal+=$v;
			} elseif($checkNum == -3) {
				$giftCertTotal+=$v;
			} else {
				$otherTotal+=$v;
			}
			if(!isseT($byType[$type])) {
				$byType[$type]=0;
			}
			$byType[$type]+=$v;
			break;
		case "Type":
			$type=$v;
			$line.="<td>{$v}</td>";
			break;
		default:
			$line.="<td>{$v}</td>";
			break;
		}
	}
	$line.="</tr>";
	$tRows[]=$line;
}
$gTotal=sprintf("%0.2f",$gTotal);
$ccTotal=sprintf("%0.2f",$ccTotal);
$donationTotal=sprintf("%0.2f",$donationTotal);
$giftCertTotal=sprintf("%0.2f",$giftCertTotal);
$otherTotal=sprintf("%0.2f",$otherTotal);

$body="<table cellpadding=\"5\" cellspacing=\"0\" border=\"0\">";
$body.=implode("\n",$tRows);
$body.="<tr><td colspan=\"7\"><hr></td></tr>\n";
if($gTotal != 0) {
	$body.="<tr><td colspan=\"3\" align=\"right\">Cash / Checks Total</td><td colspan=\"4\" align=\"right\">{$gTotal}</td></tr>\n";
}
if($ccTotal != 0) {
	$body.="<tr><td colspan=\"3\" align=\"right\">Credit Card Total</td><td colspan=\"4\" align=\"right\">{$ccTotal}</td></tr>\n";
}
if($donationTotal != 0) {
	$body.="<tr><td colspan=\"3\" align=\"right\">Donated Gift. Cert.</td><td colspan=\"4\" align=\"right\">{$donationTotal}</td></tr>\n";
}
if($giftCertTotal != 0) {
	$body.="<tr><td colspan=\"3\" align=\"right\">Sold Gift. Cert.</td><td colspan=\"4\" align=\"right\">{$giftCertTotal}</td></tr>\n";
}
if($otherTotal != 0) {
	$body.="<tr><td colspan=\"3\" align=\"right\">Other</td><td colspan=\"4\" align=\"right\">{$otherTotal}</td></tr>\n";
}
$body.="<tr><td colspan=\"7\"><hr></td></tr>\n";
foreach($byType as $k=>$v) {
	$amt=sprintf("%0.2f",$v);
	$body.="<tr><td colspan=\"3\" align=\"right\">{$k}</td><td colspan=\"4\" align=\"right\">{$amt}</td></tr>\n";
}
$body.="<tr><td colspan=\"7\"><hr></td></tr>\n";
$body.="<tr><td colspan=\"7\"><hr></td></tr>\n";
$body.="</table>";
//renderPage($body,false,'Member Management',$db);
$title=sprintf("%d-%s Detail View",$_GET['depositnum'],date('mdY'));
renderPage($body,false,$title,$db);

function getHeaders($row) {
	$rv="<tr>";
	foreach($row as $k=>$v) {
		$rv.="<td class=\"invertBold\">{$k}</td>";
	}
	$rv.="</tr>";
	return $rv;
}
?>
