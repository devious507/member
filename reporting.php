<?php

require_once("project.php");

$db=myDB();
$this_year=date('Y');
$last_year=$this_year-1;
$next_year=$this_year+1;
$depositNum = getCurrentDepositNumber($db);
$prevDepositNum = $depositNum-1;

$menu = "<ul>";
$menu.= "<li>Member Counts\n";
$menu.= "<ul>";
$menu.= "<li><a href=\"report_membershipCount.php?year={$this_year}\">{$last_year} Member Counts</a></li>\n";
$menu.= "<li><a href=\"report_membershipCount.php?year={$next_year}\">{$this_year} Member Counts</a></li>\n";
$menu.= "</ul></li>";

$menu.= "<li>Mebership Lists By Name<ul>";
$menu.= "<li><a href=\"report_memberRosterByName.php?year={$this_year}\">{$last_year} Membership Year</a></li>\n";
$menu.= "<li><a href=\"report_memberRosterByName.php?year={$next_year}\">{$this_year} Membership Year</a></li>\n";
$menu.= "<li><a href=\"report_EmailListExport.php\">Email List Export</a></li>\n";
$menu.="</ul></li>";

$menu.= "<li>Membership Lists By Flag<ul>";
$flag=getMemberFlags();
foreach($flag as $k=>$v) {
	$menu.="<li><a href=\"report_MemberByFlag.php?flag={$k}\">{$v}</a></li>";
}
$menu.= "</ul></li>";

$menu.="<li>Members without Email Address<ul>\n";
$menu.= "<li><a href=\"report_membersWithoutEmail.php?year={$this_year}\">{$last_year} Membership Year</a></li>";
$menu.= "<li><a href=\"report_membersWithoutEmail.php?year={$next_year}\">{$this_year} Membership Year</a></li>";
$menu.="</ul></li>\n";

$menu.="<li>Map Stuff<ul>\n";
$menu.= "<li><a href=\"map/geoCode.php\">GeoCode Uncoded Addresses</a></li>";
$menu.= "<li><a href=\"map/index.php\">Current Member Map</a></li>";
$menu.="</ul></li>\n";

$menu.= "<li><a href=\"report_depositReport.php?depositnum={$depositNum}\">Deposit Report</a></li>\n";

$menu.= "</ul>\n";

renderPage($menu,true,'Member Management',$db);
?>
