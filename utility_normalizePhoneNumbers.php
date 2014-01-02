<?php

require_once("project.php");

$db=myDB();
$sql="select memberid,phone from members ORDER BY memberid";
$res = simpleQuery($sql,true,$db);
$data = $res->fetchAll(PDO::FETCH_ASSOC);

foreach($data as $row) {
	$id = $row['memberID'];
	$phone = $row['phone'];
	$phone_temp = preg_replace("/[^0-9]/", '', $phone);
	if(strlen($phone_temp) == 10) {
		$acode = substr($phone_temp,0,3);
		$prefix= substr($phone_temp,3,3);
		$nxx   = substr($phone_temp,6,4);
		$phone = $acode."-".$prefix."-".$nxx;
		$sql="UPDATE members SET phone='{$phone}' WHERE memberid={$id}";
		print $sql."<br>\n";
		flush();
		ob_flush();
		simpleQuery($sql,true,$db);
	}
}
print "<a href=\"index.php\">Back to Index</a>";
?>
