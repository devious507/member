<?php

require_once("project.php");


$dataFile=file_get_contents(DATAFILE);
$date=date('mdY');
header("Content-type: application/octet-stream");
header("Content-Disposition: attachment; filename=\"membershipDatabase-{$date}.s3db\"");
print $dataFile;
?>
