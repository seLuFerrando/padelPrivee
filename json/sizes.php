<?php
require_once('../connections/padelprivee.php');
  if(isset($_GET['ID'])){
    $id=$_GET['ID'];
  }else{
    $id=0;
  }

    $querySizes = "SELECT sizes_article.quantity, sizes._id, sizes.size 
      FROM `sizes_article` INNER JOIN `sizes` 
      ON `sizes`.`_id` = `sizes_article`.`size` 
      WHERE `sizes_article`.`article` = $id";
    $rsSizes = mysql_query($querySizes, $padelprivee) or die(mysql_error());
    
	$totSizes= mysql_num_rows($rsSizes);
 
$arr = array();
 
if ($totSizes> 0) {
   while ($row_rsSizes = mysql_fetch_array($rsSizes)) {
      $arr[] = $row_rsSizes;
   }
} 
echo json_encode($arr);
mysql_close($padelprivee); 
?>