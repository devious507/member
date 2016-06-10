<?php

require_once("project.php");

if($_SESSION['write'] == 0 ) {
	header("Location: utility_userManagement.php");
	exit();
}
if(!isset($_GET['username'])) {
	header("Location: utility_userManagement.php");
	exit();
}

$sql="SELECT * FROM users WHERE username='{$_GET['username']}'";
$res = simpleQuery($sql,true,$db);
$data = $res->fetchAll(PDO::FETCH_ASSOC);
if(count($data) != 1) {
	header("Location: utility_userManagement.php");
	exit();
}
$body="<form method=\"post\" action=\"utility_changePasswordAction.php\">";
$body.="<input type=\"hidden\" name=\"username\" value=\"{$_GET['username']}\">";
$body.="<table cellpadding=\"5\" cellspacing=\"0\" border=\"1\">";
$body.="<tr><td>New Password</td><td><input type=\"password\" name=\"newpass1\"></td></tr>\n";
$body.="<tr><td>Repeat Password</td><td><input type=\"password\" name=\"newpass2\"></td></tr>\n";
$body.="<tr><td colspan=\"2\"><input type=\"submit\" value=\"Update Password\"></td></tr>\n";
$body.="</table>";
$body.="</form>";
renderPage($body);


?>
