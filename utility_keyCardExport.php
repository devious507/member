<?php


require_once("project.php");

if( ($_SESSION['write'] == 0) && ($_GET['username'] != $_SESSION['username']) ) {
	header("Location: utility_userManagement.php");
	exit();
}

if(!isset($_GET['export_type'])) {
	$types=array("members"=>"Regular Members",
		"trusted"=>"Trusted Members",
		"marked_t"=>"Trusted Members Marked for Printing",
		"marked_r"=>"Regular Members Marked for Printing");
	$body="<p>Keycard Export Menu</p><ul>";
	foreach($types as $k=>$v) {
		$body.="<li><a href=\"utility_keyCardExport.php?export_type={$k}\">{$v}</a></li>";
	}
	$body.="</ul>";
	renderPage($body,true,'Member Management',$db);
	exit();
}

$year=date('Y');
if(date('n') >= 5) {
	$year++;
}
switch($_GET['export_type']) {
case "trusted":
	$sql = "SELECT NameFirst,NameLast,keycard_number,visual_id FROM members WHERE (trustedmember=1 OR boardmember=1) AND expiresyear>={$year} AND keycard_number IS NOT NULL AND keycard_number != '' ORDER BY keycard_number";
	$filename="TrustedMembers.csv";
	break;
case "members":
	$sql = "SELECT NameFirst,NameLast,keycard_number,visual_id FROM members WHERE (trustedmember=0 AND boardmember=0) AND expiresyear>={$year} AND keycard_number IS NOT NULL AND keycard_number != '' ORDER BY keycard_number";
	$filename="RegularMembers.csv";
	break;
case "marked_r":
	$sql = "SELECT NameFirst,NameLast,keycard_number,visual_id FROM members WHERE printenvelope=1 AND expiresyear>={$year} AND keycard_number IS NOT NULL AND keycard_number != '' ORDER BY keycard_number";
	$filename="MarkedForPrinting_R.csv";
	break;
case "marked_t":
	$sql = "SELECT NameFirst,NameLast,keycard_number,visual_id FROM members WHERE (trustedmember=1 OR boardmember=1) AND printenvelope=1 AND expiresyear>={$year} AND keycard_number IS NOT NULL AND keycard_number != '' ORDER BY keycard_number";
	$filename="MarkedForPrinting_T.csv";
	break;
default:
	header("Location: utility_keyCardExport.php");
	exit();
}


$res=simpleQuery($sql,true,$db);
$data=$res->fetchAll(PDO::FETCH_ASSOC);
$output='';
if(count($data) > 0) {
	foreach($data as $line) {
		$First=preg_replace("/,/","",$line['NameFirst']);
		$Last =preg_replace("/,/","",$line['NameLast']);
		$output.=sprintf("%s,%s,,%s,%s\n",$First,$Last,$line['keycard_number'],$line['keycard_number']);
	}
}
//header("Content-type: text/plain");
header("Content-Disposition: attachment; filename=\"{$filename}\"");
print $output;

?>
