<?php

require_once("project.php");

$sql="SELECT max(memberID) FROM members";
$db = myDB();
$res=simpleQuery($sql,true,$db);
$data=$res->fetch(PDO::FETCH_ASSOC);
if(count($data) == 0) {
	$maxID=1000;
} else {
	$maxID=$data['max(memberID)'];
}
$maxID++;
$sql="INSERT INTO members (memberid,wantsemail,lifemember,deceased) VALUES ({$maxID},1,0,0)";
$res=simpleQuery($sql,true,$db);
header("Location: memberRecord.php?memberID={$maxID}");
?>
