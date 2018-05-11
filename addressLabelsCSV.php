<?php

require_once("project.php");
$db=myDB();

$sql="SELECT memberID FROM members WHERE printenvelope=1";
$res=simpleQuery($sql,true,$db);
$data=$res->fetchAll(PDO::FETCH_ASSOC);
if(!isset($_GET['keep'])) {
	$body="<p>Address Labels</p>";
	$body.="<p><a href=\"addressLabelsCSV.php?keep=true\">Export and Keep DB Markers</a></p>";
	$body.="<p><a href=\"addressLabelsCSV.php?keep=false\">Export and Clear DB Markers</a></p>";
	renderPage($body);
	exit();
}
if(count($data) == 0) {
	renderPage("<p>No envelopes to print</p>");
} else {
	header("Content-type: text/plain");
	generateLabelLine($db,$_GET['keep']);
}
?>
