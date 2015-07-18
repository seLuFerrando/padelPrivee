<?php
require_once('../connections/padelprivee.php');
  if(isset($_GET['ID'])){
    $id=$_GET['ID'];
  }else{
    $id=0;
  }

    $queryArticles = "SELECT * FROM `articles` WHERE `_id` = $id";
    $rsArticles = mysql_query($queryArticles, $padelprivee) or die(mysql_error());
 	$totArticles= mysql_num_rows($rsArticles);
$arr = array();
 
if ($totArticles> 0) {
   while ($row_rsArticles = mysql_fetch_array($rsArticles)) {
      $arr[] = $row_rsArticles;
   }
} 
echo json_encode($arr);
mysql_close($padelprivee); 
?>