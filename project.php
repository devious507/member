<?php

error_reporting(E_ALL);
ini_set("display_errors","On");

if(DIRECTORY_SEPARATOR == '/') {
	define("DATAFILE","data/membership.sqlite3");
	if($_SERVER['PHP_AUTH_USER'] != 'membership' || $_SERVER['PHP_AUTH_PW'] != 'mrpc2705') {
		doShowAuth();
	}
} else { 
	$storagelocation = exo_getglobalvariable('HEPubStorageLocation', '');
	$dbname = $storagelocation.'membership.sqlite3';
	define("DATAFILE",$dbname);
}

function doShowAuth() {
	header("WWW-Authenticate: Basic realm=\"Membership Management\"");
	header("HTTP/1.0 401 Unauthorized");
	echo "401 Unauthorized";
	exit();
}

function myDB($prefix='') {
	$dsn = "sqlite:".$prefix.DATAFILE;
	$db = new PDO($dsn);
	$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	$sql_members="CREATE TABLE IF NOT EXISTS members ( memberID int, NameLast varchar, NameFirst varchar, address varchar, City varchar, State varchar, Zip varchar, phone varchar, email varchar, comment varchar, membersince integer, expiresyear integer, wantsemail boolean, deceased boolean, lifemember boolean, pendingifemember boolean, remaininglifeamount numeric)";
	$sql_duespaid= "CREATE TABLE IF NOT EXISTS duesPaid ( memberID integer, year integer, datepaid varchar, checknumber integer, amount numeric, bankDepositNumber integer, comment varchar, paymenttype varchar)";
	$sql_membershipcards= "CREATE TABLE IF NOT EXISTS membershipCards ( memberID integer, expirationYear integer, cardNumber integer, note varchar, void boolean)";
	$sql_bankdeposit="CREATE TABLE IF NOT EXISTS bankDeposit (depositNumber interger, depositDate varchar)";
	$db->exec($sql_members);
	$db->exec($sql_duespaid);
	$db->exec($sql_membershipcards);
	$db->exec($sql_bankdeposit);
	return $db;
}

function mkEnvelopeButton($memberID,$db=NULL) {
	if(!isset($db)) {
		renderPage("<p>Must pass DB handle to mkEnvelopeButton</p>");
		exit();
	}
	$sql="SELECT printenvelope FROM members WHERE memberID={$memberID}";
	$res=simpleQuery($sql,true,$db);
	$row=$res->fetch();
	if($row[0] == 1) {
		return NULL;
	} else {
		$icon = "icons/envelope.png";
		$href = "<a href=\"envelope.php?memberid={$memberID}\"><img src=\"{$icon}\" border=\"0\"></a>";
		return $href;
	}
}
function mkEditQuery($row,$tbl) {
	$icon="icons/edit.png";
	foreach($row AS $k=>$v) {
		$wheres[]=$k."='".$v."'";
	}
	$string=urlencode(implode(" AND ",$wheres));
	$href="<a href=\"editTableLine.php?table={$tbl}&wheres={$string}\"><img src=\"{$icon}\" border=\"0\"></a>";
	return $href;
}
function mkDeleteQuery($row,$tbl) {
	$icon="icons/delete.png";
	foreach($row AS $k=>$v) {
		$wheres[]=$k."='".$v."'";
	}
	$string=urlencode(implode(" AND ",$wheres));
	$href="<a href=\"deleteFromTable.php?table={$tbl}&wheres={$string}\"><img src=\"{$icon}\" border=\"0\"></a>";
	return $href;
}

function getCurrentDepositNumber($db=NULL) {
	if($db == NULL) {
		print "DB Handle must be passed to getCurrentDepositNumber()";
		exit();
	}
	$sql="select max(depositNumber) FROM bankDeposit";
	$db=myDB();
	$res=simpleQuery($sql,true,$db);
	$row=$res->fetch(PDO::FETCH_ASSOC);
	if($row['max(depositNumber)'] != NULL) {
		return $row['max(depositNumber)'];
	} else {
		$date=('m/d/Y');
		$sql="INSERT INTO bankDeposit (depositNumber,depositDate) VALUES (1,'{$date}')";
		simpleQuery($sql,true,$db);
		return 1;
	}
}
function simpleQuery($sql,$execute=false,$db=NULL) {
	if(!isset($db)) {
		print "DB connection mussed be passed to simpleQuery()";
		exit();
	}
	if($execute) {
		try 
		{
			$res = $db->prepare($sql);
			$res->execute();
		} catch(Exception $e) {
			myDumper($e);
		}
		return $res;
	} else {
		print "<br>{$sql}<br>";
		exit();
	}
}
function getClubMemberNameByMemberID($memberid,$db=NULL) {
	if($db == NULL) {
		print "DB handle must be passed to getClubMemberNameByMamberID";
		exit();
	}
	$sql="SELECT namefirst,namelast FROM members WHERE memberid={$memberid}";
	$res=simpleQuery($sql,true,$db);
	$row = $res->fetch(PDO::FETCH_ASSOC);
	return $row['NameFirst']." ".$row['NameLast'];
}
function nextCardNumber($db=NULL) {
	if($db == NULL) {
		print "DB Handle must be passed to nextCardNumbeR()";
		exit();
	}
	$this_year = date('Y');
	$next_year = $this_year+1;
	$sql="SELECT max(cardNumber) FROM membershipcards WHERE expirationYear={$next_year}";
	$res=simpleQuery($sql,true,$db);
	$row=$res->fetch(PDO::FETCH_ASSOC);
	if($row['max(cardNumber)'] != NULL) {
		$last=$row['max(cardNumber)'];
	} else {
		$last=1000;
	}
	$next=$last+1;
	return $next;
}
function myPaymentTypes() {
	$rv = array(
		'annual'=>'Annual Membership',
		'guest'=>'Guest Membership',
		'merchandise'=>'Club Merchandise',
		'donation'=>'Donation',
		'life'=>'Life Membership',
		'gunshow_tables'=>'Gunshow Table',
		'gunshow_door'=>'Gunshow Door Receipts',
		'change'=>'Change Fund Surrendered',
		'other'=>'Other / Miscellaneous',
		'training'=>'Training / Class Payments'
	);
	return $rv;
}
function myDumper($v) {
	print "<pre>";
	var_dump($v);
	print "</pre>";
	exit();
}
function leftMenu($db=NULL) {
	if(isset($db)) {
		$sql="SELECT count(*) FROM members WHERE printenvelope=1";
		$res=simpleQuery($sql,true,$db);
		$row=$res->fetch();
		$envelopeCount = $row[0];
	}
	$rv ="<ul>\n";
	$rv.="\t<li><a href=\"index.php\">Membership File</a></li>\n";
	$rv.="\t<li><a href=\"reporting.php\">Reporting</a></li>\n";
	$rv.="\t<li><a href=\"utilities.php\">Utilities</a></li>\n";
	$rv.="</ul>\n";
	$rv.="<hr>\n";
	$rv.="<ul>\n";
	if(isset($envelopeCount)) {
		$rv.="\t<li><a href=\"getEnvelopePDF.php\">Print Envelopes ({$envelopeCount})</a></li>\n";
	} else {
		$rv.="\t<li><a href=\"getEnvelopePDF.php\">Print Envelopes</a></li>\n";
	}
	$rv.="</ul>\n";
	return $rv;
}
function generateEnvelopeBlock($memberInfo,$cardInfo,$cardInfo2=NULL) {
	if($cardInfo['expirationYear'] == NULL) {
		$line[]=sprintf("&nbsp;");
	} elseif($cardInfo2 != NULL) {
		$line[]=sprintf("%s%s-%s%s",
			$cardInfo['expirationYear'],
			$cardInfo['cardNumber'],
			$cardInfo2['expirationYear'],
			$cardInfo2['cardNumber']
		);
	} else {
		$line[]=sprintf("%s%s",$cardInfo['expirationYear'],$cardInfo['cardNumber']);
	}
	$line[]=sprintf("%s %s",$memberInfo['NameFirst'],$memberInfo['NameLast']);
	$line[]=sprintf("%s",$memberInfo['address']);
	$line[]=sprintf("%s, %s %s",$memberInfo['City'],$memberInfo['State'],$memberInfo['Zip']);
	$block=implode("<br>\n",$line);
	return $block;
}
function generateEnvelope($db=NULL,$keep=NULL) {
	if($db == NULL) {
		print "DB Handle must be passed to generateEnvelope()";
		exit();
	}
	$sql="SELECT memberID,namefirst,namelast,address,city,state,zip FROM members WHERE printenvelope=1";
	$res = simpleQuery($sql,true,$db);
	$data=$res->fetchAll(PDO::FETCH_ASSOC);
	$year=date('Y');
	$year++;
	foreach($data as $memberInfo) {
		$memberID=$memberInfo['memberID'];
		$sql="SELECT expirationyear,cardnumber FROM membershipcards WHERE memberid={$memberID} AND void=0 AND expirationyear={$year} ORDER BY expirationyear DESC,cardnumber DESC LIMIT 2";
		$res2 = simpleQuery($sql,true,$db);
		$cardInfo = $res2->fetch(PDO::FETCH_ASSOC);
	//	print "<pre>";var_dump($cardInfo); print "\n"; print "</pre>";
		try {
			$cardInfo2 = $res2->fetch(PDO::FETCH_ASSOC);
			$blocks[]=generateEnvelopeBlock($memberInfo,$cardInfo,$cardInfo2);
		} catch (Exception $e) {
			$blocks[]=generateEnvelopeBlock($memberInfo,$cardInfo);
		}
	}
	//print "<pre>"; var_dump($blocks); exit();
	$body="<html><head><title></title></head><body>";
	foreach($blocks as $dat) {
		$body.="<div style=\"margin-top: 150;margin-left: 400; font-size: large;\">{$dat}</div>";
		$body.="<div class=\"page-break\"></div>";
	}
	$body.="</body></html>";
	if($keep == "false") {
		print "clearing markers";
		$sql="UPDATE members SET printenvelope=0 WHERE printenvelope=1";
		$res=simpleQuery($sql,true,$db);
	} else {
		print "keeping markers";
	}
	header("Content-type: application/x-pdf");
	require_once("pdf/dompdf_config.inc.php");
	$dompdf = new DOMPDF();
	$dompdf->set_paper('COMMERCIAL #10 ENVELOPE');
	$dompdf->load_html($body);
	$dompdf->render();
	$dompdf->stream('envelope.pdf');
	exit();
}
function generateEnvelopeOld($memberID=3147,$db=NULL) {
	if($db == NULL) {
		print "DB Handle must be passed to generateEnvelope()";
		exit();
	}
	$sql="SELECT namefirst,namelast,address,city,state,zip FROM members WHERE memberid={$memberID}";
	$res = simpleQuery($sql,true,$db);
	$memberInfo=$res->fetch(PDO::FETCH_ASSOC);
	$sql="SELECT expirationyear,cardnumber FROM membershipcards WHERE memberid={$memberID} AND void=0 ORDER BY expirationyear DESC,cardnumber DESC LIMIT 1";
	$res = simpleQuery($sql,true,$db);
	$cardInfo = $res->fetch(PDO::FETCH_ASSOC);
	$line[]=sprintf("%s%s",$cardInfo['expirationYear'],$cardInfo['cardNumber']);
	$line[]=sprintf("%s %s",$memberInfo['NameFirst'],$memberInfo['NameLast']);
	$line[]=sprintf("%s",$memberInfo['address']);
	$line[]=sprintf("%s, %s %s",$memberInfo['City'],$memberInfo['State'],$memberInfo['Zip']);
	$block=implode("<br>\n",$line);
	$body="<html><head><title></title></head><body><div style=\"margin-top: 150;margin-left: 400; font-size: large\">{$block}</div></body></html>";
	header("Content-type: application/x-pdf");
	require_once("pdf/dompdf_config.inc.php");
	$dompdf = new DOMPDF();
	$dompdf->set_paper('COMMERCIAL #10 ENVELOPE');
	$dompdf->load_html($body);
	$dompdf->render();
	$dompdf->stream('envelope.pdf');
}
function getPDF($body,$filename="mypdf.pdf",$menu=false,$orientation='portrait') {
	require_once("pdf/dompdf_config.inc.php");
	$html = generatePage($body,$menu);
	$dompdf = new DOMPDF();
	$dompdf->load_html($html);
	$dompdf->set_paper('letter',$orientation);
	$dompdf->render();
	$dompdf->stream($filename);
	exit();
}
function renderPage($body,$menu=true,$title="Membership Management",$db=NULL) {
	print generatePage($body,$menu,$title,$db);
	exit();
}
function generatePage($body,$menu=true,$title,$db=NULL) {
	$leftMenu = leftMenu($db);
	$rv ="<html>\n";
	$rv.="<head>\n";
	$rv.="<title>{$title}</title>\n";
	$rv.="<meta http-equiv=\"Content-Type\" content=\"text/html; charset=iso-8859-1\">\n";
	$rv.="<link href=\"style.css\" rel=\"stylesheet\" type=\"text/css\">\n";
	print "<script>\n";
	print "document.onkeydown = function(evt) {\n";
	print "\tevt = evt || window.event;\n";
	print "\tif (evt.ctrlKey && evt.keyCode == 70) {\n";
	print "\t\twindow.location.href = 'index.php';\n";
	print "\t}\n";
	print "};\n";
	print "window.onload = function() {\n";
	print "\tdocument.getElementById(\"focusBox\").focus();\n";
	print "};\n";
	print "</script>\n";
	$rv.="</head>\n";
	$rv.="<body>\n";
	if($menu) {
		$rv.="<div class=\"leftmenu\">{$leftMenu}</div>\n";
		$rv.="<div class=\"mainScreen\">{$body}</div>\n";
	} else {
		$rv.="<div>{$body}</div>\n";
	}
	$rv.="</body>\n";
	$rv.="</html>\n";
	return $rv;
}

function getMemberFlags() {
	$flags=array('wantsemail'=>'Wants Email',
		'deceased'=>'Deceased',
		'lifemember'=>'Life Member',
		'boardmember'=>'Board Member',
		'rso'=>'Credentialed RSO',
		'active_rso'=>'Active RSO',
		'welder'=>'Welder',
		'plumber'=>'Plumber',
		'electrician'=>'Electrician',
		'carpenter'=>'Carpenter',
		'painter'=>'Painter',
		'light_labor'=>'Light Labor',
		'heavy_labor'=>'Heavy Labor'
	);
	return $flags;

}
?>
