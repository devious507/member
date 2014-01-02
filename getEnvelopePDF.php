<?php

require_once("project.php");
$db=myDB();

$sql="SELECT memberID FROM members WHERE printenvelope=1";
$res=simpleQuery($sql,true,$db);
$data=$res->fetchAll(PDO::FETCH_ASSOC);
if(!isset($_GET['keep'])) {
	$body="<p><a href=\"getEnvelopePDF.php?keep=true\">Print and Keep DB Markers</a></p>";
	$body.="<p><a href=\"getEnvelopePDF.php?keep=false\">Print and Clear DB Markers</a></p>";
	renderPage($body);
	exit();
}
if(count($data) == 0) {
	renderPage("<p>No envelopes to print</p>");
} else {
	generateEnvelope($db,$_GET['keep']);
}
?>
