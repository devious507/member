<?php

require_once("project.php");
if(!isset($_GET['depositnum'])) {
	header("Location: index.php");
	exit();
} 
$sql="select m.NameFirst,m.NameLast,d.datepaid AS Date,d.checknumber AS 'Check #',d.comment AS Comment,d.paymenttype AS Type,d.amount AS Amount from duesPaid AS d LEFT OUTER JOIN members AS m ON d.memberID=m.memberID WHERE d.bankDepositNumber={$_GET['depositnum']} ORDER BY d.datepaid DESC,m.NameLast,m.NameFirst";
$db=myDB();
$res = simpleQuery($sql,true,$db);
$data = $res->fetchAll(PDO::FETCH_ASSOC);
$tRows=array();
$gTotal=0;
foreach($data as $row) {
	if(count($tRows) == 0) {
		$tRows[]=getHeaders($row);
	}
	$line="<tr>";
	foreach($row as $k=>$v) {
		switch($k) {
		case "Check #":
			if($v == -1) {
				$line.="<td>CC</td>";
			} elseif($v == 0) {
				$line.="<td>CASH</td>";
			} else {
				$line.="<td>{$v}</td>";
			}
			break;
		case "Amount":
			$line.=sprintf("<td align=\"right\">%0.2f</td>",$v);
			$gTotal+=$v;
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
$body="<table cellpadding=\"5\" cellspacing=\"0\" border=\"0\">";
$body.=implode("\n",$tRows);
$body.="<tr><td colspan=\"7\"><hr></td></tr>\n";
$body.="<tr><td colspan=\"3\" align=\"right\">Deposit Total</td><td colspan=\"4\" align=\"right\">{$gTotal}</td></tr>\n";
$body.="<tr><td colspan=\"7\"><hr></td></tr>\n";
$body.="<tr><td colspan=\"7\"><hr></td></tr>\n";
$body.="</table>";
renderPage($body,true,'Member Management',$db);

function getHeaders($row) {
	$rv="<tr>";
	foreach($row as $k=>$v) {
		$rv.="<td class=\"invertBold\">{$k}</td>";
	}
	$rv.="</tr>";
	return $rv;
}
?>
