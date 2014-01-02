<?php

require_once("project.php");
if(!isset($_GET['flag'])) {
	header("Location: index.php");
	exit();
}
$flags = getMemberFlags();
$db=myDB();

$year=date('Y');
$month=date('m');
if($month >= 5) {
	$year++;
}
$sql="SELECT memberID,namelast,namefirst,address,city,state,zip,phone,email FROM members WHERE {$_GET['flag']}=1 AND expiresyear >= {$year} ORDER BY namelast,namefirst";
$res = simpleQuery($sql,true,$db);
$body="<table cellpadding=\"2\" cellspacing=\"0\" border=\"0\">";
if(!isset($_GET['print']) && !isset($_GET['pdf'])) {
	$print="<a href=\"{$_SERVER['PHP_SELF']}?flag={$_GET['flag']}&print=print\">Printable View</a>";
	$body.="<tr><td colspan=\"3\">{$print}</td>";
	$print="<a href=\"{$_SERVER['PHP_SELF']}?flag={$_GET['flag']}&print=csv\">Download CSV</a>";
	$body.="<td colspan=\"3\">{$print}</td>";
	$print="<a href=\"map/index.php?mode={$_GET['flag']}\">Map</a>";
	$body.="<td colspan=\"3\">{$print}</td></tr>";
}
$date=date('m/d/Y H:i:s');
$data=$res->fetchAll(PDO::FETCH_ASSOC);
$body.="<tr><td colspan=\"9\" style=\"text-align: left\">{$flags[$_GET['flag']]} List -- ".count($data)." members</b></td></tr>";
$background=NULL;
$count=1;
foreach($data as $row) {
	$body.="<tr>\n";
	$count++;
	if($background != '#ffffff') {
		$background="#ffffff";
	} else {
		$background="#cacaca";
	}
	foreach($row as $k=>$v) {
		switch($k) {
		case "memberID":
			$url="<a href=\"memberRecord.php?memberID={$v}\">{$v}</a>";
			$body.="\t<td style=\"background-color: {$background};font-size: small\">{$url}</td>\n";
			break;
		default:
			$body.="\t<td style=\"background-color: {$background};font-size: small\">{$v}</td>\n";
			break;
		}
	}
	$body.="</tr>\n";
}

$body.="</table>";
if(isset($_GET['print'])) {
	if($_GET['print'] == 'csv') {
		$dateTime=date('m/d/Y g:iA');
		$csv=$dateTime."\r\n";
		$csv.=$flags[$_GET['flag']]." List -- ".count($data)." Members\r\n";
		foreach($data as $row) {
			$vals=array();
			foreach($row as $k=>$v) {
				$vals[]="\"{$v}\"";
			}
			$line=implode(",",$vals);
			$csv.=$line."\r\n";
		}
		header("Content-type: text/csv");
		header("Content-Disposition: attachment; filename=\"{$_GET['flag']}-{$dateTime}.csv\"");
		print $csv;
		exit();
	} else {
		renderPage($body,false);
	}
}else {
	renderPage($body,true,'Member Managment',$db);
}
?>
