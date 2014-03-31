<?php

require_once("project.php");
$db=myDB();

if(!isset($_GET['depositnum'])) {
	header("Location: reporting.php");
	exit();
}

$deposit = $_GET['depositnum'];
$sql="select sum(amount) as amount,checknumber FROM duespaid WHERE bankDepositNumber={$deposit} GROUP BY checknumber ORDER BY checknumber";
$res = simpleQuery($sql,true,$db);
$data=$res->fetchAll(PDO::FETCH_ASSOC);
$g_total=0;
$chk_total=0;
$cc_total=0;
$cash_total=0;
$checksSum=0;
		$lines[]=sprintf("<tr><td class=\"right\">&nbsp;</td><td>Ck#</td><td class=\"right\">Amount</td><td>&nbsp;</td><td>&nbsp;</td></tr>");
foreach($data as $row) {
	if($row['checknumber'] == -1) {
		$cc_total=$row['amount'];
	} elseif($row['checknumber'] == 0) {
		$cash_total=$row['amount'];
	} else {
		$lines[]=sprintf("<tr><td class=\"right\">&nbsp;</td><td>{$row['checknumber']}</td><td class=\"right\">%.2f</td><td>&nbsp;</td><td>&nbsp;</td></tr>",$row['amount']);
		$chk_total+=$row['amount'];
		$checksSum+=$row['checknumber'];
	}
	$g_total+=$row['amount'];
}
if($chk_total > 0) {
	$lines[]=sprintf("<tr><td class=\"right\">Checks Total</td><td colspan=\"2\">(%d)</td><td class=\"right\">%.2f</td><td>&nbsp;</td></tr>",$checksSum,$chk_total);
}
if($cash_total >0) {
		$lines[]=sprintf("<tr><td class=\"right\">Cash Total</td><td>&nbsp;</td><td>&nbsp;</td><td class=\"right\">%.2f</td><td>&nbsp;</td></tr>",$cash_total);
}
if($cc_total > 0) {
		$lines[]=sprintf("<tr><td class=\"right\">Credit Card Total</td><td>&nbsp;</td><td>&nbsp;</td><td class=\"right\">%.2f</td><td>&nbsp;</td></tr>",$cc_total);
}
if($g_total >0) {
	$lines[]=sprintf("<tr><td class=\"right\">Cash & Checks Total</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td class=\"right\">%.2f</td></tr>",$cash_total+$chk_total);
	$lines[]=sprintf("<tr><td class=\"right\">Grand Total</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td class=\"right\">%.2f</td></tr>",$g_total);
}

$t1="<table cellspacing=\"0\" cellpadding=\"5\" border=\"0\" width=\"400\">";
if(isset($lines)) {
	$t1.=implode("",$lines);
}
$t1.="</table>";


$sql="select sum(amount),paymenttype FROM duespaid WHERE bankdepositNumber={$deposit} GROUP BY paymenttype ORDER BY paymenttype";
$res = simpleQuery($sql,true,$db);
$lines=array();
$g_total=0;
$data=$res->fetchAll(PDO::FETCH_ASSOC);
foreach($data as $row) {
	$amt = $row['sum(amount)'];
	$type= $row['paymenttype'];
	if($amt != 0) {
		$lines[]=sprintf("<tr><td class=\"left\">%s</td><td class=\"right\">%.2f</td></tr>",$type,$amt);
		$g_total+=$amt;
	}
}
$lines[]="<tr><td colspan=\"2\"><hr></td></tr>";
$lines[]=sprintf("<tr><td class=\"right\">Total</td><td class=\"right\">%.2f</td></tr>",$g_total);
$t2="<table cellspacing=\"0\" cellpadding=\"5\" border=\"0\">";
$t2.=implode("",$lines);
$t2.="</table>";

//$body="<div style=\"float: top\">Deposit Report # {$_GET['depositnum']}</div><div style=\"border: 1px solid black;width: 400px; float: left\">{$t1}</div>\n<div style=\"margin-left: 410px; width: 275px\">{$t2}</div>\n";
$genDate=date('m/d/Y H:i:s');
$body ="<table cellpadding=\"5\" cellspacing=\"0\" border=\"1\">";
$body.="<tr><td colspan=\"2\" class=\"invertBold\">Deposit Report #{$deposit} / Generated {$genDate}</td></tr>";
if(!isset($_GET['printable']) && !isset($_GET['pdf'])) {
	$body.="<tr><td class=\"topLeft\">{$t1}</td><td class=\"topLeft\">{$t2}</td></tr>";
	$form="<form method=\"get\" action=\"report_depositReport.php\">Change to Report # <input type=\"text\" name=\"depositnum\" size=\"3\"><input type=\"submit\" value=\"Go\"></form>";
	//$body.="<tr><td class=\"topleft\"><a href=\"report_depositReport.php?depositnum={$deposit}&printable=true\">Printable View</a> | <a href=\"report_depositReport.php?depositnum={$deposit}&pdf=true\">Download PDF</a></td><td class=\"topleft\">{$form}</td></tr>";
	$body.="<tr><td class=\"topleft\"><a href=\"report_depositReport.php?depositnum={$deposit}&printable=true\">Printable View</a><br><a href=\"report_depositDetail.php?depositnum={$deposit}\">Deposit Detail Report</a></td><td class=\"topleft\">{$form}</td></tr>";
} elseif(isset($_GET['pdf'])) {
	$body.="<tr><td class=\"topleft\" colspan=\"2\">{$t2}</td></tr>";
} else {
	$body.="<tr><td class=\"topLeft\">{$t1}</td><td class=\"topLeft\">{$t2}</td></tr>";
}
$body.="</table>";
if(isset($_GET['printable'])) {
	$myTitle=$deposit."-".date('mdY');
	renderPage($body,false,$myTitle);
} elseif(isset($_GET['pdf'])) {
	$date=date('mdY');
	$filename="Deposit_{$_GET['depositnum']}-{$date}.pdf";
	header("Content-type: application/pdf");
	getPDF($body,$filename,false,'landscape');
	renderPage($body,false);
} else {
	renderPage($body,true,'Member Manager',$db);
}
?>
