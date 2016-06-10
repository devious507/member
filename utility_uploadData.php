<?php

require_once("project.php");
if($_SESSION['write'] == 0 ) {
	header("Location: index.php");
	exit();
}

$db = myDB();

if(!isset($_GET['dataType']) && !isset($_POST['dataType'])) {
	$body ="Select file type to upload:<br> <br>";
	$body.="<a href=\"utility_uploadData.php?dataType=members\">Member List</a><br>";
	$body.="<a href=\"utility_uploadData.php?dataType=duesPaid\">Payments Made</a><br>";
	$body.="<a href=\"utility_uploadData.php?dataType=membershipCards\">Membership Cards</a><br>";
	$body.="<a href=\"utility_uploadData.php?dataType=bankDeposit\">Bank Deposit List</a></br>";
	renderPage($body);
	exit();
} elseif(!isset($_FILES['file'])) {
	$body ='<form action="utility_uploadData.php" method="post" enctype="multipart/form-data">';
	$body.='<input type="hidden" name="dataType" value="'.$_GET['dataType'].'">';
	$body.='<label for="file">Filename:</label>';
	$body.='<input type="file" name="file" id="file"><br>';
	$body.='<input type="submit" name="submit" value="Submit">';
	$body.="</form>";
	renderPage($body);
	exit();
} else {
	$input = $_FILES['file']['tmp_name'];
	$fh = fopen($input,'r');
	while(($Data=fgetcsv($fh)) !== false) {
		foreach($Data as $k=>$v) {
			$Data[$k]=preg_replace("/'/","''",$v);
		}
		$sql[]="INSERT INTO {$_POST['dataType']} VALUES ('".implode("','",$Data)."');";
	}
	$db=myDB();
	foreach($sql as $line) {
		$res = simpleQuery($line,true,$db);
		print $line." -- SUCCESS<br>\n";
		flush();
		ob_flush();
	}
	print "<br><a href=\"index.php\">Back</a><br>";
}

?>
