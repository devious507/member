<?php

require_once("project.php");
$db=myDB();

$sql="SELECT namelast,namefirst,address,city,state,phone FROM members WHERE lifemember=1 ORDER BY namelast,namefirst";
$res = simpleQuery($sql,true,$db);
$body="<table cellpadding=\"2\" cellspacing=\"0\" border=\"0\">";
if(!isset($_GET['print']) && !isset($_GET['pdf'])) {
	$print="<a href=\"{$_SERVER['PHP_SELF']}?print=true\">Printable View</a>";
	$body.="<tr><td colspan=\"8\">{$print}</td></tr>";
}
$date=date('m/d/Y H:i:s');
$body.="<tr><td colspan=\"8\" style=\"text-align: left\">Life Member List</b></td></tr>";
$data=$res->fetchAll(PDO::FETCH_ASSOC);
$background=NULL;
$count=1;
foreach($data as $row) {
	array_unshift($row,$count);
	$count++;
	if($background != '#ffffff') {
		$background="#ffffff";
	} else {
		$background="#cacaca";
	}
	foreach($row as $k=>$v) {
		$body.="<td style=\"background-color: {$background};font-size: small\">{$v}</td>";
	}
	$body.="</tr>";
}

$body.="</table>";
if(isset($_GET['print'])) {
	renderPage($body,false);
}else {
	renderPage($body);
}
?>
