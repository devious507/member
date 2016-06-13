<?php

require_once("project.php");
checkIsWriter();

$db = myDB();


if(!isset($_GET['membership_year'])) {
	$year=date('Y');
	$year++;
	$body ="<form method=\"get\" action=\"utility_scanMissingCards.php\">";
	$body.="Membership Year <input type=\"text\" size=\"5\" name=\"membership_year\" value=\"{$year}\"> <input type=\"submit\" value=\"Scan\">";
	$body.="</form>\n";
	renderPage($body);
} else {
	$lines=array();
	$year=$_GET['membership_year'];
	$sql = "SELECT min(cardnumber),max(cardnumber) FROM membershipcards WHERE expirationyear={$year}";
	$res = simpleQuery($sql,true,$db);
	$data=$res->fetchAll(PDO::FETCH_ASSOC);
	if(count($data) != 1) {
		$num=count($data);
		renderPage("unable to scan, {$sql} returned an unexpected number of rows ({$num})");
		exit();
	} else {
		$row=$data[0];
		$minCard = $row['min(cardnumber)'];
		$maxCard = $row['max(cardnumber)'];
		$sql="SELECT cardnumber FROM membershipcards WHERE expirationyear={$year} ORDER BY cardnumber ASC";
		$res=simpleQuery($sql,true,$db);
		$currentCount=$minCard;
		$data=$res->fetchAll(PDO::FETCH_ASSOC);
		foreach($data as $row) {
			$currentData = $row['cardNumber'];
			if($currentCount != $currentData) {
				$lines[]="<tr><td>Database entry {$currentCount} not found -- {$currentData}</td></tr>";
				$currentCount=$currentData;
			}
			$currentCount++;
		}
		$body ="<table cellpadding=\"5\" cellspacing=\"0\" border=\"1\">";
		$body.="<tr><td>".count($lines)." holes found in sequence number</td></tr>";
		if(count($lines) > 0) {
			$body.=implode("\n",$lines);
		}
		$body.="</table>\n";
		renderPage($body);
	}
}
?>
