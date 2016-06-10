<?php

require_once("project.php");
if($_SESSION['write'] == 0 ) {
	header("Location: index.php");
	exit();
}



$date=date('mdY');
$cmd = "/bin/tar -zcO ".escapeshellarg(dirname(__FILE__));
header("Content-type: application/x-gzip");
header("Content-Disposition: attachment; filename=\"membershipApplication.tar.gz\"");
system($cmd);
?>
