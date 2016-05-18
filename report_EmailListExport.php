<?php

require_once("project.php");
$db=myDB();

$year=date('Y');
$month=date('m');
if($month >= 5) {
	$year++;
}
$sql="SELECT email,namefirst,namelast FROM members WHERE wantsemail=1 AND expiresyear >= {$year} ORDER BY namelast,namefirst";
$res = simpleQuery($sql,true,$db);
$data=$res->fetchAll(PDO::FETCH_ASSOC);
header("Content-type: text/plain");
foreach($data as $entry) {
	printf("%s\t%s\t%s\n",$entry['email'],$entry['NameFirst'],$entry['NameLast']);
}
?>
