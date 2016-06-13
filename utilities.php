<?php

require_once("project.php");
$db=myDB();

$menu="<ul>";
if($_SESSION['write'] == 1) {
	$menu.="\t<li><a href=\"utility_uploadData.php\">Upload Data</a></li>\n";
	$menu.="\t<li><a href=\"utility_scanMissingCards.php\">Scan for Missing Cards</a></li>\n";
	$menu.="\t<li><a href=\"utility_normalizePhoneNumbers.php\">Normalize Phone Number Formats</a></li>\n";
	$menu.="\t<li><a href=\"utility_updateExpirationYear.php\">Update Expiration Years In Membership File</a></li>\n";
}

$menu.="\t<li><a href=\"utility_lookupByCardNumber.php\">Lookup Member by Card Number</a></li>\n";
if($_SESSION['write'] == 1) {
	$menu.="\t<li><a href=\"utility_closeDeposit.php\">Close Batch and Open New One</a><br>&nbsp;</li>\n";

	$menu.="\t<li><a href=\"utility_backupDataFile.php\">Backup Data File</a></li>\n";
	$menu.="\t<li><a href=\"utility_restoreDataFile.php\">Restore Data File</a></li>\n";
	$menu.="\t<li><a href=\"utility_backupEntireApplication.php\">Backup Entire Application (.tar.gz file)</a><br>&nbsp;</li>\n";

	$menu.="\t<li><a href=\"map/geoCode.php\">GeoCode Address's in Database</a><br>&nbsp;</li>\n";

}
$menu.="\t<li><a href=\"utility_userManagement.php\">User Management</a><br>&nbsp;</li>\n";
$menu.="</ul>\n";

renderPage($menu,true,'Member Management',$db);
?>
