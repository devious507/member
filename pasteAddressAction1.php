<?php
//
// Incoming Variables
//
// $_POST["memberID"]
// $_POST["pastebin"]
//


require_once("project.php");
//var_dump($_SESSION); exit();
$db=myDB();

if(!isset($_POST['memberID'])) {
	header("Location: index.php");
	exit();
} elseif(!isset($_POST['pastebin'])) {
	header("Location: index.php");
	exit();
} else {
	$memberID=$_POST['memberID'];
	$address=preg_split("/\n/",$_POST['pastebin']);
}

//header("Content-type: text/plain"); var_dump($_POST);

$nameFull = $address[0];
$nameArr = preg_split("/ /",$nameFull);
$nameFirst = array_shift($nameArr);
$nameLast = implode(" ",$nameArr);

$street_address = $address[1];

$tAddress = $address[2];
$tmp=preg_split("/ /",$tAddress);
$city=preg_replace("/,/","",array_shift($tmp));
$state=array_shift($tmp);
$zip=array_shift($tmp);

$phone = preg_replace("/\D/",'',$address[3]);
$phone = preg_replace("/^1/",'',$phone);
$email = $address[4];

$form ="<form method=\"post\" action=\"updateMember.php\">\n";
$form.="<input type=\"hidden\" name=\"memberID\" value=\"{$memberID}\">\n";
$form.="<table cellpadding=\"3\" cellspacing=\"0\" border=\"1\">\n";

$form.=myLine("First Name","nameFirst",$nameFirst);
$form.=myLine("Last Name","nameLast",$nameLast);
$form.=myLine("Address","address",$street_address);
$form.=myLine("City","City",$city);
$form.=myLine("State","State",$state);
$form.=myLine("Zip","Zip",$zip);
$form.=myLine("Phone","phone",$phone);
$form.=myLine("Email","email",$email);

$form.="<tr><td colspan=\"2\"><input type=\"submit\" name=\"update\"></td></tr>\n";
$form.="</table>\n";
$form.="</form>\n";

renderPage($form,true,'Update Record From Paste',$db);


function myLine($label,$name,$value) {
	return "<tr><td>{$label}</td><td><input type=\"text\" name=\"{$name}\" value=\"{$value}\"></td></tr>\n";
}
?>

