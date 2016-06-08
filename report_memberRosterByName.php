<?php

require_once("project.php");
$db=myDB();

if(!isset($_GET['year'])) {
	header("Location: index.php");
}

$sql="SELECT count(cardnumber) FROM membershipcards WHERE expirationyear={$_GET['year']}";
$res=simpleQuery($sql,true,$db);
$row=$res->fetch(PDO::FETCH_ASSOC);
$cards = $row['count(cardnumber)'];

$sql="SELECT memberid FROM membershipcards WHERE expirationyear={$_GET['year']} GROUP BY memberid";
$res=simpleQuery($sql,true,$db);
$data=$res->fetchAll(PDO::FETCH_ASSOC);
$members=count($data);

$sql="SELECT cardnumber FROM membershipcards WHERE expirationyear={$_GET['year']} AND void=1";
$res=simpleQuery($sql,true,$db);
$data=$res->fetchAll(PDO::FETCH_ASSOC);
$voids=count($data);

$sql="SELECT c.cardnumber,c.void,m.namelast||', '||m.namefirst,m.phone,m.spouse_last||', '||m.spouse_first,m.spouse_phone,c.note FROM membershipcards AS c LEFT OUTER JOIN members AS m ON c.memberid=m.memberid WHERE c.expirationyear={$_GET['year']} ORDER BY m.namelast,m.namefirst";
$res = simpleQuery($sql,true,$db);
$body="<table cellpadding=\"2\" cellspacing=\"0\" border=\"0\">";
if(!isset($_GET['print']) && !isset($_GET['pdf'])) {
	$print="<a href=\"report_memberRosterByName.php?year={$_GET['year']}&print=true\">Printable View</a>";
	$body.="<tr><td colspan=\"8\">{$print}</td></tr>";
}
$date=date('m/d/Y H:i:s');
$body.="<tr><td colspan=\"8\" style=\"text-align: left\">Card Expiration Year: <b>{$_GET['year']}</b></td></tr>";
$body.="<tr><td colspan=\"8\"> {$members} members // {$cards} cards issued // {$voids} cards voided (Report Generated {$date})</td></tr>";
$data=$res->fetchAll(PDO::FETCH_ASSOC);
$background=NULL;
foreach($data as $row) {
	$void=$row['void'];
	unset($row['void']);
	$body.="<tr>";
	if($background != '#ffffff') {
		$background="#ffffff";
	} else {
		$background="#cacaca";
	}
	foreach($row as $k=>$v) {
		if($void == 1) {
			$body.="<td style=\"background-color: {$background};font-size: small\"><del>{$v}</del></td>";
		} else {
			$body.="<td style=\"background-color: {$background};font-size: small\">{$v}</td>";
		}
	}
	$body.="</tr>";
}

$body.="</table>";
if(isset($_GET['print'])) {
	renderPage($body,false);
}else {
	renderPage($body,true,'Member Management',$db);
}
?>
