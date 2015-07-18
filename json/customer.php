<?php
require_once('../connections/padelprivee.php');
  if(isset($_GET['ID'])){
    $id=$_GET['ID'];
  }else{
    $id='0';
  }

    $queryCustomers = "SELECT * FROM `customers` WHERE `login` = '" . $id . "'";
    $rsCustomers = mysql_query($queryCustomers, $padelprivee) or die(mysql_error());
 	$totCustomers= mysql_num_rows($rsCustomers);
	$arr = array();
 
	if ($totCustomers> 0) {
	   while ($row_rsCustomers = mysql_fetch_array($rsCustomers)) {
    	  $arr[] = $row_rsCustomers;
   		}
	} 
echo json_encode($arr);
mysql_close($padelprivee); 
?>