<?php

include_once("../project.php");
if($_SESSION['write'] == 0 ) {
	header("Location: index.php");
	exit();
}
include_once("include/GoogleMap.php");
include_once("include/JSMin.php");


$map = new GoogleMapAPI();
$map->_minify_js=isset($_REQUEST['min'])?FALSE:TRUE;
$map->setWidth(1300);
$map->setHeight(650);
//$map->enableClustering();
//$map->addMarkerByAddress("901 11th Ave SE, Minot, ND","MINOT","Minot, ND Marker");
$year=date('Y');
if(date('m') > 4) {
	$year++;
}

$sql = 'select namelast,namefirst,address,city,state FROM members WHERE expiresyear >='.$year.' AND lat IS NOT NULL AND lon IS NOT NULL';
$sql = "select memberID,address,City,State FROM members WHERE expiresyear >={$year} AND (lat IS NULL OR lon IS NULL) LIMIT 50";
$db=myDB('../');
$res = simpleQuery($sql,true,$db); 
print "<pre>";
while(($row=$res->fetch(PDO::FETCH_ASSOC))==true) {
	$addr = $row['address'].", ".$row['City'].", ".$row['State'];
	$latlon=$map->getGeoCode($addr);
	if($latlon != false) {
		$lat = $latlon['lat'];
		$lon = $latlon['lon'];
		$sql2 = "UPDATE members SET lat={$lat}, lon={$lon} WHERE memberID={$row['memberID']}";
		$newRes = simpleQuery($sql2,true,$db);
		flush();
		ob_flush();
		print $addr."\n";
		print $sql2."\n";
	} else {
		print "memberID {$row['memberID']} has bad address!\n";
	}	
}
print "</pre>";
print "<a href=\"index.php\">Show Map</a>";
?>
