<?php
# FileName="Connection_php_mysql.htm"
# Type="MYSQL"
# HTTP="true"
$hostname_padelprivee = "localhost";
$database_padelprivee = "u418879115_padel";
$username_padelprivee = "root";
$password_padelprivee = "Dientax2141";

//$hostname_padelprivee = "mysql.2tr.es";
//$database_padelprivee = "u418879115_padel";
//$username_padelprivee = "u418879115_padel";
//$password_padelprivee = "Dientax2141";
$padelprivee = mysql_connect($hostname_padelprivee, $username_padelprivee, $password_padelprivee) or trigger_error(mysql_error(),E_USER_ERROR); 
mysql_select_db($database_padelprivee, $padelprivee);
?>