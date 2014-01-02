<?php

require_once("project.php");
$db=myDB();
if(!isset($_GET['year'])) {
	header("Location: reporting.php");
	exit();
}
$sql="select m.NameFirst,m.NameLast,m.address,m.City,m.State,m.Zip FROM membershipcards AS c LEFT OUTER JOIN members as m ON c.memberID=m.memberID WHERE c.expirationYear={$_GET['year']} AND (m.email = '' OR m.email IS NULL) GROUP BY NameFirst,NameLast,address,City,State,Zip ORDER BY NameLast,NameFirst";
$res=simpleQuery($sql,true,$db);
$data=$res->fetchAll(PDO::FETCH_ASSOC);
$body="<table>";
$count=1;
foreach($data as $row) {
	$body.="<tr>";
	$body.="<td>{$count}</td>";
	$count++;
	foreach($row as $k=>$v) {
		$body.="<td>{$v}</td>";
	}
	$body.="</tr>";
}
$body.="</table>";
renderPage($body,true,'Member Manager',$db);

?>
