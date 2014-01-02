<?php

require_once("project.php");
$db=myDB();
// Unique Members
$sql="select memberid,expirationyear FROM membershipcards WHERE expirationyear={$_GET['year']} GROUP BY memberid,expirationyear";
$res = simpleQuery($sql,true,$db);
$data=$res->fetchAll(PDO::FETCH_ASSOC);
$memberCount = count($data);

// Life Members
$sql = "select count(*) FROM members WHERE lifeMember=1 AND deceased != 1";
$res = simpleQuery($sql,true,$db);
$row = $res->fetch();
$lifeCount = $row[0];

// Membership Cards
$sql="SELECT count(*) FROM membershipcards WHERE expirationyear={$_GET['year']}";
$res = simpleQuery($sql,true,$db);
$row=$res->fetch();
$cardCount = $row[0];

// Voids
$sql="SELECT count(*) FROM membershipcards WHERE expirationyear={$_GET['year']} AND void=1";
$res = simpleQuery($sql,true,$db);
$row=$res->fetch();
$voidCount = $row[0];

$body="<table cellpadding=\"5\" border=\"1\" cellspacing=\"0\" width=\"500\">";
$date=date("m/d/Y H:i:s");
$body.="<tr><td style=\"text-align: center\" colspan=\"4\">Report Generated {$date} for memberships expring in {$_GET['year']}</td></tr>";
$body.="<tr><th class=\"left\">Unique Members</th><th class=\"left\">Life Members</th><th class=\"left\">Cards Issued</th><th class=\"left\">VOIDS</th></tr>";
$body.="<tr><td>{$memberCount}</td><td>{$lifeCount}</td><td>{$cardCount}</td><td>{$voidCount}</td></tr>";
$body.="</table>\n";

$flags=getMemberFlags();
unset($flags['wantsemail']);
unset($flags['deceased']);
unset($flags['lifemember']);
unset($flags['boardmember']);
foreach($flags as $k=>$v) {
	$sql="SELECT count({$k}) FROM members WHERE {$k}=1 AND expiresyear>={$_GET['year']}";
	$res=simpleQuery($sql,true,$db);
	$row=$res->fetch();
	$flagCt[$k]=$row[0];
}

$body.="<hr><table cellpadding=\"5\" cellspacing=\"0\" border=\"1\">\n";
foreach($flagCt as $k=>$v) {
	$hdr[]="<a href=\"report_MemberByFlag.php?flag={$k}\">{$flags[$k]}</a>";
	//$hdr[]=$flags[$k];
	$dat[]=$v;
}
$body.="<tr><td align=\"right\">".implode("</td><td align=\"right\">",$hdr)."</td></tr>\n";
$body.="<tr><td align=\"right\">".implode("</td><td align=\"right\">",$dat)."</td></tr>\n";

$body.="</table>\n";


if(isset($_GET['printable'])) {
	renderPage($body,false);
	exit();
} else {
	$body.="<hr><a href=\"report_membershipCount.php?year={$_GET['year']}&printable=true\">Print View</a>";
	renderPage($body,true,'Member Management',$db);
	exit();
}

?>
