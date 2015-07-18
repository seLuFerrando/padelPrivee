<?php
$conexion = mysql_connect("mysql.2tr.es", "u418879115_padel", "Dientax2141");
mysql_select_db("u418879115_padel", $conexion);
 
$queTareas = "SELECT groups._id, groups.group, groups.image, groups.status AS idStatus,
 groups.dateStart, groups.dateEnd, status.status FROM groups 
 INNER JOIN status ON groups.status = status._id 
 WHERE visibility=1 ORDER BY groups.status ASC";
$resTareas = mysql_query($queTareas, $conexion) or die(mysql_error());
$totTareas= mysql_num_rows($resTareas);
 
$arr = array();
 
if ($totTareas> 0) {
   while ($rowTareas = mysql_fetch_array($resTareas)) {
      $arr[] = $rowTareas;
   }
}
 
echo json_encode($arr);
 
?>