<?php

require_once("project.php");
$db=myDB();
if(!isset($_GET['confirm'])) {
	$newQS=$_SERVER['QUERY_STRING']."&confirm=true";
	$href="<a href=\"deleteFromTable.php?{$newQS}\">Confirm Deletion</a>";
	$back="<a href=\"index.php\">Back to Index</a>";
	renderPage($href."<br>".$back);
	exit();
} else {
	$sql="SELECT memberID FROM {$_GET['table']} WHERE {$_GET['wheres']}";
	$res=simpleQuery($sql,true,$db);
	$data=$res->fetchAll(PDO::FETCH_ASSOC);
	$row=$data[0];
	$memberID=$row['memberID'];

	if(count($data) >= 1) {
		$sql="DELETE FROM {$_GET['table']} WHERE {$_GET['wheres']}";
		$res=simpleQuery($sql,true,$db);
		header("Location: memberRecord.php?memberID={$memberID}");
	} else {
		renderPage("Unable to process delete request, multiple rows affected\n<!--\n{$sql}\n-->\n");
	}
}

?>
