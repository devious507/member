<?php

require_once("project.php");
$db=myDB();

$sql="SELECT memberID,namelast,namefirst,address,city,state,zip,phone,email FROM members WHERE boardmember=1 ORDER BY namelast,namefirst";
$res = simpleQuery($sql,true,$db);
$body="<table cellpadding=\"2\" cellspacing=\"0\" border=\"0\">";
if(!isset($_GET['print']) && !isset($_GET['pdf'])) {
	$print="<a href=\"{$_SERVER['PHP_SELF']}?print=true\">Printable View</a>";
	$body.="<tr><td colspan=\"8\">{$print}</td></tr>";
}
$date=date('m/d/Y H:i:s');
$data=$res->fetchAll(PDO::FETCH_ASSOC);
$body.="<tr><td colspan=\"8\" style=\"text-align: left\">Board Member List -- ".count($data)." members</b></td></tr>";
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
	renderPage($body,false);
}else {
	renderPage($body);
}
?>
