<?php

include_once("../project.php");
include_once("include/GoogleMap.php");
include_once("include/JSMin.php");

$map = new GoogleMapAPI();
$map->_minify_js=isset($_REQUEST['min'])?FALSE:TRUE;
$map->setWidth('100%');
$map->setHeight('95%');
if(isset($_GET['cluster']) && $_GET['cluster'] == 'false') {
} else {
	// Enable Marker Clustering
	 $map->enableClustering();
	// //Set options (passing nothing to set defaults, just demonstrating usage
	 $map->setClusterOptions(12);
	 $map->setClusterLocation("include/markerclusterer_compiled.js");
}



$year=date('Y');
if(date('m') > 4) {
	$year++;
}

$sql = 'select memberid,namelast,namefirst,address,city,state,lat,lon FROM members WHERE expiresyear >='.$year.' AND lat IS NOT NULL AND lon IS NOT NULL';
if(isset($_GET['mode']) && $_GET['mode'] == 'singlemember' && isset($_GET['value'])) {
	$sql.=" AND memberID='{$_GET['value']}'";
} elseif(isset($_GET['mode'])) {
	$sql.= " AND {$_GET['mode']}=1";
}
$db=myDB('../');
$res = simpleQuery($sql,true,$db); 
while(($row=$res->fetch(PDO::FETCH_ASSOC))==true) {
	$divisor=1000000;
	$offset['lat']=rand(0,99)/$divisor;
	$offset['lon']=rand(0,99)/$divisor;
	if(rand(0,1)==0) {
		$offset['lat']*=-1;
	}
	if(rand(0,1)==0) {
		$offset['lat']*=-1;
	}
	$lat=$row['lat']+$offset['lat'];
	$lon=$row['lon']+$offset['lon'];
	$name = $row['NameFirst'].' '.$row['NameLast'];
	$name.="<br>".$row['address'];
	$name.="<br>".$row['City'].', '.$row['State'];
	$name.="<br><a href=\"../memberRecord.php?memberID={$row['memberID']}\">Membership Record</a>";
	$map->addMarkerByCoords($lon,$lat,'',$name);
}
	//exit();

?>
<!--
<?=$sql;?>
-->
<html>
<head>
<?=$map->getHeaderJS();?>
<?=$map->getMapJS();?>
</head>
<body>
<a href="../index.php">Back</a>
<?=$map->printOnLoad();?>
<?=$map->printMap();?>
<?=$map->printSidebar();?>
</body>
</html>
