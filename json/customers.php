<?php
$conexion = mysql_connect("mysql.2tr.es", "u418879115_padel", "Dientax2141");
mysql_select_db("u418879115_padel", $conexion);
 
	$type = $_POST['type']; 
	$name = $_POST['name'];
	$secondname = $_POST['secondname'];
	$postalCode = $_POST['postalCode'];
	$email = $_POST['email'];
	$login = $_POST['email'];
	$password = $_POST['password'];
	$date = date("Y-m-d");
	if($type == 'first'){
		$address = '0';
		$city  = '0';
		$nif = '0';
		$phone = '0';
	}else{
		$address = $_POST['address'];
		$city  = $_POST['city'];
		$nif = $_POST['nif'];
		$phone = $_POST['phone'];
	}

$queTareas = "INSERT INTO customers (name, secondname, address, postalCode, city, nif, login, password, email, phone, date) 
VALUES ('".$name."','".$secondname."','".$address."','".$postalCode."','".$city."','".$nif."','".$login."','".$password."','".$email."','".$phone."', now())";

mysql_query($queTareas, $conexion) or die(mysql_error());
echo $queTareas;
 
echo $name;
 
?>