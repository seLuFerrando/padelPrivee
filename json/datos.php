<?php
$conexion = mysql_connect("mysql.2tr.es", "u418879115_padel", "Dientax2141");
mysql_select_db("u418879115_padel", $conexion);
 
$queTareas = "SELECT * FROM articles";
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