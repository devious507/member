<?php

require_once("project.php");
$db=myDB();

$sql="SELECT * FROM {$_GET['table']} WHERE {$_GET['wheres']}";
$res=simpleQuery($sql,true,$db);
$data=$res->fetchAll(PDO::FETCH_ASSOC);
$id="id=\"focusBox\"";
if(count($data) == 1) {
	$row=$data[0];
	$body="<form method=\"post\" action=\"editLineAction.php\">\n";
	$body.="<input type=\"hidden\" name=\"tablename\" value=\"{$_GET['table']}\">\n";
	$body.="<table cellpadding=\"5\" cellspacing=\"0\" border=\"1\">\n";
	foreach($row as $k=>$v) {
		switch($k) {
		case "memberID":
		case "expirationYear":
		case "cardNumber":
		case "void":
			$lbl[]="\t<td>{$k}</td>\n";
			$val[]="\t<td><input type=\"hidden\" name=\"h_{$k}\" value=\"{$v}\">{$v}</td>";
			break;
		default:
			$lbl[] ="\t<td>{$k}</td>\n";
			$val[] ="\t<td><input {$id} type=\"text\" size=\"15\" name=\"{$k}\" value=\"{$v}\"></td>\n";
			$id='';
			break;
		}
	}
	$lbl[]="\t<td>&nbsp;</td>\n";
	$val[]="\t<td><input type=\"submit\" name=\"submit\" value=\"Update\"></td>\n";
	$body.="<tr>".implode("",$lbl)."</tr>";
	$body.="<tr>".implode("",$val)."</tr>";
	$body.="</table></form>\n";
	renderPage($body);
} else {
	renderPage("Unable to edit line, multiple lines affected!");
}

?>
