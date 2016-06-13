<?php

require_once("project.php");

if($_SESSION['write'] == 0 ) {
	$sql="SELECT username,write FROM users WHERE username='{$_SESSION['username']}'";
} else {
	$sql="SELECT username,write FROM users ORDER BY username";
}
$res = simpleQuery($sql,true,$db);
$data = $res->fetchAll(PDO::FETCH_ASSOC);
$body="<table cellpadding=\"5\" cellspacing=\"0\" border=\"1\">";
foreach($data as $row) {
	$lbl=$row['username'];
	if($row['write'] == 0) {
		$lbl.=" - READONLY";
	}
	$changepass="<a href=\"utility_changePassword.php?username={$row['username']}\">Change Passwd</a>";
	$deletelink="<a href=\"utility_deleteUser.php?username={$row['username']}\">Delete</a>";
	$toggleWrite="<a href=\"utility_toggleWrite.php?username={$row['username']}\">Change Read Only</a>";
	if($row['username'] == $_SESSION['username']) {
		$deletelink=$toggleWrite="&nbsp;";
	}
	$body.=sprintf("<tr><td>%s</td><td>%s</td><td>%s</td><td>%s</td></tr>",
		$lbl,$changepass,$toggleWrite,$deletelink);
}
$body.="<tr><td colspan=\"4\"><hr></td></tr>";
$body.="<tr><td colspan=\"4\"><a href=\"utility_addNewUser.php\">Add User</a></td></tr>";
$body.="</table>";

renderPage($body);


?>
