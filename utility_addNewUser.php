<?php

require_once("project.php");
checkIsWriter();


$body ="<form method=\"post\" action=\"utility_addNewUserAction.php\">";
$body.="<table cellpadding=\"3\" border=\"0\" cellspacing=\"0\">";
$body.="<tr><td>New Username</td><td><input type=\"text\" name=\"username\"></td></tr>";
$body.="<tr><td colspan=\"2\"><input type=\"submit\" value=\"Add User\"></td></tr>";
$body.="</table></form>";

renderPage($body);

?>
