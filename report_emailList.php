<?php

require_once("project.php");
$db = myDB();

$month=date('m');
if(!isset($_GET['year'])) {
	$year=date('Y');
	$m=date('m');
	if($m > 4) {
		$year++;
	}
} else {
	$year=$_GET['year'];
}

$sql="SELECT c.expirationyear,m.email FROM membershipcards AS c LEFT OUTER JOIN members AS m ON c.memberid=m.memberid WHERE m.email NOT NULL AND m.email != '' AND c.expirationyear={$year} AND wantsemail=1 GROUP BY c.expirationyear,m.email ORDER BY m.email";
$res=simpleQuery($sql,true,$db);
$data=$res->fetchAll();

$body ="<table cellpadding=\"2\" cellspacing=\"0\" border=\"0\">";
$body.="<tr><td>Card Expiration Year: {$year}</td></tr>";
foreach($data as $row) {
	$body.="<tr>";
	$body.="<td>{$row[1]}</td>";
	$body.="</tr>";
}
$body.="</table>";
renderPage($body,false);
