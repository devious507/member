<?php

require_once("project.php");
checkIsWriter();

$db=myDB();
$sql="SELECT memberID,max(expirationYear) as expirationYear FROM membershipcards GROUP BY memberID ORDER BY memberID";
$res=simpleQuery($sql,true,$db);
$data=$res->fetchAll(PDO::FETCH_ASSOC);
foreach($data as $row) {
	$sql="UPDATE members SET expiresyear={$row['expirationYear']} WHERE memberID={$row['memberID']} AND expiresyear!={$row['expirationYear']}";
	$res=simpleQuery($sql,true,$db);
}
$statements[]="<a href=\"utilities.php\">Back to Utilities</a>";
renderPage(" ");
?>
