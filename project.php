<?php

error_reporting(E_ALL);
ini_set("display_errors","On");
define("development_mode",0);

session_name("membershipApp");
session_start();

// Work Around for Older Versions of PHP < 5.6.0

if(!function_exists('hash_equals')) {
	function hash_equals($str1, $str2) {
		if(strlen($str1) != strlen($str2)) {
			return false;
		} else {
			$res = $str1 ^ $str2;
			$ret = 0;
			for($i = strlen($res) - 1; $i >= 0; $i--) $ret |= ord($res[$i]);
			return !$ret;
		}
	}
}

if(DIRECTORY_SEPARATOR == '/') {
	define("DATAFILE","data/membership.sqlite3");
	if(isset($_POST['username']) && isset($_POST['password'])) {
		$_SESSION['username']=$_POST['username'];
		$_SESSION['password']=$_POST['password'];
		header("Location: index.php");
		exit();
	}
	if(isset($_SESSION['username']) && isset($_SESSION['password'])) {
		$db=myDB();
		$sql=sprintf("SELECT password,write FROM users WHERE username='%s'",$_SESSION['username']);
		$res = simpleQuery($sql,true,$db);
		$data = $res->fetchAll(PDO::FETCH_ASSOC);
		if(isset($data[0])) {
			if(hash_equals($data[0]['password'], crypt($_SESSION['password'],$data[0]['password']))) {
				$_SESSION['write']=$data[0]['write'];
			} else {
				doShowAuth();
				exit();
			}
		}
		if(count($data) == 1) {
			$_SESSION['write']=$data[0]['write'];
		} else {
			doShowAuth();
			exit();
		}
	} else {
		doShowAuth();
	}
} else { 
	$body="<p>Windows Operating system Unsupported at this time</p>";
	renderPage($body,false);
	// Windows Storage Location Stuff, unused currently
	//$storagelocation = exo_getglobalvariable('HEPubStorageLocation', '');
	//$dbname = $storagelocation.'membership.sqlite3';
	//define("DATAFILE",$dbname);
}

function checkIsWriter() {
	if( (isset($_SESSION['write'])) && ($_SESSION['write'] == 0) ) {
		header("Location: index.php");
		exit();
	}
}
function doShowAuth() {
	$body ="<form method=\"post\" action=\"index.php\">";
	$body.="<table cellpadding=\"5\" cellspacing=\"0\" border=\"1\">";
	$body.="<tr><td>Login</td><td><input type=\"text\" size=\"10\" name=\"username\"></td></tr>";
	$body.="<tr><td>Password</td><td><input type=\"password\" size=\"10\" name=\"password\"></td></tr>";
	$body.="<tr><td colspan=\"2\"><input type=\"submit\" value=\"Login\"></td></tr>";
	$body.="</table>";
	$body.="</form>";
	renderPage($body,false);
	exit();
}

function myDB($prefix='') {
	$dsn = "sqlite:".$prefix.DATAFILE;
	$db = new PDO($dsn);
	$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

	// Original System Tables
	$sql[]="CREATE TABLE IF NOT EXISTS bankDeposit (depositNumber interger, depositDate varchar)";
	$sql[]="CREATE TABLE IF NOT EXISTS duesPaid ( memberID integer, year integer, datepaid varchar, checknumber integer, amount numeric, bankDepositNumber integer, comment varchar, paymenttype varchar)";
	$sql[]="CREATE TABLE IF NOT EXISTS [members] ([memberID] int  UNIQUE NOT NULL,[NameLast] varchar  NULL,[NameFirst] varchar  NULL,[address] varchar  NULL,[City] varchar  NULL,[State] varchar  NULL,[Zip] varchar  NULL,[phone] varchar  NULL,[email] varchar  NULL,[comment] varchar  NULL,[membersince] integer  NULL,[expiresyear] integer  NULL,[wantsemail] boolean  NULL,[deceased] boolean  NULL,[lifemember] boolean  NULL,[pendingifemember] boolean  NULL,[remaininglifeamount] numeric  NULL,[printenvelope] bOOLEAN DEFAULT 'false' NOT NULL, boardmember boolean default 0, rso boolean default 0, active_rso boolean default 0, welder boolean default 0, carpenter boolean default 0, electrician boolean default 0, plumber boolean default 0, painter boolean default 0, light_labor boolean default 0, heavy_labor boolean default 0, lat numeric, lon numeric)";
	$sql[]="CREATE TABLE IF NOT EXISTS membershipCards ( memberID integer, expirationYear integer, cardNumber integer, note varchar, void boolean)";

	// 6/8/2016 -- Add payment_types table
	$sql[]="CREATE TABLE IF NOT EXISTS payment_types (paymentTypeID integer PRIMARY KEY, paymentType varchar)";
	$sql[]="INSERT OR REPLACE INTO payment_types VALUES (-1,'Credit Card')";
	$sql[]="INSERT OR REPLACE INTO payment_types VALUES (-2,'Donated Gift Certificate')";
	$sql[]="INSERT OR REPLACE INTO payment_types VALUES (-3,'Gift Certificate')";

	// 6/8/2016 -- Create users table
	$sql[]="CREATE TABLE IF NOT EXISTS users (username varchar PRIMARY KEY, password varchar, write bool)";

	// Add Spouse Fields to members table
	$alter[]="alter table members add column spouse_first varchar";
	$alter[]="alter table members add column spouse_last varchar";
	$alter[]="alter table members add column spouse_phone varchar";
	$alter[]="alter table members add column spouse_email varchar";
	
	foreach($sql as $statement) {
		$db->exec($statement);
	}
	foreach($alter as $statement) {
		try {
			$db->exec($statement);
		} catch (Exception $e) {
			// Not doing anything with this
		}
	}

	$sql="SELECT count(*) as c FROM users";
	$res = simpleQuery($sql,true,$db);
	$data = $res->fetchAll(PDO::FETCH_ASSOC);
	if($data[0]['c'] == 0) {
		$pass=crypt('admin');
		$sql="INSERT INTO users VALUES ('admin','{$pass}',1)";
		$res=$db->exec($sql);
	}
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
function nextCardNumber($db=NULL,$year=NULL) {
	if( ($db == NULL) || ($year==NULL) ) {
		print "DB Handle and year must be passed to nextCardNumbeR()";
		exit();
	}
	$next_year=$year;
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
		'idpa'=>'IDPA Match Fees',
		'life'=>'Life Membership',
		'gift_cert'=>'Gift Certificate',
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

		// Unique Members
		$year=date('Y')+1;
		$sql="select memberid,expirationyear FROM membershipcards WHERE expirationyear={$year} GROUP BY memberid,expirationyear";
		$res = simpleQuery($sql,true,$db);
		$data=$res->fetchAll(PDO::FETCH_ASSOC);
		$memberCount = count($data);
	}
	if(isset($_SESSION['username'])) {
		$rv="<b>".ucfirst(strtolower($_SESSION['username']))."</b><hr>";
	} else {
		$rv='';
	}
	if(isset($memberCount)) {
		$rv.="\t<b>&nbsp;&nbsp;{$memberCount} Members</b><hr>\n";
	}
	if(isset($_SESSION['write'])) {
		if($_SESSION['write'] == 0) {
			$rv.="\t<b>READ ONLY<br>SESSION</b><hr>\n";
		}
	}
	$rv.="<ul>\n";
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
	$rv.="<hr>\n";
	$rv.="\t<li><a href=\"utility_logout.php\">Logout</a></li>\n";
	$rv.="<hr>\n";
	if(development_mode) {
		$rv.="<hr>\n";
		$rv.="\t<li><a href=\"utility_dumper.php\">Test Dump Vars</a></li>\n";
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
	foreach($data as $memberInfo) {
		$memberID=$memberInfo['memberID'];
		$sql="SELECT max(expirationyear) as a FROM membershipcards WHERE memberid={$memberID} AND void=0";
		$res_temp = simpleQuery($sql,true,$db);
		$temp_data=$res_temp->fetch(PDO::FETCH_ASSOC);
		$year=$temp_data['a'];
		unset($res_temp);
		unset($temp_data);

		$sql="SELECT expirationyear,cardnumber FROM membershipcards WHERE memberid={$memberID} AND void=0 AND expirationyear={$year} ORDER BY expirationyear DESC,cardnumber ASC LIMIT 2";
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
