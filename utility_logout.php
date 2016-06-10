<?php

require_once("project.php");

$_SESSION = array();
$params = session_get_cookie_params();
setcookie(session_name(),'',time()-3600,'/');
unset($_COOKIE[session_name()]);
session_destroy();
header("Location: index.php");

?>
