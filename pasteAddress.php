<?php

require_once("project.php");

//var_dump($_SESSION); exit();
$db = myDB();
if(!isset($_GET['memberID'])) {
	header("Location: index.php");
	exit();
}
$memberID = $_GET['memberID'];

$form ="<form method=\"POST\" action=\"pasteAddressAction1.php\">\n";
$form.="<table cellpadding=\"3\" cellspacing=\"0\" border=\"1\">\n";
$form.="<tr><td>Member ID</td><td><input type=\"hidden\" name=\"memberID\" value=\"{$memberID}\">{$memberID}</td></tr>\n";
$form.="<tr><td colspan=\"2\"><textarea name=\"pastebin\" rows=\"10\" cols=\"50\"></textarea></td></tr>\n";
$form.="<tr><td colspan=\"2\"><input type=\"submit\"></td></tr>\n";
$form.="</table>\n";
$form.="</form>\n";

renderPage($form,true,'Update Record From Paste',$db);
?>
