<?php

require_once("project.php");

$db = myDB();

if(isset($_POST['search'])) {
	unset($_POST['search']);
	foreach($_POST as $k=>$v) {
		$v=preg_replace("/\*/",'%',$v);
		$v=preg_replace("/\?/",'%',$v);
		if($v != '') {
			if(preg_match("/%/",$v)) {
				$wheres[] = "{$k} LIKE '{$v}'";
			} elseif($v == 'NULL') {
				$wheres[] = "({$k} IS NULL OR {$k} = '')";
			} else {
				$wheres[] = "{$k} = '{$v}'";
			}
		}
	}
	$sql="SELECT * FROM members WHERE ".implode(" AND ",$wheres)." ORDER BY NameLast, NameFirst;";
	$result = simpleQuery($sql,true,$db);
	$data = $result->fetchAll(PDO::FETCH_ASSOC);
	if(count($data) == 0) {
		$body="No Results Found <!--{$sql}--><br>\n";
		$body.="<p><a href=\"newMember.php\">New Member Entry</a></p>";
		renderPage($body,true,'Member Management',$db);
		exit();
	} elseif(count($data) == 1) {
		$memberID=$data[0]['memberID'];
		header("Location: memberRecord.php?memberID={$memberID}");
		exit();
	} else {
		$body ="<table cellpadding=\"5\" cellspacing=\"0\" border=\"1\">\n";
		foreach($data as $row) {
			if($row['NameFirst'] == '' && $row['NameLast'] == '') {
				$href = "<a href=\"memberRecord.php?memberID={$row['memberID']}\">({$row['memberID']})</a>";
			} else {
				$href = "<a href=\"memberRecord.php?memberID={$row['memberID']}\">{$row['NameLast']}, {$row['NameFirst']}</a>";
			}
			$body.="<tr><td>{$href}</td><td>{$row['address']}</td><td>{$row['phone']}</td><td>{$row['email']}</td></tr>\n";
		}
		$body.="</table>\n";
		renderPage($body,true,'Member Management',$db);
	}
	exit();
}
renderPage(mySearch(),true,'Member Management',$db);

function mySearch() {
	$body ="<form method=\"post\" action=\"index.php\">";
	$body.="<table cellpadding=\"5\" cellspacing=\"0\" border=\"0\">";
	$body.="<tr><th colspan=\"4\">Member Search</th></tr>\n";
	$body.="<tr><td colspan=\"1\">Name (First, Last)</td><td><input id=\"focusBox\" type=\"text\" size=\"15\" name=\"NameFirst\"></td><td colspan=\"2\"><input type=\"text\" size=\"20\" name=\"NameLast\"></td></tr>\n";
	$body.="<tr><td colspan=\"1\">Address</td><td colspan=\"3\"><input type=\"text\" size=\"43\" name=\"address\"</td></tr>\n";
	$body.="<tr><td>City, State, Zip</td><td><input type=\"text\" name=\"City\" size=\"15\"></td><td><input type=\"text\" Size=\"4\" name=\"State\"</td><td><input type=\"text\" size=\"8\" name=\"Zip\"></td></tr>\n";
	$body.="<tr><td>Email</td><td colspan=\"3\"><input type=\"text\" size=\"43\" name=\"email\"></td></tr>\n";
	$body.="<tr><td colspan=\"4\"><input type=\"submit\" name=\"search\" value=\"Search\"></td></tr>\n";
	$body.="</table>\n</form>\n";
	$body.="<p><a href=\"newMember.php\">New Member Entry</a></p>";
	return $body;
}
?>
